<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Admin_Ajax')) {

	class FPD_Admin_Ajax {

		public function __construct() {

			// - general
			add_action( 'wp_ajax_fpd_dismiss_notification', array( &$this, 'dismiss_notification' ) );

			// - products
			add_action( 'wp_ajax_fpd_get_products', array( &$this, 'get_products' ) );
			add_action( 'wp_ajax_fpd_create_product', array( &$this, 'create_product' ) );
			add_action( 'wp_ajax_fpd_update_product', array( &$this, 'update_product' ) );
			add_action( 'wp_ajax_fpd_delete_product', array( &$this, 'delete_product' ) );
			add_action( 'wp_ajax_fpd_export', array( &$this, 'export_product' ) );

			// - product categories
			add_action( 'wp_ajax_fpd_get_product_categories', array( &$this, 'get_product_categories' ) );
			add_action( 'wp_ajax_fpd_create_product_category', array( &$this, 'create_product_category' ) );
			add_action( 'wp_ajax_fpd_update_product_category', array( &$this, 'update_product_category' ) );
			add_action( 'wp_ajax_fpd_delete_product_category', array( &$this, 'delete_product_category' ) );

			// - templates
			add_action( 'wp_ajax_fpd_get_templates', array( &$this, 'get_templates' ) );
			add_action( 'wp_ajax_fpd_create_template', array( &$this, 'create_template' ) );
			add_action( 'wp_ajax_fpd_delete_template', array( &$this, 'delete_template' ) );

			// - views
			add_action( 'wp_ajax_fpd_get_view', array( &$this, 'get_view' ) );
			add_action( 'wp_ajax_fpd_update_view', array( &$this, 'update_view' ) );
			add_action( 'wp_ajax_fpd_delete_view', array( &$this, 'delete_view' ) );

			// - options
			add_action( 'wp_ajax_fpd_get_options', array( &$this, 'get_options' ) );
			add_action( 'wp_ajax_fpd_get_options_group', array( &$this, 'get_options_group' ) );
			add_action( 'wp_ajax_fpd_update_options', array( &$this, 'update_options' ) );

			// - UI composer
			add_action( 'wp_ajax_fpd_get_colors_css', array( &$this, 'get_colors_css' ) );
			add_action( 'wp_ajax_fpd_get_ui_layouts', array( &$this, 'get_ui_layouts' ) );
			add_action( 'wp_ajax_fpd_get_ui_layout', array( &$this, 'get_ui_layout' ) );
			add_action( 'wp_ajax_fpd_create_ui_layout', array( &$this, 'create_ui_layout' ) );
			add_action( 'wp_ajax_fpd_update_ui_layout', array( &$this, 'update_ui_layout' ) );
			add_action( 'wp_ajax_fpd_delete_ui_layout', array( &$this, 'delete_ui_layout' ) );

			// - design categories
			add_action( 'wp_ajax_fpd_get_design_categories', array( &$this, 'get_design_categories' ) );
			add_action( 'wp_ajax_fpd_get_design_category', array( &$this, 'get_design_category' ) );
			add_action( 'wp_ajax_fpd_create_design_category', array( &$this, 'create_design_category' ) );
			add_action( 'wp_ajax_fpd_update_design_category', array( &$this, 'update_design_category' ) );
			add_action( 'wp_ajax_fpd_delete_design_category', array( &$this, 'delete_design_category' ) );

			// - orders
			add_action( 'wp_ajax_fpd_get_orders', array( &$this, 'get_orders' ) );
			add_action( 'wp_ajax_fpd_get_order', array( &$this, 'get_order' ) );
			add_action( 'wp_ajax_fpd_update_order', array( &$this, 'update_order' ) );
			add_action( 'wp_ajax_fpd_delete_order', array( &$this, 'delete_order' ) );
			add_action( 'wp_ajax_fpd_create_file_export', array( &$this, 'create_file_export' ) );

			// - settings
			add_action( 'wp_ajax_fpd_check_api_key', array( &$this, 'check_api_key' ) );

			// - status
			add_action( 'wp_ajax_fpd_reset_image_sources', array( &$this, 'reset_image_sources' ) );

			// - pricing rules
			add_action( 'wp_ajax_fpd_update_pricing_rules', array( &$this, 'update_pricing_rules' ) );

		}

		public function dismiss_notification() {

			if ( !isset($_POST['name']) ) die;

			check_ajax_referer( 'fpd_ajax_nonce' );

			$success = update_option( 'fpd_notification_' . $_POST['name'], true );

			echo json_encode(array(
				'name' => $_POST['name'],
				'success' => $success
			));

			die;

		}

		public function get_products() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$defaults = array(
				'include_views' => true,
				'page' => 1,
				'limit' => 20,
				'search' => null,
				'category_id' => null,
				'cols' => '*',
				'filter_by' => 'ID',
				'sort_by' => 'ASC',
				'user_id' => null
			);

			$args = wp_parse_args( $_GET, $defaults );

			$response = FPD_Resource_Products::get_products( $args );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function create_product() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Products::create_product( $payload );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function update_product() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Products::update_product( $payload['id'], $payload['data'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function delete_product() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Products::delete_product( $payload['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function export_product() {

			if ( !isset($_GET['id']) )
				exit;

			check_ajax_referer( 'fpd_ajax_nonce' );

			$product_id = $_GET['id'];
			//$product_id = 11;

			if( !class_exists('ZipArchive') ) {
				die;
			}

			$upload_dir = wp_upload_dir();
			$upload_dir = $upload_dir['basedir'];

			$exports_dir = $upload_dir . '/fpd_exports/';

			wp_mkdir_p( $exports_dir );

			//temp export dir
			$temp_export_dir = $exports_dir . 'product_' . $product_id;
			wp_mkdir_p( $temp_export_dir );

			$fp = new FPD_Product($product_id);

			//final_json
			$final_json = array();
			$final_json['title'] = $fp->get_title();
			$product_thumbnail = $fp->get_thumbnail();

			//product thumbnail
			if( $source_name = fpd_admin_copy_file( $product_thumbnail, $temp_export_dir) ) {
				$product_thumbnail = $source_name;
			}
			$final_json['thumbnail'] = $product_thumbnail;
			$final_json['options'] = $fp->get_options();
			$final_json['views'] = array();

		    $views = $fp->get_views();

		    foreach($views as $view) {

				$elements = $view->elements;
				if( !is_array($elements) ) {
					continue;
				}
				for($i=0; $i < sizeof($elements); $i++) {

					$source = $elements[$i]['source'];

					if($elements[$i]['type'] == 'image' && base64_encode(base64_decode($source, true)) !== $source) {

						//image layers in views
						if( $source_name = fpd_admin_copy_file( $source, $temp_export_dir) ) {
							$elements[$i]['source'] = $source_name;
						}

					}

				}

				//final_view
				$final_view = array();
				$final_view['title'] = $view->title;
				$view_thumbnail = $view->thumbnail;
				//view thumbnail
				if( $source_name = fpd_admin_copy_file( $view_thumbnail, $temp_export_dir) ) {
					$view_thumbnail = $source_name;
				}
				$final_view['thumbnail'] = $view_thumbnail;
				$final_view['elements'] = $elements;

				$fancy_view = new FPD_View($view->ID);
				$view_options = $fancy_view->get_options();

				//export mask image
				if( isset($view_options['mask']) && isset($view_options['mask']['url']) ) {
					if( $source_name = fpd_admin_copy_file( $view_options['mask']['url'], $temp_export_dir) ) {
						$view_options['mask']['url'] = $source_name;
					}
				}

				$final_view['options'] = $view_options;


				array_push($final_json['views'], $final_view);

			}

			$fop = fopen($temp_export_dir . '/product.json', 'w');
			fwrite($fop, json_encode($final_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
			fclose($fop);

			$zipname =  'product_' . $product_id . '.zip';
			$zip_path =  $exports_dir . $zipname;
			$zip = new ZipArchive;
			$zip->open($zip_path, ZipArchive::CREATE);

			if ($handle = opendir($temp_export_dir)) {

		    	while (false !== ($entry = readdir($handle))) {

		        	if ($entry != "." && $entry != ".." && !strstr($entry,'.php')) {
		            	$zip->addFile($temp_export_dir . '/'. $entry, $entry);
		        	}

		      	}

			  	closedir($handle);
			}

		    $zip->close();

		    fpd_admin_delete_directory($temp_export_dir);

		    header("Content-type: application/zip");
			header("Content-Disposition: attachment; filename=$zipname");
			header("Content-length: " . filesize($zip_path));
			header("Pragma: no-cache");
			header("Expires: 0");
			readfile("$zip_path");

			unlink($zip_path);

			die;

		}

		public function get_product_categories() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$response = FPD_Resource_Products::get_all_product_categories();

			echo json_encode( $response );

			die;

		}

		public function create_product_category() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Products::create_product_category( $payload );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function update_product_category() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Products::update_product_category( $payload['id'], $payload['data'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function delete_product_category() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Products::delete_product_category( $payload['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_templates() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$defaults = array(
				'type' => 'user'
			);

			$args = wp_parse_args( $_GET, $defaults );

			$response = FPD_Resource_Templates::get_product_templates( $args );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function create_template() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Templates::create_product_template( $payload );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function delete_template() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Templates::delete_product_template( $payload['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_view() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$response = FPD_Resource_Views::get_product_view( $_GET['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function update_view() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Views::update_product_view( $payload['id'], $payload['data'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function delete_view() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Views::delete_product_view( $payload['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_options() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$option_keys = isset( $_GET['keys'] ) ? $_GET['keys'] : array();

			$response = FPD_Resource_Options::get_options( $option_keys );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_options_group() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$defaults = array(
				'group' => 'general',
				'lang_code' => null,
			);

			$args = wp_parse_args( $_GET, $defaults );

			$response = FPD_Resource_Options::get_options_group( $args );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function update_options() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Options::update_options( $payload );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_colors_css() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			if( isset($payload['primary_color']) ) {

				$response = self::parse_css($payload['primary_color'], $payload['secondary_color']);

				if(is_wp_error( $response )) {
					wp_send_json_error($response->get_error_message(), 500);
				}
				else {
					echo $response;
				}

			}

			die;

		}

		public function get_ui_layouts() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$response = FPD_Resource_UI_Layouts::get_ui_layouts();

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_ui_layout() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$response = FPD_Resource_UI_Layouts::get_ui_layout( $_GET['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function create_ui_layout() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_UI_Layouts::create_ui_layout( $payload );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function update_ui_layout() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_UI_Layouts::update_ui_layout( $payload['id'], $payload['data'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function delete_ui_layout() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_UI_Layouts::delete_ui_layout( $payload['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_design_categories() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$response = FPD_Resource_Designs::get_design_categories();

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_design_category() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$response = FPD_Resource_Designs::get_single_category( $_GET['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function create_design_category() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Designs::create_design_category( $payload['title'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function update_design_category() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Designs::update_design_category( $payload['id'], $payload['data'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function delete_design_category() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Designs::delete_design_category( $payload['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_orders() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$defaults = array(
				'page' => 1,
				'type' => 'shortcode',
			);

			$args = wp_parse_args( $_GET, $defaults );

			$response = FPD_Resource_Orders::get_all_orders( $args );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function get_order() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$response = FPD_Resource_Orders::get_single_order( $_GET );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function update_order() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Orders::update_order( $payload['id'], $payload['data'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function delete_order() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Orders::delete_order( $payload['id'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public function create_file_export() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = $file_url = null;

			if( isset($payload['summary_json']) && is_string($payload['summary_json']) )
				$payload['summary_json'] = json_decode( stripslashes($payload['summary_json']), true );

			if( isset($payload['print_ready']) && $payload['print_ready'] ) {

				if( class_exists('Fancy_Product_Designer_Export') ) {

					try {

						$file = Fancy_Product_Designer_Export::create_print_ready_file( $payload );
						$file_url = content_url( '/fancy_products_orders/print_ready_files/' . $file );

					}
					catch(Exception $e) {

						$file_url = null;
						$response = new WP_Error(
							'print-ready-export-failed',
							$e->getMessage()
						);

					}

				}
				else
					$response = new WP_Error(
						'print-ready-export-not-installed',
						__('The Print-Ready add-on is not installed', 'radykal')
					);

			}
			else if( isset($payload['svg_data']) ) {

				try {
					$file_url = FPD_File_Export::svg_to_pdf( $payload );
				}
				catch(Exception $e) {

					$file_url = null;
					$response = new WP_Error(
						'basic-export-failed',
						$e->getMessage()
					);

				}

			}

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( array(
					'url' => $file_url
				) );
			}

			die;

		}

		public function check_api_key() {

			if ( !isset($_POST['api_key']) )
				exit;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			$url = add_query_arg( array('api_key' => $_POST['api_key']), Fancy_Product_Designer::get_cloud_admin_api_url() . 'has_plan/premium' );
			$cloud_response = fpd_http_post_json( $url );

			$ajax_res = array(
				'api_key' => $_POST['api_key']
			);

			if( $cloud_response && isset( $cloud_response->error_code ) ) {

				$ajax_res['error_code'] = $cloud_response->error_code;



			}
			else if( $cloud_response && isset( $cloud_response->created_at )) {

				$ajax_res['created_at'] = $cloud_response->created_at;
				update_option( 'fpd_ae_admin_api_key', $_POST['api_key'] );

			}

			header('Content-Type: application/json');
			echo json_encode(
				$ajax_res
			);

			die;
		}

		public function reset_image_sources() {

			if ( !isset($_POST['old_domain']) )
			    exit;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			$old_domain = trim($_POST['old_domain']);
			if(substr($old_domain, -1) == '/') {
			    $old_domain = substr($old_domain, 0, -1);
			}

			$url_parts = parse_url(site_url());
			$current_domain = $url_parts['scheme'].'://'.$url_parts['host'];

			global $wpdb;

			//views
			$updated_views = 0;
			if( fpd_table_exists(FPD_VIEWS_TABLE) ) {

				$views = $wpdb->get_results("SELECT ID, thumbnail, elements FROM ".FPD_VIEWS_TABLE);
				foreach($views as $view) {

					$updated_cols = array();
					$formats = array();
					$thumbnail = stripslashes( maybe_unserialize( $view->thumbnail ) );
					$elements = stripslashes( maybe_unserialize( $view->elements ) );

					if(strpos($thumbnail, $old_domain) !== false) {
						$updated_cols['thumbnail'] = str_replace( $old_domain, $current_domain, $thumbnail );
						$formats[] = '%s';
					}

					if(strpos($elements, $old_domain) !== false) {
						$updated_cols['elements'] = str_replace( $old_domain, $current_domain, $elements );
						$formats[] = '%s';
					}

					if(count($updated_cols) > 0) {
						$wpdb->update( FPD_VIEWS_TABLE, $updated_cols, array('ID' => $view->ID), $formats, '%d' );
						$updated_views++;
					}

				}

			}

			//templates
			$updated_templates = 0;
			$templates = FPD_Template::get_templates();
			foreach($templates as $template) {

				$updated_cols = array();
				$formats = array();
				$views = stripslashes( maybe_unserialize( $template->views ) );

				if(strpos($views, $old_domain) !== false) {
					$updated_cols['views'] = str_replace( $old_domain, $current_domain, $views );
					$formats[] = '%s';
				}

				if(count($updated_cols) > 0) {
					$wpdb->update( FPD_TEMPLATES_TABLE, $updated_cols, array('ID' => $template->ID), $formats, '%d' );
					$updated_templates++;
				}

			}

			//shortcode orders
			$updated_sc_orders = 0;
			if( fpd_table_exists(FPD_ORDERS_TABLE) ) {

				$sc_orders = $wpdb->get_results("SELECT ID, views FROM ".FPD_ORDERS_TABLE);
				foreach($sc_orders as $sc_order) {

					$updated_cols = array();
					$formats = array();
					$views = stripslashes( maybe_unserialize( $sc_order->views ) );

					if(strpos($views, $old_domain) !== false) {
						$updated_cols['views'] = str_replace( $old_domain, $current_domain, $views );
						$formats[] = '%s';
					}

					if(count($updated_cols) > 0) {
						$wpdb->update( FPD_ORDERS_TABLE, $updated_cols, array('ID' => $sc_order->ID), $formats, '%d' );
						$updated_sc_orders++;
					}

				}

			}

			//wc orders
			$updated_wc_orders = 0;
			if( fpd_table_exists(FPD_ORDERS_TABLE) && class_exists('FPD_WC_Order') ) {

				$wc_orders = FPD_WC_Order::get_all_fpd_orders();
				foreach($wc_orders as $wc_order) {

					foreach($wc_order['order_items'] as $order_item) {

						$item_fpd_data = stripslashes( fpd_wc_get_order_item_meta($order_item['ID'], $wc_order['ID']) );

						if( strpos($item_fpd_data, $old_domain) !== false ) {
							wc_update_order_item_meta( $order_item['ID'], '_fpd_data', str_replace( $old_domain, $current_domain, $item_fpd_data ));
							$updated_wc_orders++;
						}

					}

				}

			}

			echo json_encode(array(
				'old_domain' 		=> $old_domain,
				'new_domain' 		=> $current_domain,
				'updated_views' 	=> $updated_views,
				'updated_templates' => $updated_templates,
				'updated_sc_orders' => $updated_sc_orders,
				'updated_wc_orders' => $updated_wc_orders,
			));

			die;

		}

		public function update_pricing_rules() {

			check_ajax_referer( 'fpd_ajax_nonce' );

			$payload = json_decode( file_get_contents('php://input'), true );

			$response = FPD_Resource_Pricing_Rules::update_pricing_rules( $payload['groups'] );

			if(is_wp_error( $response )) {
				wp_send_json_error($response->get_error_message(), 500);
			}
			else {
				echo json_encode( $response );
			}

			die;

		}

		public static function parse_css( $primary_color='', $secondary_color='') {

			$result = FPD_UI_Layout_Composer::parse_css('@primaryColor: '.$primary_color.'; @secondaryColor: '.$secondary_color.';');

			if( !is_array($result) ) {

				return json_encode(array(
					'css' => $result
				));

			}
			else {

				return new WP_Error(
					'parse-colors-css-fail',
					$result['message']
				);

			}

		}

	}
}

new FPD_Admin_Ajax();

?>