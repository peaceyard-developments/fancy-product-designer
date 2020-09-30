<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Labels_Order_Viewer') ) {

	class FPD_Labels_Order_Viewer {

		public static function get_labels() {

			return apply_filters('fpd_admin_labels_order_viewer',
				array (
					'orderType' => __( 'Order Type', 'radykal' ),
					'woocommerce' => __( 'WooCommerce', 'radykal' ),
					'shortcode' => __( 'Shortcode', 'radykal' ),
					'gravityForm' => __( 'Gravity Form', 'radykal' ),
					'noElementSelected' => __( 'No element selected!', 'radykal' ),
					'printReadyExportTitle' => __( 'Print-Ready Export Features', 'radykal' ),
					'printReadyExportInfo' => __( '<ul class=\'list\'><li>Define a printing area with boxes.</li><li>Define any size for the exported printing area (A1, A2... or a custom size in MM).</li><li>Fonts are embbeded.</li><li>Exclude layers from export.</li><li>Export to JPEG or PNG in any DPI.</li></ul> For more information how to set up the products for the print-ready export, please <a href=\'https://support.fancyproductdesigner.com/support/solutions/articles/13000054514-exporting-a-layered-pdf-to-any-format\' target=\'_blank\'>visit the help article in our support center!</a>', 'radykal' ),
					'basicExportTitle' => __( 'Basic Export Features', 'radykal' ),
					'basicExportInfo' => __( '<ul class=\'list\'><li>Rescale exported format.</li><li>Exclude layers from export.</li></ul>', 'radykal' ),
					'variation' => __( 'Variation', 'radykal' ),
					'quantity' => __( 'Quantity', 'radykal' ),
					'orderViewer' => __( 'Order Viewer', 'radykal' ),
					'fpdProduct' => __( 'FPD Product', 'radykal' ),
					'fpdProductInfo' => __( 'This is the ordered product customized by the customer.', 'radykal' ),
					'manageLayers' => __( 'Manage Layers', 'radykal' ),
					'ruler' => __( 'Ruler', 'radykal' ),
					'undo' => __( 'Undo', 'radykal' ),
					'redo' => __( 'Redo', 'radykal' ),
					'namesNumbers' => __( 'Names & Numbers', 'radykal' ),
					'showRealCanvasSize' => __( 'Show Real Canvas Size', 'radykal' ),
					'printReadyExport' => __( 'Print-Ready Export', 'radykal' ),
					'basicExport' => __( 'Basic Export', 'radykal' ),
					'singleElement' => __( 'Single Element', 'radykal' ),
					'depositphotos' => __( 'Depositphotos', 'radykal' ),
					'zipPdfFont' => __( 'ZIP: PDF + Font Files', 'radykal' ),
					'zipPdfCustomImages' => __( 'ZIP: PDF + Custom Images', 'radykal' ),
					'views' => __( 'View(s)', 'radykal' ),
					'from' => __( 'From', 'radykal' ),
					'to' => __( 'To', 'radykal' ),
					'pdfElementsPageInfo' => __( 'Add an additional page with information about all elements.', 'radykal' ),
					'dpi' => __( 'DPI', 'radykal' ),
					'download' => __( 'Download', 'radykal' ),
					'includedPrintReadyExport' => __( 'What is included in the print-ready export?', 'radykal' ),
					'outputFormat' => __( 'Output Format', 'radykal' ),
					'scaleFactor' => __( 'Scale Factor', 'radykal' ),
					'includedBasicExport' => __( 'What is included in the basic export?', 'radykal' ),
					'selectedElement' => __( 'Selected Element', 'radykal' ),
					'addedByCustomer' => __( 'Added By Customer', 'radykal' ),
					'savedServerImages' => __( 'Saved Images On Server', 'radykal' ),
					'export' => __( 'Export', 'radykal' ),
					'imageFormat' => __( 'Image Format', 'radykal' ),
					'padding' => __( 'Padding', 'radykal' ),
					'elementExportOriginSize' => __( 'Use origin size, that will set the scaling to 1, when exporting the image.', 'radykal' ),
					'elementExportNoBoundingBox' => __( 'Export without bounding box clipping if element has one.', 'radykal' ),
					'usedColors' => __( 'Used Colors', 'radykal' ),
					'bulkAddVariations' => __( 'Bulk-Add Variations', 'radykal' ),
					'saveOrder' => __( 'Save Order Changes', 'radykal' ),
					'createProductFromOrder' => __( 'Create Product from Order', 'radykal' ),
					'enterProductTitle' => __( 'Enter a product title', 'radykal' ),
					'noEmpyProductTitle' => __( 'The product title can not be empty!', 'radykal' ),
					'downloadReplace' => __( 'Download & Replace', 'radykal' ),
					'dpRequestImage' => __( 'Requesting image from depositingphotos.com', 'radykal' ),
					'downloadImage' => __( 'Downloading image to local server', 'radykal' ),
					'editPrintingBox' => __( 'Edit Printing Box', 'radykal' ),

					//container
					'updatingOrder' => __( 'Updating Order...', 'radykal' ),
					'deletingOrder' => __( 'Deleting Order...', 'radykal' ),
					'deleteOrder' => __( 'Delete Order', 'radykal' ),
					'deleteOrderText' => __( 'Are you sure to delete the order?', 'radykal' ),
					'creatingFile' => __( 'Creating File...', 'radykal' ),
					'saveOrder' => __( 'Save Order Changes', 'radykal' ),
					'createProductFromOrder' => __( 'Create Product from Order', 'radykal' ),
					'creatingProduct' => __( 'Creating Product...', 'radykal' ),
					//shortcode orders
					'customer' => __( 'Customer', 'radykal' ),
					'date' => __( 'Date', 'radykal' ),
					'loadingOrders' => __( 'Loading Orders...', 'radykal' ),
					'loadingOrder' => __( 'Loading Order...', 'radykal' ),
					'noOrders' => __( 'You have not received any order yet!', 'radykal' ),
				)
			);

		}
	}

}

?>