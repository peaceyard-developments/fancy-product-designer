<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_WC_Cross_Sells')) {

	class FPD_WC_Cross_Sells {

		public function __construct() {

			$display = get_option('fpd_cross_sells_display', 'none');

			//SINGLE PRODUCT PAGE
			add_action( 'fpd_product_designer_form_end', array(&$this, 'add_product_form_field') );
			add_action( 'fpd_after_product_designer', array( &$this, 'after_product_designer'), 5 );

			if( $display === 'single_before' ) {
				add_action( 'woocommerce_before_single_product', array(&$this, 'cross_sells_display') );
			}
			else if( $display === 'single_after' || $display === 'single_modal' ) {
				add_action( 'woocommerce_after_single_product', array(&$this, 'cross_sells_display') );
			}

			//CART
			add_action( 'fpd_wc_add_to_cart', array(&$this, 'fpd_added_to_cart'), 10, 6 );
			add_filter( 'woocommerce_add_cart_item_data', array(&$this, 'add_cart_item_data'), 10, 2 );

			if( $display === 'cart_before' ) {
				add_action( 'woocommerce_before_cart_table', array(&$this, 'cross_sells_display') );
			}
			else if( $display === 'cart_after' || $display === 'cart_modal' ) {
				add_action( 'woocommerce_after_cart', array(&$this, 'cross_sells_display') );
			}


		}

		public function add_product_form_field( $product_settings ) {

			if( $product_settings->get_option('cross_sells_display') !== 'none' && fpd_get_option('fpd_cross_sells_overlay_image') ):
			?>
			<input type="hidden" value="" name="fpd_cross_sell_image" />
			<?php
			endif;

		}

		//save overlay data uri in cart item for cart display
		public function add_cart_item_data( $cart_item_meta, $product_id ) {

			if( isset($_POST['fpd_cross_sell_image']) )
				$cart_item_meta['fpd_cross_sell_image'] = $_POST['fpd_cross_sell_image'];

		    return $cart_item_meta;

		}

		public function after_product_designer( $product_settings ) {

			global $woocommerce;

			$cross_sell_fpd = null;

			if( isset($_GET['cross_sell_key']) ) {

				$cart = $woocommerce->cart->get_cart();

				$cart_item = $woocommerce->cart->get_cart_item( $_GET['cross_sell_key'] );
				if( !empty($cart_item) ) {

					if( isset($cart_item['fpd_data']) )
						$cross_sell_fpd = stripslashes($cart_item['fpd_data']['fpd_product']);

				}

			}
			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					$selector.on('productCreate', function() {

						var crossSellData = <?php echo is_null($cross_sell_fpd) ? 0 : $cross_sell_fpd; ?>,
							viewCount = 0;

						if(crossSellData) {

							if(crossSellData.product) {

								fancyProductDesigner.toggleSpinner(true);
								crossSellData.product.forEach(function(view, viewIndex) { //loop views

									if(fancyProductDesigner.viewInstances[viewIndex]) {

										var viewElements = [];
										view.elements.forEach(function(element) {

											if(!element.parameters.excludeFromExport) {
												viewElements.push(element);
											}

										})

										fancyProductDesigner.viewInstances[viewIndex].addElements(viewElements, function(viewInstance) {

											viewCount++;
											if(viewCount == fancyProductDesigner.viewInstances.length) {
												fancyProductDesigner.toggleSpinner(false);
											}

										})

									}

								})
							}

							crossSellData = null;

						}

					});

					$cartForm.off('fpdProductSubmit').on('fpdProductSubmit', function() {

						fancyProductDesigner.viewInstances[0].toDataURL(function(dataURL) {

								$cartForm.find('input[name="fpd_cross_sell_image"]').val(dataURL);
								fancyProductDesigner.toggleSpinner(true);
								$cartForm.submit();

							}, 'transparent', {format: 'png', onlyExportable: true})

					});

				});

			</script>
			<?php

		}

		public function fpd_added_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			global $woocommerce;

			$woocommerce->session->set( 'fpd_wc_last_added_cart_item', $cart_item_key );

		}

		public function cross_sells_display() {

			global $woocommerce;

			if( !$woocommerce->session->get( 'fpd_wc_last_added_cart_item' ) )
				return;

			$cart_item_key = $woocommerce->session->get('fpd_wc_last_added_cart_item');
			$woocommerce->session->set( 'fpd_wc_last_added_cart_item', null );
			$cart_item = $woocommerce->cart->get_cart_item( $cart_item_key );

			$source_product_id = $cart_item['product_id'];
			$source_product = wc_get_product( $source_product_id );

			$product_settings = new FPD_Product_Settings( $source_product->get_id() );
			$display = $product_settings->get_option('cross_sells_display');

			if( $display === 'none' )
				return;

			$cross_sells_ids = $source_product->get_cross_sell_ids();
			if( empty($cross_sells_ids) )
				return;

			$overlayImg = '';
			$overlayDataUri = null;

			if( isset($_POST['fpd_cross_sell_image']) ) //single product display
				$overlayDataUri = $_POST['fpd_cross_sell_image'];
			else if( isset($cart_item['fpd_cross_sell_image']) ) //cart display
				$overlayDataUri = $cart_item['fpd_cross_sell_image'];

			if( !empty($overlayDataUri) )
				$overlayImg = '<img src="'.esc_attr( $overlayDataUri ).'" class="fpd-overlay" />';

			$wrapper_css_class = 'fpd-cross-sells';

			if( $display === 'single_modal' || $display === 'cart_modal' )
				echo '<div class="fpd-cross-sells-modal"><div class="fpd-croll-sells-close">&times;</div>';

			echo '<div class="' . $wrapper_css_class . '">';

			echo '<h4>'. esc_html( FPD_Settings_Labels::get_translation('woocommerce', 'cross_sells:headline') ) .'</h4>';
			echo '<div class="fpd-cross-sells-items">';

			foreach( $cross_sells_ids as $cross_sells_id ) {

				$wc_product = wc_get_product($cross_sells_id);
				$permalink = add_query_arg( array(
					'cross_sell_key' => $cart_item_key),
				$wc_product->get_permalink() );

				echo '<a href="'. $permalink .'">';
				echo $wc_product->get_image( 'full' );
				echo $overlayImg;
				echo '<div class="fpd-cross-sell-title">' . $wc_product->get_title() . '</div>';
				echo '</a>';

			}

			echo '</div></div>';

			if( $display === 'single_modal' || $display === 'cart_modal' )
				echo '</div>';

			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					jQuery('.fpd-croll-sells-close').click(function() {
						jQuery(this).parents('.fpd-cross-sells-modal:first').remove();
					})

				})

			</script>
			<style type="text/css">

				.fpd-cross-sells {
					margin: 40px 0;
				}

				.fpd-cross-sells > h4 {
					margin: 0 0 20px;
					font-size: 1.5em;
				}

				.fpd-cross-sells-items {
					display: flex;
				}

				.fpd-cross-sells-items > a {
					margin: 0 10px;
					position: relative;
					flex: 1;
					min-width: 100px;
					text-align: center;
					box-shadow: 0px 1px 3px 0px rgba(0,0,0,0.2);
					transition: all ease-in-out 300ms;
				}

				.fpd-cross-sells-items > a:hover {
					box-shadow: 0px 2px 8px 0px rgba(0,0,0,0.2);
				}

				.fpd-cross-sells-items > a img {
					width: auto;
				}

				.fpd-cross-sells-items > a img.fpd-overlay {
					position: absolute;
					top: 0;
					left: 50%;
					transform: translateX(-50%);
				}

				.fpd-cross-sells-items .fpd-cross-sell-title {
					font-size: 1.2em;
					padding: 5px 10px;
				}

				.fpd-cross-sells-modal {
					position: fixed;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					background: rgba(0,0,0, 0.7);
					z-index: 100000000;
					animation: fadein 500ms;
					transition-delay: 500ms;
				}

				.fpd-cross-sells-modal > .fpd-cross-sells {
					max-width: 960px;
					width: 80%;
					max-height: 100%;
					overflow: auto;
					padding: 20px;
					margin: 0;
					background: #fff;
					border: 1px solid #ccc;
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
				}

				.fpd-croll-sells-close {
					position: absolute;
				    top: 0;
				    right: 20px;
				    color: #fff;
				    font-size: 50px;
				    line-height: 1;
				    cursor: pointer;
				    opacity: 0.8;
				    transition: all ease-in-out 200ms;
				}

				.fpd-croll-sells-close:hover {
					opacity: 1;
				}

				@keyframes fadein {
				    from { opacity: 0; }
				    to   { opacity: 1; }
				}

				@media screen and (max-width: 575px) {

					.fpd-cross-sells {
						margin: 0;
					}

					.fpd-cross-sells-items {
				    	flex-direction: column;
				  	}

				  	.fpd-cross-sells-items > a {
						margin-bottom: 20px;
				  	}

				}

			</style>
			<?php

		}

	}
}

new FPD_WC_Cross_Sells();

?>