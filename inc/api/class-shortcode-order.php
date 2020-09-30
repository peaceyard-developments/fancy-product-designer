<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Shortcode_Order')) {

	class FPD_Shortcode_Order {

		public static function create( $customer_name, $customer_mail, $order, $print_order = null ) {

			if( empty($order) ) {
				return false;
			}

			global $wpdb, $charset_collate;

			//create views table if necessary
			if( !fpd_table_exists(FPD_ORDERS_TABLE) ) {

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				//create products table
				$sql_string = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				              customer_name VARCHAR(300) COLLATE utf8_general_ci NOT NULL,
				              customer_mail VARCHAR(100) COLLATE utf8_general_ci NOT NULL,
				              views LONGTEXT COLLATE utf8_general_ci NOT NULL,
							  PRIMARY KEY (ID)";

				$sql = "CREATE TABLE ".FPD_ORDERS_TABLE." ($sql_string) $charset_collate;";
				dbDelta($sql);

			}

			$date_col_exists = $wpdb->get_var( "SHOW COLUMNS FROM ".FPD_ORDERS_TABLE." LIKE 'created_date'" );
			if( empty($date_col_exists) ) {
				$wpdb->query( "ALTER TABLE ".FPD_ORDERS_TABLE." ADD COLUMN created_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP" );
			}

			$order_col_exists = $wpdb->get_var( "SHOW COLUMNS FROM ".FPD_ORDERS_TABLE." LIKE 'order'" );
			if( !empty($order_col_exists) ) {
				$wpdb->query( "ALTER TABLE ".FPD_ORDERS_TABLE." CHANGE COLUMN `order` `views` LONGTEXT NOT NULL" );
			}

			$print_order_col_exists = $wpdb->get_var( "SHOW COLUMNS FROM ".FPD_ORDERS_TABLE." LIKE 'print_order'" );
			if( $print_order && empty($print_order_col_exists) ) {
				$wpdb->query( "ALTER TABLE ".FPD_ORDERS_TABLE." ADD COLUMN print_order LONGTEXT COLLATE utf8_general_ci NOT NULL" );
			}

			$order_data = array(
				'data' => array(
					'customer_name' => $customer_name,
					'customer_mail' => $customer_mail,
					'views' => $order
				),
				'format' => array( '%s', '%s', '%s')
			);

			if( $print_order ) {
				$order_data['data']['print_order'] = $print_order;
				$order_data['format'][] = '%s';
			}

			$order_data = apply_filters( 'fpd_shortcode_order_data', $order_data );

			$inserted = $wpdb->insert(
				FPD_ORDERS_TABLE,
				$order_data['data'],
				$order_data['format']
			);

			if( $inserted ) {

				$additional_data = array(
					'order_id' => $wpdb->insert_id,
					'customer_name' => $customer_name,
					'customer_mail' => $customer_mail
				);

				$fpd_data = array(
					'fpd_product' => $order,
					'fpd_print_order' => $print_order
				);

				$fpd_data = apply_filters( 'fpd_new_order_item_data', $fpd_data, 'shortcode', $additional_data );

				$to_mail = get_option('admin_email');
				$subject = sprintf( __('New Order received from %s', 'radykal'), $customer_name );

				$message  = sprintf( __('New Order received from %s.', 'radykal'), $customer_name)."\n\n";
				$message .= sprintf( __('Order Details for #%d', 'radykal'), $inserted)."\n";
				$message .= "====================================\n";
				$message .= sprintf( __('Customer Name: %s', 'radykal'), $customer_name )."\n";
				$message .= sprintf( __('Customer Email: %s', 'radykal'), $customer_mail)."\n";
				$message .= "====================================\n\n";
				$message .= sprintf( __('View Order: %s', 'radykal'), esc_url_raw( admin_url('admin.php?page=fpd_orders') ) )."\n";

				$to_mail = apply_filters( 'fpd_shortcode_order_mail_to', $to_mail, $wpdb->insert_id, $order_data );
				$subject = apply_filters( 'fpd_shortcode_order_mail_subject', $subject, $wpdb->insert_id, $order_data );
				$message = apply_filters( 'fpd_shortcode_order_mail_message', $message, $wpdb->insert_id, $order_data );
				$headers = apply_filters( 'fpd_shortcode_order_mail_headers', '', $wpdb->insert_id, $order_data );
				$attachments = apply_filters( 'fpd_shortcode_order_mail_attachments', array(), $wpdb->insert_id, $order_data );

				do_action('fpd_shortcode_order_mail', $inserted, $customer_name, $customer_mail, $message, $wpdb->insert_id, $order_data );

				wp_mail( $to_mail, $subject, $message, $headers, $attachments );
				return $inserted;

			}
			else {
				return false;
			}

		}

		public static function get_orders( $limit=5, $offset=0, $include_order_data=false ) {

			if( fpd_table_exists(FPD_ORDERS_TABLE) ) {

				global $wpdb;

				if( $include_order_data ) {

					return $wpdb->get_results(
						"SELECT * FROM ".FPD_ORDERS_TABLE." ORDER BY ID DESC LIMIT $limit OFFSET $offset",
						ARRAY_A
					);

				}
				else {

					$cols_str = "ID, created_date, CONCAT(customer_name, ' ',customer_mail) AS name";
					$print_order_col_exists = $wpdb->get_var( "SHOW COLUMNS FROM ".FPD_ORDERS_TABLE." LIKE 'print_order'" );
					if( !empty($print_order_col_exists) )
						$cols_str .= ", LENGTH(print_order) as has_print_data";

					return $wpdb->get_results(
						"SELECT ".$cols_str." FROM ".FPD_ORDERS_TABLE." ORDER BY ID DESC LIMIT $limit OFFSET $offset",
						ARRAY_A
					);

				}


			}

			return array();

		}

		public static function get_order( $id ) {

			global $wpdb;
			$data = $wpdb->get_row("SELECT * FROM ".FPD_ORDERS_TABLE." WHERE ID=$id ");

			//replace old views column name with order
			if( isset($data->order) ) {
				$data->views = $data->order;
				unset($data->order);
			}

			$data->views = stripslashes(fpd_update_image_source( $data->views ));

			return $data;

		}

		public static function update( $id, $data_array = array() ) {

			global $wpdb;

			//all available columns with format that can be updated
			$data_keys = array(
				'customer_name' => '%s',
				'customer_mail' => '%s',
				'views' => '%s',
				'created_data' => '%s',
				'print_order' => '%s'
			);

			//the data and formats arrays that will be used in the sql
			$data = array();
			$formats = array();

			//loop through all available keys and check if the key exist in the passed data_array
			foreach( $data_keys as $key => $value ) {

				if( array_key_exists( $key, $data_array ) ) {

					$data[$key] = $data_array[$key];
					$formats[] = $data_keys[$key];

				}

			}

			//update view with the passed data and return number of updated columns
			return $wpdb->update(
				FPD_ORDERS_TABLE,
				$data,
				array('ID' => $id),
				$formats,
				'%d'
			);

		}

		public static function delete( $id ) {

			global $wpdb;

			try {
				$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_ORDERS_TABLE." WHERE ID=%d", $id) );
				return 1;
			}
			catch(Exception $e) {
				return 0;
			}

		}

	}

}