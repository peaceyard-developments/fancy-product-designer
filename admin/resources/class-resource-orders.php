<?php

if( !class_exists('FPD_Resource_Orders') ) {

	class FPD_Resource_Orders {

		const MAX_RESULTS = 20;

		public static function get_all_orders( $args = array() ) {

			$defaults = array(
				'type' => null,
				'page' => 1
			);

			$args = wp_parse_args( $args, $defaults );

			global $wpdb;

			$order_type = $args['type']; //load orders by type: wc | shortcode
			$page = $args['page']; //pagination
			$offset = ( $page - 1 ) * self::MAX_RESULTS;

			//get shortcode orders
			$shortcode_orders = array();
			if( fpd_table_exists(FPD_ORDERS_TABLE) && (is_null($order_type) || $order_type === 'shortcode') ) {

				$shortcode_orders = FPD_Shortcode_Order::get_orders( self::MAX_RESULTS, $offset );

			}

			//get woocommerce orders
			$wc_orders = array();
			if( class_exists('WooCommerce') && (is_null($order_type) || $order_type === 'wc') ) {

				$wc_orders = $wpdb->get_results("
					SELECT ID,post_date AS created_date FROM {$wpdb->prefix}posts
					WHERE ID IN(
						SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items
						WHERE order_item_id IN (
							SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key LIKE '%fpd_data%'
						)
						GROUP BY order_id
					)
					AND post_status NOT LIKE 'trash'
					ORDER BY ID DESC
					LIMIT ".self::MAX_RESULTS."
					OFFSET $offset
				", ARRAY_A);

				foreach($wc_orders as $key => $wc_order) {

					$order_id = $wc_order['ID'];
					$wc_order_items = $wpdb->get_results("
						SELECT order_item_id AS ID, order_item_name AS name FROM {$wpdb->prefix}woocommerce_order_items
						WHERE order_id = {$order_id}
						AND
						order_item_id IN (
							SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key LIKE '%fpd_data%'
						)
					", ARRAY_A);

					foreach($wc_order_items as $item_key => $wc_order_item) {

						$print_data = wc_get_order_item_meta( $wc_order_item['ID'], '_fpd_print_order' );

						if( !empty($print_data) )
							$wc_order_items[$item_key]['has_print_data'] = true;

					}


					$wc_orders[$key]['order_items'] = $wc_order_items;

				}

			}

			//get gravity form orders
			$gf_orders = array();
			if( class_exists('GFForms') && (is_null($order_type) || $order_type === 'gf') ) {

				$gf_orders = $wpdb->get_results("
					SELECT entry_id AS ID FROM {$wpdb->prefix}gf_entry_meta
					WHERE meta_value LIKE '%{\"product\"%' AND entry_id IN (
						SELECT ID FROM {$wpdb->prefix}gf_entry WHERE status='active'
					)
					ORDER BY ID DESC
					LIMIT ".self::MAX_RESULTS."
					OFFSET $offset
				",ARRAY_A);

				foreach($gf_orders as $key => $gf_order) {

					$gf_orders[$key]['created_date'] = $wpdb->get_var("SELECT date_created AS ID FROM {$wpdb->prefix}gf_entry WHERE id=".$gf_order['ID']."");

				}

			}

			//merge wc and shortcode orders
			$all_orders = array_merge($shortcode_orders, $wc_orders, $gf_orders);
			$all_orders = apply_filters( 'fpd_rest_orders_data', $all_orders, $order_type );

			return $all_orders;

		}

		public static function get_single_order( $args = array() ) {

			$defaults = array(
				'id' => null,
				'type' => 'wc',
				'item_key' => '_fpd_data', //woocommerce
				'item_id' => null //woocommerce
			);

			$args = wp_parse_args( $args, $defaults );

			$id = $args['id'];
			$order_type = $args['type'];
			$order_type = empty($order_type) ? 'wc' : $order_type;
			$order_item_key = empty($args['item_key']) ? '_fpd_data' : $args['item_key'];

			$response_data = array();

			if( class_exists('WooCommerce') && $order_type === 'wc' ) {

				$response_data['ID'] = $id;

				//get all items from an wc order
				if( empty($args['item_id']) ) {

					$wc_order = wc_get_order( $id );
					$order_items = $wc_order->get_items();
					$order_items_data = array();

					foreach($order_items as $order_item) {

						$wc_order_item = wc_get_order_item_meta( $order_item->get_id(), $order_item_key, true );
						if( !empty($wc_order_item) ) { //check if item with key exists

							$order_data = json_decode(fpd_strip_multi_slahes($wc_order_item), true);
							$order_data['item_id'] = $order_item->get_id();

							array_push( $order_items_data, $order_data );
						}

					}

					$response_data['order_items'] = $order_items_data;

				}
				//get single item from a wc order
				else {

					$order_item_id = $args['item_id'];

					if( $order_item_key == '_fpd_data' ) {
						$wc_order_item = wc_get_order_item_meta( $order_item_id, '_fpd_data', true );
						if( empty($wc_order_item) ) //fallback older orders
							$wc_order_item = wc_get_order_item_meta( $order_item_id, 'fpd_data', true );
					}
					else {
						$wc_order_item = wc_get_order_item_meta( $order_item_id, $order_item_key, true );
					}

					if( $wc_order_item ) {

						$wc_order_item = is_array($wc_order_item) ? $wc_order_item['fpd_product'] : $wc_order_item;
						if( function_exists('fpd_strip_multi_slahes') )
							$wc_order_item = fpd_strip_multi_slahes($wc_order_item);

						$response_data['order'] = json_decode( $wc_order_item );

					}
					else {
						$response_data = null;
					}

				}


			}
			//gf order
			else if( class_exists('GFForms') && $order_type === 'gf' ) {

				global $wpdb;

				$order = $wpdb->get_row( "SELECT entry_id, meta_value FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id=$id AND meta_value LIKE '%{\"product\"%'");

				if( !empty($order) ) {

					$response_data = [
						'ID' => $order->entry_id,
						'order' => json_decode( $order->meta_value ),
					];

				}
				else {
					$response_data = null;
				}

			}
			//shortcode order
			else if( fpd_table_exists(FPD_ORDERS_TABLE)  && $order_type === 'shortcode' ) {

				global $wpdb;

				$order_row = $wpdb->get_row( "SELECT * FROM ".FPD_ORDERS_TABLE." WHERE ID=$id");

				if( !empty($order_row) ) {

					if( $order_item_key == '_fpd_print_order' )
						$order_data = $order_row->print_order;
					else
						$order_data = isset($order_row->views) ? $order_row->views : $order_row->order;

					$response_data = [
						'ID' => $order_row->ID,
						'created_date' => $order_row->created_date,
						'order' => json_decode( fpd_strip_multi_slahes( $order_data ) ),
					];

				}
				else {
					$response_data = null;
				}

			}

			if( !empty( $response_data ) ) {
				return apply_filters( 'fpd_rest_single_order_data', $response_data, $id, $order_type );
			}
			else

				return new WP_Error(
					'order-not-found',
					__('Order not found!', 'radykal')
				);

		}

		public static function update_order( $order_id , $args = array() ) {

			$defaults = array(
				'type' => 'wc',
				'data' => null,
				'print_data' => null,
				'old_dp_url' => null,
				'dp_url' => null,
			);

			$args = wp_parse_args( $args, $defaults );
			$order_data = is_array( $args['data'] ) ? json_encode( $args['data'] ) : $args['data'];
			$order_print_data = is_array( $args['print_data'] ) ? json_encode( $args['print_data'] ) : $args['print_data'];

			$updated = false;

			//download DP image to local server
			if( !is_null( $args['dp_url'] ) ) {

				$new_src = $args['dp_url'];

				$filename = time().'.jpeg';
				$dp_dir = FPD_WP_CONTENT_DIR . '/uploads/fpd_depositphotos/';
				$dp_dir_url = content_url( '/uploads/fpd_depositphotos/'.$filename );

				if( !file_exists($dp_dir) )
					wp_mkdir_p($dp_dir);

				if( copy($new_src, $dp_dir.$filename) ) {

					return array(
						'old_dp_url' => $args['old_dp_url'],
						'new_dp_url' => $dp_dir_url,
						'message' =>  __('Depositphotos Image successfully replaced!', 'radykal')
					);

				}

			}
			if($args['type'] == 'wc') {

				$updated = wc_update_order_item_meta( $order_id, '_fpd_data', $order_data );
				if( !empty($order_print_data) )
					wc_update_order_item_meta( $order_id, '_fpd_print_order', $order_print_data );

			}
			else if($args['type'] == 'shortcode') {

				$cols_data = array( 'views' => $order_data );

				if( !empty($order_print_data) )
					$cols_data['print_order'] = $order_print_data;

				$updated = FPD_Shortcode_Order::update( $order_id, $cols_data );

			}
			else if($args['type'] == 'gf' ) {

				$gf_fpd_order_field_id = null;
				$gf_entry = GFAPI::get_entry($order_id);

				//no entry found with given id
				if(is_wp_error( $gf_entry ) ) {
					return $gf_entry;
				}

				$gf_form_id = $gf_entry['form_id'];
				$gf_form = GFAPI::get_form($gf_form_id);
				$gf_form_fields = $gf_form['fields'];

				//find order field id in form fields
				foreach($gf_form_fields as $gf_form_field) {
					if( property_exists( $gf_form_field, 'cssClass' ) && $gf_form_field->cssClass == 'fpd-order' ) {
						$gf_fpd_order_field_id = $gf_form_field->id;
					}
				}

				if( !is_null($gf_fpd_order_field_id) )
					$updated = GFAPI::update_entry_field( $order_id, intval($gf_fpd_order_field_id), $order_data );


			}

			if( $updated ) {
				return array(
					'message' => __('Order successfully updated.', 'radykal')
				);
			}
			else

				return new WP_Error(
					'order-update-fail',
					__('Order could not be updated. Please try again!', 'radykal')
				);

		}

		public static function delete_order( $order_id , $type = 'shortcode' ) {

			$deleted = false;
			if( $type == 'shortcode' ) {

				$deleted = FPD_Shortcode_Order::delete( $order_id );

			}

			if( $deleted ) {
				return array(
					'message' => __('Order successfully deleted.', 'radykal')
				);
			}
			else

				return new WP_Error(
					'order-delete-fail',
					__('Order could not be deleted. Please try again!', 'radykal')
				);

		}

	}

}

?>