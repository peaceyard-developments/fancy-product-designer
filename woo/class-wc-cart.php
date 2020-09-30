<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_WC_Cart')) {

	class FPD_WC_Cart {

		public function __construct() {

			//ADD_TO_CART process
			//add additional [fpd_data]([fpd_product],[fpd_price]) to cart item
			add_filter( 'woocommerce_add_cart_item_data', array(&$this, 'add_cart_item_data'), 10, 2 );
			add_filter( 'woocommerce_add_cart_item', array(&$this, 'add_cart_item'), 10 );
			//handler when a product is added to the cart
			add_action( 'woocommerce_add_to_cart', array( &$this, 'add_product_to_cart'), 10, 6 );

			//IN CART
			//get cart item from session
			add_filter( 'woocommerce_get_cart_item_from_session', array(&$this, 'get_cart_item_from_session'), 10, 2 );
			//add some extra meta data
			add_filter( 'woocommerce_get_item_data', array(&$this, 'get_item_data'), 10, 2 );
			//reset cart item link so the customized product is loaded from the cart
			add_filter( 'woocommerce_cart_item_permalink', array(&$this, 'set_cart_item_permalink'), 100, 3 );
			add_filter( 'woocommerce_cart_item_name', array(&$this, 'reset_cart_item_link'), 100, 3 );

			//change cart item thumbnail
			add_filter( 'woocommerce_cart_item_thumbnail', array(&$this, 'change_cart_item_thumbnail'), 100, 3 );
			add_action( 'woocommerce_after_cart', array(&$this, 'after_cart') );

			//no price when get quote is enabled
			add_filter( 'woocommerce_cart_item_price', array(&$this, 'cart_item_price'), 10, 3 );
			add_filter( 'woocommerce_cart_item_subtotal', array(&$this, 'cart_item_price'), 10, 3 );

			add_filter( 'woocommerce_cart_item_quantity', array(&$this, 'set_quantity_input_field'), 10, 3 );

			//add_action( 'woocommerce_cart_collaterals', array(&$this, 'cross_sells_display'), 20 );

		}

		public function cross_sells_display() {

			?>
			<style type="text/css">

				.fpd-cross-sell-item {
					position: relative;
				}

				.fpd-cross-sell-overlay {
					position: absolute;
					top: 50%;
					left: 50%;
					-webkit-transform:translate(-50%,-50%);
					transform:translate(-50%,-50%)
				}

			</style>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					var $cartItems = jQuery('.cart_item .product-thumbnail img');

					if($cartItems.length) {

						jQuery('.cross-sells').find('.wp-post-image').addClass('fpd-cross-sell-item')
						.after('<img src="'+$cartItems.eq(Math.floor(Math.random()*$cartItems.length)).attr('src')+'" class="fpd-cross-sell-overlay" />');

					}



				});


			</script>
			<?php

		}

		//1 - store values from additional form fields
		public function add_cart_item_data( $cart_item_meta, $product_id ) {

			if( isset($_POST['fpd_product']) ) {

				$cart_item_meta['fpd_data'] = array();
				$cart_item_meta['fpd_data']['fpd_product'] = $_POST['fpd_product'];
				$cart_item_meta['fpd_data']['fpd_remove_cart_item'] = $_POST['fpd_remove_cart_item'];

				if( isset($_POST['fpd_product_price']) )
					$cart_item_meta['fpd_data']['fpd_product_price'] = $_POST['fpd_product_price'];

				if( isset($_POST['fpd_quantity']) && !empty($_POST['fpd_quantity']) )
					$cart_item_meta['fpd_data']['fpd_quantity'] = $_POST['fpd_quantity'];

				if( isset($_POST['fpd_product_thumbnail']) )
					$cart_item_meta['fpd_data']['fpd_product_thumbnail'] = $_POST['fpd_product_thumbnail'];

				if( isset($_POST['fpd_print_order']) )
					$cart_item_meta['fpd_data']['fpd_print_order'] = $_POST['fpd_print_order'];

				//PLUS
				if( isset($_POST['fpd_bulk_variations_order']) && !empty($_POST['fpd_bulk_variations_order']))
					$cart_item_meta['fpd_data']['fpd_bulk_variations_order'] = $_POST['fpd_bulk_variations_order'];

			}

		    return $cart_item_meta;
		}

		//2- set cart item price
		public function add_cart_item( $cart_item ) {

			global $woocommerce;

			//check if data contains a product
	        if ( isset($cart_item['fpd_data']) && $cart_item['fpd_data'] ) {

		        $fpd_data = $cart_item['fpd_data'];
	            if (isset($fpd_data['fpd_product_price'])) {

		            $product = $cart_item['data'];
					$final_price = floatval($fpd_data['fpd_product_price']);

					//prices exclusive of tax
					$tax_rates 		= WC_Tax::get_rates( $product->get_tax_class() );
		           	if( !wc_prices_include_tax() ) {
/*
						$taxes      	= WC_Tax::calc_inclusive_tax( $fpd_data['fpd_product_price'], $tax_rates);
						$tax_amount 	= WC_Tax::get_tax_total( $taxes );
						$final_price 	= $final_price - $tax_amount;
*/
		            }
		            else {
/*
						$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
						$base_taxes   = WC_Tax::calc_tax(  $final_price, $base_tax_rates, true );
						$modded_taxes = WC_Tax::calc_tax(  $final_price - array_sum( $base_taxes ), $tax_rates, false );
						$final_price = round( $final_price + array_sum( $base_taxes )  );
*/
		            }

					//security: check that price can not be lower than wc price
					$final_price = $final_price < $product->get_price() ? $product->get_price() : $final_price;
		            $final_price = apply_filters( 'fpd_wc_cart_item_price', $final_price, $cart_item, $fpd_data );
					$product->set_price($final_price);

					$product_settings = new FPD_Product_Settings( is_a($product, 'WC_Product_Variation') ? $product->get_parent_id() : $product->get_id() );
					if( $product_settings->get_option('get_quote') ) {
						$product->set_price(0);
					}

	            }

	        }

		    return $cart_item;

		}

		//3
		public function add_product_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			if( isset($cart_item_data['fpd_data']) ) {

				global $woocommerce;

				//check if an old cart item exist
				if( !empty($cart_item_data['fpd_data']['fpd_remove_cart_item']) )
					$woocommerce->cart->set_quantity($cart_item_data['fpd_data']['fpd_remove_cart_item'], 0);

				//set quantity via fpd-plus
				if( isset($cart_item_data['fpd_data']['fpd_quantity']) )
					$woocommerce->cart->set_quantity($cart_item_key, intval($cart_item_data['fpd_data']['fpd_quantity']));

				do_action( 'fpd_wc_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );

			}

		}

		public function get_cart_item_from_session( $cart_item, $values ) {

	        //check for fpd data in session
	        if (isset($values['fpd_data'])) {
	            $cart_item['fpd_data'] = $values['fpd_data'];
	        }

			//check if cart item is fancy product
	        if (isset($cart_item['fpd_data'])) {
	        	//add fpd data to cart item
	            $this->add_cart_item($cart_item);
	        }

	        return $cart_item;
	    }

		//meta data displayed after the title, key: value
	    public function get_item_data( $other_data, $cart_item ) {

		    if ( isset($cart_item['fpd_data']) && fpd_get_option('fpd_cart_show_element_props') == 'props' ) {

				//get fpd data
				$fpd_data = $cart_item['fpd_data'];

				if( isset($fpd_data['fpd_product']) && $fpd_data['fpd_product'] ) {

					$order = json_decode(stripslashes($fpd_data['fpd_product']), true);
					if( array_key_exists('product', $order) )
						$views = $order['product'];
					else
						$views = $order; //deprecated: getProduct() as used instead getOrder()

					//display relevant elements with props
					$display_elements = self::get_display_elements( $views );
					foreach($display_elements as $display_element) {

						array_push($other_data, array(
							'name' => '<span style="font-weight: normal;font-size:0.95em;">'.$display_element['title'].'</span>',
							'value' => $display_element['values']
						));

					}

				}

			}

		    return $other_data;

	    }

	    public function set_cart_item_permalink( $permalink, $cart_item=null, $cart_item_key=null ) {

		    if ( !empty($permalink) && $cart_item && isset($cart_item['fpd_data']) && $cart_item['fpd_data'] ) {

				 $permalink = add_query_arg( array('cart_item_key' => $cart_item_key), $permalink );

			}

			return $permalink;

	    }

		public function reset_cart_item_link( $link, $cart_item, $cart_item_key ) {

			if ( !empty($link) && isset($cart_item['fpd_data']) && $cart_item['fpd_data'] ) {

				$url = '';
				//get href from a tag
				$dom = new DOMDocument;
				libxml_use_internal_errors(true);
				$dom->loadHTML( $link );
				$xpath = new DOMXPath( $dom );
				libxml_clear_errors();
				$doc = $dom->getElementsByTagName("a")->item(0);
				$href = $xpath->query(".//@href");
				foreach ( $href as $s ) {
					$url = $s->nodeValue;

				}

				if( !empty($url) ) {

					//set again for WPML
					$url = add_query_arg( array('cart_item_key' => $cart_item_key), $url );

					$link = sprintf( '<a href="%s">%s<br /><i style="opacity: 1; font-size: 0.9em;">%s</i></a>', $url, $cart_item['data']->get_name(), FPD_Settings_Labels::get_translation( 'woocommerce', 'cart:_re-edit product' ) );

				}

				if( fpd_get_option('fpd_cart_show_element_props') === 'used_colors' ) {

					$fpd_data = $cart_item['fpd_data'];
					$order = json_decode(stripslashes($fpd_data['fpd_product']), true);
					if( array_key_exists('product', $order) )
						$views = $order['product'];
					else
						$views = $order; //deprecated: getProduct() as used instead getOrder()

					$link .= '<div style="margin-top:10px;" class="fpd-wc-cart-element-colors fpd-clearfix">'.implode('',self::get_display_elements( $views, 'used_colors' )).'</div>';

				}

			}

			return $link;

		}

		public function change_cart_item_thumbnail( $thumbnail, $cart_item = null ) {

			if( fpd_get_option('fpd_cart_custom_product_thumbnail') && !empty($thumbnail) && !is_null($cart_item) && isset($cart_item['fpd_data']) ) {

				$fpd_data = $cart_item['fpd_data'];

				//check if data contains the fancy product thumbnail
		        if ( isset($fpd_data['fpd_product_thumbnail']) && !empty($fpd_data['fpd_product_thumbnail']) ) {

		        	$dom = new DOMDocument;
					libxml_use_internal_errors(true);
					$dom->loadHTML( $thumbnail );
					$xpath = new DOMXPath( $dom );
					libxml_clear_errors();
					$doc = $dom->getElementsByTagName("img")->item(0);
					$src = $xpath->query(".//@src");
					$srcset = $xpath->query(".//@srcset");
					foreach ( $src as $s ) {
						$s->nodeValue = $fpd_data['fpd_product_thumbnail'];
					}

					foreach ( $srcset as $s ) {
						$s->nodeValue = $fpd_data['fpd_product_thumbnail'];
					}

					return $dom->saveXML( $doc );

		        }

			}

			return $thumbnail;

		}

		public function after_cart() {

			//fix for WC 3.4, because wp_kses_post method is running over thumbnail that removes the data:
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {

					function setImageDataURI() {
						jQuery('.product-thumbnail img').each(function() {

							var $img = jQuery(this);

							if($img.attr('src') && $img.attr('src').substring(0, 5) !== 'data:' && $img.attr('src').substring(0, 4) !== 'http') {
								$img.attr('src', 'data:'+$img.attr('src'))
								.removeAttr('srcset');
							}

						});
					};

					jQuery( document.body ).on( 'updated_wc_div' , setImageDataURI);
					setImageDataURI();

				});
			</script>
			<?php

		}

		public function cart_item_price( $price, $cart_item, $cart_item_key ) {

			if ( isset($cart_item['fpd_data']) && $cart_item['fpd_data'] ) {

				$product = $cart_item['data'];

				$product_settings = new FPD_Product_Settings( is_a($product, 'WC_Product_Variation') ? $product->get_parent_id() : $product->get_id() );
				if( $product_settings->get_option('get_quote') ) {
					return '-';
				}

			}

			return $price;

		}

		//add custom quantity display to cart for fpd quantity
		public function set_quantity_input_field( $product_quantity, $cart_item_key, $cart_item=null ) {

		    if( $cart_item && isset($cart_item['fpd_data']) && isset($cart_item['fpd_data']['fpd_quantity']) ) {

				$product_quantity = '<div class="quantity"><span class="fpd-quantity">'.$cart_item['fpd_data']['fpd_quantity'].'</span></div>';

		    }

		    return $product_quantity;

		}

		public static function get_display_elements( $views, $format=1 ) {

			$display_elements = array();

			foreach($views as $view) {

				$viewElements = $view['elements'];
				foreach($viewElements as $viewElement) {

					$elementParams = $viewElement['parameters'];
					if( isset($elementParams['isEditable']) && @$elementParams['isEditable'] ) {

						$values = array();

						//check if fill is set and if yes, look for a hex name
						if( isset($elementParams['fill']) && @$elementParams['fill'] && is_string($elementParams['fill']) ) {

							$element_colors = isset($elementParams['svgFill']) ? $elementParams['svgFill'] : array($elementParams['fill']);
							$color_display = self::color_display( $element_colors );

							if( !empty($color_display) ) {

								//show only used colors
								if( $format === 'used_colors' ) {
									$display_elements[] = $color_display;
									continue;
								}

								array_push($values, $color_display);

							}

						}

						//get font family and text size
						if( $format !== 'used_colors' && isset($elementParams['fontFamily']) && @$elementParams['fontFamily'] )
							array_push($values, $elementParams['fontFamily'].', '.intval($elementParams['fontSize']).'px' );

						if( sizeof($values) > 0 ) {

							$title = isset($elementParams['text']) ? $elementParams['text'] : $viewElement['title'];
							$display_elements[] = array(
								'title' => strlen($title) > 20  ? substr($title, 0, 17) . '...' : $title,
								'values' => implode(' ', $values)
							);

						}

					}

				}

			}

			return $display_elements;

		}

		public static function color_display( $color_arr ) {

			$color_html = '<div style="float: left;">';
			foreach($color_arr as $color_value) {

				$hex =  strtolower(str_replace('#', '', $color_value));
				$hex_name = fpd_get_hex_name($hex);
				$hex_title = empty($hex_name) ? '#'.$hex : $hex_name. ' - #'.$hex;

				$hex_title = apply_filters( 'fpd_wc_cart_item_color_name', $hex_title, $hex, empty($hex_name) ? null : $hex_name);

				if( !empty($hex_title) ) {

					$color_contrast = fpd_get_contrast_color($color_value);
					$color_html .= '<span class="fpd-cart-element-color" style="white-space: nowrap; border:1px solid #f2f2f2;font-size:11px;margin-right:4px;padding:2px 3px;background: '.$color_value.'; color:'.$color_contrast.'">'.strtoupper($hex_title).'</span>';

				}


			}

			$color_html .= '</div>';

			return $color_html;

		}

	}
}

new FPD_WC_Cart();

?>