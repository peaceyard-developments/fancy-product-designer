<?php
/*
Plugin Name: Fancy Product Designer
Plugin URI: https://fancyproductdesigner.com/
Description: HTML5 Product Designer for Wordpress and WooCommerce. Create and sell customizable products.
Version: 4.4.1
Author: fancyproductdesigner.com
Author URI: https://fancyproductdesigner.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

if (!defined('FPD_PLUGIN_DIR'))
    define( 'FPD_PLUGIN_DIR', dirname(__FILE__) );

if (!defined('FPD_PLUGIN_ROOT_PHP'))
    define( 'FPD_PLUGIN_ROOT_PHP', dirname(__FILE__).'/'.basename(__FILE__)  );

if (!defined('FPD_PLUGIN_ADMIN_DIR'))
    define( 'FPD_PLUGIN_ADMIN_DIR', dirname(__FILE__) . '/admin' );

if (!defined('FPD_WP_CONTENT_DIR'))
	define( 'FPD_WP_CONTENT_DIR', str_replace('\\', '/', WP_CONTENT_DIR) );

if (!defined('FPD_ORDER_DIR'))
    define( 'FPD_ORDER_DIR', FPD_WP_CONTENT_DIR . '/fancy_products_orders/' );

if (!defined('FPD_TEMP_DIR'))
    define( 'FPD_TEMP_DIR', WP_CONTENT_DIR.'/_fpd_temp/' );

if (!defined('FPD_CATEGORIES_TABLE'))
    define( 'FPD_CATEGORIES_TABLE', $wpdb->prefix . 'fpd_categories' );

if (!defined('FPD_PRODUCTS_TABLE'))
    define( 'FPD_PRODUCTS_TABLE', $wpdb->prefix . 'fpd_products' );

if (!defined('FPD_CATEGORY_PRODUCTS_REL_TABLE'))
    define( 'FPD_CATEGORY_PRODUCTS_REL_TABLE', $wpdb->prefix . 'fpd_category_products_rel' );

if (!defined('FPD_VIEWS_TABLE'))
    define( 'FPD_VIEWS_TABLE', $wpdb->prefix . 'fpd_views' );

if (!defined('FPD_TEMPLATES_TABLE'))
    define( 'FPD_TEMPLATES_TABLE', $wpdb->prefix . 'fpd_templates' );

if (!defined('FPD_ORDERS_TABLE'))
    define( 'FPD_ORDERS_TABLE', $wpdb->prefix . 'fpd_orders' );


if( !class_exists('Fancy_Product_Designer') ) {

	class Fancy_Product_Designer {

		const VERSION = '4.4.1';
		const FPD_VERSION = '5.0.9';
		const GOOGLE_API_KEY = 'AIzaSyAkNEOqZSWRG96CbCikDBeNFgRZnlmulGY';
		const CAPABILITY = "edit_fancy_product_desiger";
		const CLOUD_ADMIN_MAIL_URL = 'https://admin.fancyproductdesigner.com/api/';
		const LOCAL = false;
		const BETA = false;
		const REST_API_MIN_VERSION = '1.6.4';
		const MSPC_MIN_VERSION = '1.2.0';

		public function __construct() {

			require_once(FPD_PLUGIN_DIR.'/inc/class-radykal-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/fpd-functions.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-product-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-category.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-product.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-view.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-fonts.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-designs.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-template.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-ui-layouts.php' );
			require_once(FPD_PLUGIN_ADMIN_DIR.'/class-admin.php');
			require_once(FPD_PLUGIN_DIR.'/inc/class-install.php');
			require_once(FPD_PLUGIN_DIR.'/inc/class-scripts-styles.php');
			require_once(FPD_PLUGIN_DIR.'/inc/api/class-shortcode-order.php');
			require_once(FPD_PLUGIN_DIR.'/inc/class-cloud-admin.php');
			require_once(FPD_PLUGIN_DIR.'/inc/class-file-export.php');

			add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
			add_action( 'init', array( &$this, 'init') );

		}

		public function plugins_loaded() {

			load_plugin_textdomain( 'radykal', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

			if ( class_exists( 'WooCommerce' ) )
				require_once(FPD_PLUGIN_DIR.'/woo/class-wc.php');

			if( !is_admin() )
				require_once(FPD_PLUGIN_DIR.'/inc/class-debug.php');

		}

		public function init() {

			require_once( FPD_PLUGIN_DIR.'/inc/class-frontend.php' );

			//register taxonomies
			register_taxonomy( 'fpd_design_category', 'attachment', array(
				'public' => true,
				'show_ui' => false,
				'hierarchical' => true,
				'sort' => true,
				'show_tagcloud' => false,
				'capabilities' => array(self::CAPABILITY),
			));

		}

		public static function get_cloud_admin_api_url() {

			return self::LOCAL ? 'http://localhost:3000/api/' : self::CLOUD_ADMIN_MAIL_URL;

		}
	}
}

new Fancy_Product_Designer();
