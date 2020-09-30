<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_WC_Admin_Product')) {

	class FPD_WC_Admin_Product {

		private $product_options = null;

		public function __construct() {

			add_action( 'admin_footer-post-new.php', array( &$this, 'product_footer' ) );
			add_action( 'admin_footer-post.php', array( &$this, 'product_footer' ) );
			//add select with fpd products
			add_action( 'woocommerce_product_after_variable_attributes', array( &$this, 'variation_settings_fields' ), 10, 3 );
			// Save Variation Settings
			add_action( 'woocommerce_save_product_variation', array( &$this, 'save_variation_settings_fields' ), 10, 2 );

		}

		public function product_footer() {

			global $post;

			if($post->post_type == 'product') {

				?>
				<script type="text/javascript">

					jQuery(document).ready(function() {

						var events = 'woocommerce_variations_loaded woocommerce_variations_added';
						jQuery('#woocommerce-product-data' ).on(events, function() {

							jQuery('.woocommerce_variations .fpd-variation-product-select').each(function(i, select) {

								var $select = jQuery(select);
								//check if select is converted with select2
								if($select.next('.select2').length === 0) {

									$select.select2({
										placeholder: '<?php _e( 'Optional: Load a different product into product designer', 'radykal' ) ?>',
										width: 'style',
										allowClear: true
									});

								}

							});

						});

					});

				</script>
				<?php
			}

		}

		//add meta box to woocommerce orders
		public function variation_settings_fields(  $loop, $variation_data, $variation ) {

			woocommerce_wp_select(
			array(
				'id'          => 'fpd_variation_product_[' . $variation->ID . ']',
				'label'       => __( 'Fancy Product Designer - Product', 'radykal' ),
				'description' => __( 'Changes the product in the Product Designer when a variation is selected.', 'radykal' ),
				'class' 	  => 'fpd-variation-product-select',
				'style'       => 'width: 100%',
				'value'       => get_post_meta( $variation->ID, 'fpd_variation_product', true ),
				'options' =>  $this->get_product_options()
				)
			);

		}

		public function save_variation_settings_fields(  $post_id ) {

			if( isset($_POST['fpd_variation_product_']) ) {

				$fpd_product_select = $_POST['fpd_variation_product_'][ $post_id ];
				$fpd_product_select = empty( $fpd_product_select ) ? '' : $fpd_product_select;
				update_post_meta( $post_id, 'fpd_variation_product', esc_attr( $fpd_product_select ) );

			}

		}

		private function get_product_options() {

			//get fpd products only once, so its not loaded for every variation
			if( !is_null($this->product_options) )
				return $this->product_options;

			$product_options = array();
			$product_options[''] = ''; //add empty option, so placeholder is showing on init

			$products = FPD_Product::get_products( array(
				'order_by' 	=> "ID ASC",
			) );

			foreach($products as $fpd_product) {
				$product_options[$fpd_product->ID] = '#'.$fpd_product->ID. ' - '. $fpd_product->title;
			}

			$this->product_options = $product_options;

			return $product_options;

		}

	}

}

new FPD_WC_Admin_Product();

?>