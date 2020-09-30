<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_WC_Order')) {

	class FPD_WC_Order {

		private $embedded_mail_images = array();

		public function __construct() {

			global $woocommerce;

			add_action( 'woocommerce_new_order_item', array( &$this, 'add_order_item_meta'), 10, 2 );

			//edit order item permalink, so it loads the customized product
			add_filter( 'woocommerce_order_item_permalink', array(&$this, 'change_order_item_permalink') , 10, 3 );

			//add additional links to order item
			add_action( 'woocommerce_order_item_meta_end', array(&$this, 'display_order_item_meta') , 10, 4 );

			//add fpd_data to order-again action
			add_filter( 'woocommerce_order_again_cart_item_data', array(&$this, 'add_order_again_item_data') , 10, 3 );

			//Email: add order item thumbnail
			add_filter( 'woocommerce_order_item_meta_start', array(&$this, 'add_order_item_thumbnail_email') , 10, 4 );
			add_action( 'phpmailer_init', array(&$this, 'init_phpmailer'));

		}

		//add order meta from the cart
		public function add_order_item_meta( $item_id, $item ) {

			$fpd_data = isset( $item->legacy_values['fpd_data'] ) ? $item->legacy_values['fpd_data'] : null;

			if( !is_null($fpd_data) ) {

				$order = new WC_Order( wc_get_order_id_by_order_item_id($item_id) );

				$additional_data = array(
					'order_id' => $order->get_id(),
					'item_id' => $item_id,
					'item' => $item,
					'customer_mail' => $order->get_billing_email()
				);

				$fpd_data = apply_filters( 'fpd_new_order_item_data', $fpd_data, 'wc', $additional_data );

				wc_add_order_item_meta( $item_id, '_fpd_data', $fpd_data['fpd_product'] );

				if( isset($fpd_data['fpd_print_order']) )
					wc_add_order_item_meta( $item_id, '_fpd_print_order', $fpd_data['fpd_print_order'] );

				if( fpd_get_option('fpd_order_product_thumbnail') )
					wc_add_order_item_meta( $item_id, '_fpd_product_thumbnail', $fpd_data['fpd_product_thumbnail'] );

			}

		}

		public function change_order_item_permalink( $permalink, $item, $order ) {

			//V3.4.9 stores data in _fpd_data
			$item_has_fpd = isset($item['fpd_data']) || isset($item['_fpd_data']);

			if( $item_has_fpd ) {

				$order_items = $order->get_items();
				$item_id = array_search($item, $order_items);

				if($item_id !== false) {

					$permalink = add_query_arg( array(
						'order' => method_exists($order,'get_id') ? $order->get_id() : $order->id,
						'item_id' => $item_id),
					$permalink );
				}
			}

			return $permalink;

		}

		public function add_order_item_thumbnail_email( $item_id, $item, $order, $plain_text=false ) {

			if( fpd_get_option('fpd_order_product_thumbnail') && !$plain_text ) {

				$product = $item->get_product();

				if( is_bool($product) )
					return;

				if( !file_exists(FPD_TEMP_DIR) )
					wp_mkdir_p( FPD_TEMP_DIR );

				$item_thumb_src = '';
				if( isset($item['_fpd_product_thumbnail']) ) {

					$temp_img_path = FPD_TEMP_DIR . uniqid(time()) . '.png';
					//get the base-64 from data
					$data_url = $item['_fpd_product_thumbnail'];
					$base64_str = substr($data_url, strpos($data_url, ",")+1);

					if( !is_page(get_the_ID()) && !is_view_order_page() && file_put_contents($temp_img_path, base64_decode($base64_str)) ) {
						$this->embedded_mail_images[] = $temp_img_path;
						$item_thumb_src = 'cid:'.basename($temp_img_path, '.d');
					}
					else {
						$item_thumb_src = isset($item['_fpd_product_thumbnail']) ? $item['_fpd_product_thumbnail'] : get_the_post_thumbnail_url($product->get_id());
					}

				}
				else {
					$item_thumb_src = get_the_post_thumbnail_url($product->get_id());
				}

				if( !empty( $item_thumb_src ) )
					echo '<div style="border: 1px solid #ccc; float: left; margin-right: 5px; margin-bottom: 5px; max-width: 30%; "><img src="'.$item_thumb_src.'" style="height: auto; width: 100%; " /></div>';

			}

		}

		public function init_phpmailer( $phpmailer ) {

			foreach($this->embedded_mail_images as $img) {
				$phpmailer->AddEmbeddedImage($img, basename($img, '.d'));
			}

		}

		public function display_order_item_meta( $item_id, $item, $order, $plain_text=false ) {

			if( $plain_text )
				return;

			$product = $item->get_product();

			if( is_bool($product) )
				return;

			//V3.4.9 stores data in _fpd_data
			$item_has_fpd = isset($item['fpd_data']) || isset($item['_fpd_data']);

			//view customized product link
			if( fpd_get_option('fpd_order_email_customization_link') && $item_has_fpd ) {

				$url = add_query_arg( array(
					'order' => method_exists($order,'get_id') ? $order->get_id() : $order->id,
					'item_id' => $item_id),
				$product->get_permalink($item) );

				echo sprintf( '<a href="%s" style="display: block; font-size: 0.9em; color: rgba(0,0,0,0.8);">%s</a>', $url, FPD_Settings_Labels::get_translation('woocommerce', 'order:_view_customized_product') );

			}

			//download button
			if( $item_has_fpd && $product->is_downloadable() && $order->is_download_permitted() ) {

				$url = add_query_arg( array(
					'order' => method_exists($order,'get_id') ? $order->get_id() : $order->id,
					'item_id' => $item_id),
				$product->get_permalink($item) );

				echo sprintf( ' | <a href="%s" class="fpd-order-item-download" style="font-size: 0.85em; color: rgba(0,0,0,0.8);">%s</a>', esc_url( $url ), __('Download', 'radykal') );
			}

			//show element props
			if( $item_has_fpd && fpd_get_option('fpd_order_show_element_props') !== 'none' ) {

				//V3.4.9: data stored in _fpd_data
				$fpd_data = isset($item['_fpd_data']) ? $item['_fpd_data'] : $item['fpd_data'];
				//V3.4.9: only order is stored in fpd_data
				$fpd_data = is_array($fpd_data) ? $fpd_data['fpd_product'] : $fpd_data;

				$order = json_decode(stripslashes($fpd_data), true);

				if( fpd_get_option('fpd_order_show_element_props') === 'used_colors' ) {
					echo '<div style="margin-top:10px;">'.implode('', FPD_WC_Cart::get_display_elements( $order['product'], 'used_colors' )).'</div>';
				}
				else {

					$display_elements = FPD_WC_Cart::get_display_elements( $order['product'] );
					foreach($display_elements as $display_element) {
						echo '<div style="margin: 10px 0; clear: both;"><p style="font-weight: bol;font-size:0.95em; margin: 10px 0 0px;">'.$display_element['title'].':</p>'.$display_element['values'].'</div>';
					}

				}

			}

		}

		//add cart item meta when order again
		public function add_order_again_item_data( $cart_item_data, $item, $order ) {

			foreach ( $item->get_meta_data() as $meta ) {

				if($meta->key === '_fpd_data') {
					$cart_item_data['fpd_data']['fpd_product'] = $meta->value;
					$cart_item_data['fpd_data']['fpd_product_price'] = $order->get_item_total($item, true);
				}

			}

			return $cart_item_data;

		}

		public static function get_all_fpd_orders() {

			global $wpdb;

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

				$wc_orders[$key]['order_items'] = $wc_order_items;

			}

			return $wc_orders;

		}

	}
}

new FPD_WC_Order();

?>