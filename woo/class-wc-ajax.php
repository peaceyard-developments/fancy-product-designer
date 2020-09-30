<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_WC_Ajax') ) {

	class FPD_WC_Ajax {

		public function __construct() {

			add_action( 'init', array( &$this, 'init') );

		}

		public function init() {

			//--- FRONTEND

			//load a product via ajax (used for different product variation)
			add_action( 'wp_ajax_fpd_load_product', array( &$this, 'load_product' ) );
			add_action( 'wp_ajax_nopriv_fpd_load_product', array( &$this, 'load_product' ) );

			//save order from account
			add_action( 'wp_ajax_fpd_save_order', array( &$this, 'save_order' ) );
			add_action( 'wp_ajax_nopriv_fpd_save_order', array( &$this, 'save_order' ) );

			//--- ADMIN

			add_action( 'wp_ajax_fpd_loadorder', array( &$this, 'load_order' ) );

		}

		public function load_product() {

			if( !isset($_POST['product_id']) )
				die;

			$product_id = $_POST['product_id'];

			$fancy_product = new FPD_Product( $product_id );
			echo $fancy_product->to_JSON();

			die;

		}

		public function save_order() {

			if( !isset($_POST['item_id']) && !isset($_POST['fpd_order']) )
				die;

			$item_id = intval($_POST['item_id']);

			$updated = wc_update_order_item_meta( $item_id, '_fpd_data', $_POST['fpd_order'] );

			if( isset($_POST['print_order']) && !empty($_POST['print_order']) )
				wc_update_order_item_meta( $item_id, '_fpd_print_order', $_POST['print_order'] );

			echo json_encode(array(
				'updated' => $updated
			));

			die;

		}

		//load the order data for the order viewer
		public function load_order() {

			if ( !isset($_POST['order_id']) || !isset($_POST['item_id']) )
			    die;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			header('Content-Type: application/json');

			$fpd_data = fpd_wc_get_order_item_meta( $_POST['item_id'], $_POST['order_id'] );

			if( !empty($fpd_data) ) {

				//V3.4.9: only order is stored in fpd_data
				$fpd_data = is_array($fpd_data) ? $fpd_data['fpd_product'] : $fpd_data;

				$views = fpd_update_image_source($fpd_data);

				//remove slashes, happening since WC3.1.0
				$views = fpd_strip_multi_slahes($views);

				$output = apply_filters( 'fpd_ajax_load_order_data', array(
					'order_data' => $views
				), $fpd_data, $_POST['order_id'], $_POST['item_id']);

				echo json_encode($output);

			}

			die;

		}

	}

}

new FPD_WC_Ajax();

?>