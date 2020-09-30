<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_File_Export') ) {

	class FPD_File_Export {

		public static function svg_to_pdf( $args = array() ) {

			$defaults = array(
				'name' => 'default',
				'svg_data' => array(),
				'width' => 200,
				'height' => 200,
				'summary_json' => null,
				'image_data' => array(),
			);

			$args = wp_parse_args( $args, $defaults );

			if( !class_exists('TCPDF') ) {
				require_once(FPD_PLUGIN_ADMIN_DIR.'/vendors/libs/tcpdf/tcpdf.php');
			}

			if( !class_exists('SVGDocument') )
				require_once(FPD_PLUGIN_ADMIN_DIR.'/vendors/libs/svglib/svglib.php');

			//register_shutdown_function( array( &$this, 'get_server_errors' ) );

			$views_data = $args['svg_data'];
			$views_data = isset($args['image_data']) && sizeof($args['image_data']) > 0 ? $args['image_data'] : $views_data;
			//if memory limit is too small, a fatal php error will thrown here


			//create fancy product orders directory
			if( !file_exists(FPD_ORDER_DIR) )
				wp_mkdir_p(FPD_ORDER_DIR);

			//create pdf dir
			$pdf_dir = FPD_ORDER_DIR.'pdfs/';
			if( !file_exists($pdf_dir) )
				wp_mkdir_p($pdf_dir);

			$pdf_path = $pdf_dir . $args['name'] . '.pdf';

			$pdf = new TCPDF('L', 'mm', 'A4');

			// set document information
			$pdf->SetCreator( get_site_url() );
			$pdf->SetTitle( $args['name'] );
			$pdf->SetFont('dejavusans');

			// remove default header/footer
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			$pdf->SetMargins(0, 0, 0);
			$pdf->SetAutoPageBreak(true, 0);
			$pdf->setJPEGQuality(100);

			foreach($views_data as $view_data) {

				$page_width = 210;
				$page_height = 297;

				if( isset($view_data['output']) && isset($view_data['output']['width']) && isset($view_data['output']['height']) ) {

					$page_width = intval($view_data['output']['width']);
					$page_height = intval($view_data['output']['height']);

				}

				$orientation = $page_width > $page_height ? 'L' : 'P';

				$pdf->AddPage($orientation, array($page_width, $page_height));

				if (isset($view_data['image'])) { //bitmap data

					$data_str = base64_decode(substr($view_data['image'], strpos($view_data['image'], ",") + 1));
					$pdf->Image('@'.$data_str,0, 0, $page_width, $page_height);

				}
				else {
					$pdf->ImageSVG('@'.$view_data['svg'], 0, 0, $page_width, $page_height);
				}

			}

			if( is_array($args['summary_json']) ) {

				$summary_json = $args['summary_json'];

				$pdf->SetLeftMargin(10);
				$pdf->AddPage('L', array(200, 150));

				//parameter that will be displayed
				$includedParameters = array('fill', 'opacity', 'top', 'left', 'scaleX', 'scaleY', 'angle', 'fontFamily', 'fontSize', 'fontStyle', 'fontWeight', 'stroke', 'strokeWidth','price', 'sku');

				$html = '';
				//if only the current view is sent, put it into new array
				$views = isset($summary_json['title']) ? array($summary_json) : $summary_json;

				//loop all views
				foreach($views as $view) {

					$html .= '<h3>'.$view['title'].'</h3><table border="1" cellspacing="3" cellpadding="4">
					<thead>
						<tr>
							<th><strong>Element</strong></th>
							<th colspan="4"><strong>Properties</strong></th>
							<th width="60px"><strong>Type</strong></th>
						</tr>
					</thead>
					<tbody>';

					$viewElements = $view['elements'];

					//loop all view elements
					foreach($viewElements as $viewElement) {

						$elementParams = $viewElement['parameters'];
						$element_html = '<div>Content: '.(isset($elementParams['text']) ? $elementParams['text'] : $viewElement['source']).'</div>';

						foreach($includedParameters as $param) {

							if( isset($elementParams[$param]) ) {

								$value = is_array($elementParams[$param]) ? implode(' | ', $elementParams[$param]) : $elementParams[$param];
								if( $param === 'fill' )
									$value = strtoupper( fpd_get_hex_name($value) ) . ' ' . $value; //display hex name

								if( !empty($value) )
									$element_html .= '<i>'. strtoupper($param).':</i> '.$value.', ';

							}
						}

						$element_html = substr( $element_html, 0, -2 );

						$html .= '<tr><td>'.$viewElement['title'].'</td><td colspan="4">'.$element_html.'</td><td width="60px">'.$viewElement['type'].'</td></tr>';
					}

					$html .= '</tbody></table>';

				}

				$pdf->writeHTML($html, true, false, true, false, '');
				$pdf->lastPage();

			}

			$pdf->Output($pdf_path, 'F');

			return content_url( substr($pdf_path, strrpos($pdf_path, '/fancy_products_orders')) );

		}

	}
}

?>