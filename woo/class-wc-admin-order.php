<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Admin_Order')) {

	class FPD_Admin_Order {

		public function __construct() {

			add_filter( 'woocommerce_hidden_order_itemmeta', array( &$this, 'hide_fpd_meta' ) );
			add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
			add_action( 'woocommerce_before_order_itemmeta', array( &$this, 'admin_order_item_values' ), 10, 3 );

		}

		//hide fpd_data from meta displayment
		public function hide_fpd_meta( $meta_keys ) {

			array_push($meta_keys, '_fpd_data');
			array_push($meta_keys, '_fpd_print_order');
			array_push($meta_keys, '_fpd_product_thumbnail');

			return $meta_keys;

		}

		//add meta box to woocommerce orders
		public function add_meta_boxes() {

			add_meta_box(
				'fpd-order',
				__( 'Fancy Product Designer', 'radykal' ),
				array( &$this, 'output_meta_box'),
				'shop_order',
				'normal',
				'default'
			);

		}

		//add a button to the ordered fancy product
		public function admin_order_item_values( $item_id, $item, $_product ) {

			if( is_object($_product) ) {

				global $post_id;
				$fpd_data = fpd_wc_get_order_item_meta( $item_id, $post_id );

				if( !empty($fpd_data) ) {

					$fpd_product = json_decode( fpd_strip_multi_slahes($fpd_data) );
					$fpd_thumbnail = wc_get_order_item_meta( $item_id, '_fpd_product_thumbnail' );

					?>
					<p>
						<?php if( !empty($fpd_thumbnail) ): ?>
						<img src="<?php esc_attr_e( $fpd_thumbnail ); ?>" style="float: left; height: 60px; width: auto; margin-right: 10px;" />
						<?php endif; ?>
						<?php if( $fpd_product && isset($fpd_product->product[0]->productTitle) ): ?>
						<span class="fpd-product-title"><?php printf( __('FPD Product: %s', 'radykal'), $fpd_product->product[0]->productTitle ); ?></span>
						<?php endif; ?>
					</p>
					<a href="#" class='button button-secondary fpd-show-order-item' data-order_id='<?php echo $post_id; ?>' data-order_item_id='<?php echo $item_id; ?>'><?php _e( 'Load in Order Viewer', 'radykal' ); ?></a>
					<?php

				}

			}

		}

		public function output_meta_box()  {

			echo '<div id="fpd-react-root"></div>';

		}

	}

}

new FPD_Admin_Order();

?>