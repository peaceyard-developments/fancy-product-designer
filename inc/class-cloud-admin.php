<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Cloud_Admin')) {

	class FPD_Cloud_Admin {

		public function __construct() {

			//WOOCOMMERCE ORDERS

			//print-ready download links in emails
			add_action( 'woocommerce_order_item_meta_end', array(&$this, 'add_order_item_links') , 20, 4 );

			//print-ready files attached into order emails
			add_filter( 'woocommerce_email_attachments', array( &$this, 'woo_email_attachment' ), 10, 3  );


			//SHORTCODE ORDERS

			//print-ready download links in emails
			add_action( 'fpd_shortcode_order_mail_message', array(&$this, 'shortcode_order_mail'), 10, 3);
			add_action( 'fpd_shortcode_order_mail_attachments', array(&$this, 'shortcode_order_attachments'), 10, 3);

		}

		public function shortcode_order_mail( $message, $order_id, $order_data ) {

			$api_key = fpd_get_option( 'fpd_ae_admin_api_key' );

			if( !empty($api_key) && fpd_get_option('fpd_ae_recipient_admin') && isset($order_data['data']['print_order']) ) {

				$ae_download_url = $this->get_print_file_uri(array(
					'api_key' => $api_key,
					'type' => 'shortcode',
					'order_id' => $order_id,
					'output_file' => fpd_get_option('fpd_ae_output_file')
				));

				$message .= sprintf( __('%s: %s', 'radykal'), FPD_Settings_Labels::get_translation( 'misc', 'automated_export:download' ), esc_url_raw( $ae_download_url ) )."\n";

			}

			return $message;

		}

		public function shortcode_order_attachments( $attachments, $order_id, $order_data ) {

			$api_key = fpd_get_option( 'fpd_ae_admin_api_key' );

			if( !empty($api_key) && fpd_get_option('fpd_ae_recipient_admin') && isset($order_data['data']['print_order']) ) {

				$ae_download_url = $this->get_print_file_uri(array(
					'api_key' => $api_key,
					'type' => 'shortcode',
					'order_id' => $order_id,
					'return_file' => 1,
					'output_file' => fpd_get_option('fpd_ae_output_file')
				));

				$response = fpd_http_post_json( $ae_download_url );

				if( $response && $response->file_url) {

					$file_url = $response->file_url;

					//creare temp directory for storing files to be sent as attachments
					if( !file_exists(FPD_TEMP_DIR) )
						wp_mkdir_p( FPD_TEMP_DIR );

					$temp_local_file = FPD_TEMP_DIR.basename($file_url);

					if( file_exists($temp_local_file) )
						unlink($temp_local_file);

					if( fpd_admin_write_file_content( $file_url, $temp_local_file ) )
						$attachments[] = $temp_local_file;

				}

			}

			return $attachments;

		}

		public function add_order_item_links( $item_id, $item, $order, $plain_text=null ) {

			$api_key = fpd_get_option( 'fpd_ae_admin_api_key' );

			if( empty($api_key) || $plain_text )
				return;

			$display_to_admin = ( fpd_get_option('fpd_ae_recipient_admin') && FPD_WC_Index::$sent_to_admin );
			$display_to_customer = ( fpd_get_option('fpd_ae_recipient_customer') &&  ( FPD_WC_Index::$email_id === 'customer_completed_order' || $order->is_paid() ) );

			if( isset($item['_fpd_print_order']) && ($display_to_customer || $display_to_admin) ) {

				$ae_download_url = $this->get_print_file_uri(array(
					'api_key' => $api_key,
					'type' => 'wc',
					'order_id' => $order->get_id(),
					'item_id' => $item_id,
					'output_file' => fpd_get_option('fpd_ae_output_file')
				));

				echo sprintf( '<a href="%s" target="_blank" class="fpd-download-print-ready-file" style="border: 1px solid rgba(0,0,0,0.8); padding: 4px 6px; border-radius: 2px; font-size: 0.85em; color: rgba(0,0,0,0.8); text-decoration: none;">%s</a>', esc_url( $ae_download_url ), FPD_Settings_Labels::get_translation( 'misc', 'automated_export:download' ) );

			}

		}

		public function woo_email_attachment( $attachments, $email_id, $order ) {

			$api_key = get_option( 'fpd_ae_admin_api_key', '' );

			if( empty($api_key) || !fpd_get_option('fpd_ae_email_attachment') )
				return $attachments;

			//check if order has fpd print orders
			$order_has_fpd_print_order = false;
			$order_items = $order->get_items();

			if( !is_array($order_items) )
				return $attachments;

			foreach( $order_items as $order_item ) {

				$fpd_print_order = wc_get_order_item_meta( $order_item->get_id(), '_fpd_print_order', true );
				if( !empty($fpd_print_order) ) {
					$order_has_fpd_print_order = true;
					break;
				}

			}

			if( !$order_has_fpd_print_order )
				return $attachments;

			$display_to_admin = ( fpd_get_option('fpd_ae_recipient_admin') && $email_id === 'new_order' );
			$display_to_customer = ( fpd_get_option('fpd_ae_recipient_customer') && ( $email_id === 'customer_completed_order' || $order->is_paid() ) );

			if( $display_to_admin || $display_to_customer ) {

				$ae_download_url = $this->get_print_file_uri(array(
					'api_key' => $api_key,
					'type' => 'wc',
					'return_file' => 1,
					'order_id' => $order->get_id(),
					'output_file' => fpd_get_option('fpd_ae_output_file')
				));

				$response = fpd_http_post_json( $ae_download_url );

				if( $response && $response->file_urls) {

					//creare temp directory for storing files to be sent as attachments
					if( !file_exists(FPD_TEMP_DIR) )
						wp_mkdir_p( FPD_TEMP_DIR );

					foreach($response->file_urls as $file_url) {

						$temp_local_file = FPD_TEMP_DIR.basename($file_url);

						if( file_exists($temp_local_file) )
							unlink($temp_local_file);

						if( fpd_admin_write_file_content( $file_url, $temp_local_file ) )
							$attachments[] = $temp_local_file;

					}

				}

			}

			return $attachments;

		}

		private function get_print_file_uri( $params=array() ) {

			return add_query_arg( $params, Fancy_Product_Designer::get_cloud_admin_api_url() . 'create_print_file' );

		}

	}
}

new FPD_Cloud_Admin();

?>