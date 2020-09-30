<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_WC_Index') ) {

	class FPD_WC_Index {

		public static $sent_to_admin = false;
		public static $email_id = null;

		public function __construct() {

			require_once( 'class-wc-ajax.php' );
			require_once( 'class-wc-admin-order.php' );
			require_once( 'class-wc-admin-product.php' );
			require_once( 'class-wc-cart.php' );
			require_once( 'class-wc-order.php' );
			require_once( 'class-wc-cross-sells.php' );

			if( class_exists('WeDevs_Dokan') )
				require_once( 'class-wc-dokan.php' );

			add_action( 'init', array( &$this, 'init') );
			add_action( 'woocommerce_email_header', array(&$this, 'email_header'), 10, 2 );
			add_action( 'woocommerce_email_footer', array(&$this, 'email_footer') );

			//will allow data protocol in attributes (maybe not safe)
			//add_filter( 'kses_allowed_protocols', array( &$this, 'allow_data_protocol_urls') );

		}

		public function init() {

			add_rewrite_endpoint( 'saved_products', EP_ROOT | EP_PAGES );

			if( !is_admin() ) {

				require_once(FPD_PLUGIN_DIR.'/woo/class-wc-product.php');

				//CATALOG
				$catalog_button_pos = fpd_get_option('fpd_catalog_button_position');
				if(  $catalog_button_pos == 'fpd-replace-add-to-cart' ) {
					add_filter( 'woocommerce_loop_add_to_cart_link', array(&$this, 'add_to_cart_shop_link'), 10, 2 );
				}
				else {
					add_action( 'woocommerce_after_shop_loop_item', array(&$this, 'add_catalog_customize_button'), 20 );
				}

			}

		}

		//custom add-to-cart button in catalog
		public function add_to_cart_shop_link( $handler, $product ) {

			if( is_fancy_product( $product->get_id() ) ) {

				$product_settings = new FPD_Product_Settings( $product->get_id() );

				return sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="button fpd-catalog-customize product_type_%s">%s</a>',
					esc_url( get_permalink($product->get_id()) ),
					esc_attr( isset( $quantity ) ? $quantity : 1 ),
					esc_attr( $product->get_id() ),
					esc_attr( $product->get_sku() ),
					esc_attr( $product->get_type() ),
					esc_html( $product_settings->get_add_to_cart_text() )
				);
			}

			return $handler;

		}

		//add customize button add the end of catalog item
		public function add_catalog_customize_button() {

			global $product;

			if( is_fancy_product( $product->get_id() ) ) {

				$product_settings = new FPD_Product_Settings( $product->get_id() );

				printf( '<a href="%s" rel="nofollow" class="button fpd-catalog-customize" style="width: 100%%; margin: 10px 0;">%s</a>',
					esc_url( get_permalink($product->get_id()) ),
					esc_html( $product_settings->get_add_to_cart_text() )
				);

			}

		}

		public function email_header( $email_heading, $email=null ) {

			if( !is_null( $email ) && method_exists( $email, 'is_customer_email' ) ) {

				self::$sent_to_admin = !$email->is_customer_email();
				self::$email_id  = $email->id;

			}

		}

		public function email_footer() {

			self::$sent_to_admin = false;
			self::$email_id = null;

		}

		public function allow_data_protocol_urls( $allowed_protocols ) {
			return array_merge( $allowed_protocols, array( 'data' ) );
		}

	}

}

new FPD_WC_Index();

?>