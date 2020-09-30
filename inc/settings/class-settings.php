<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings') ) {

	class FPD_Settings {

		public static $radykal_settings;

		public function __construct() {

			add_action( 'init', array( &$this, 'init') );

		}

		public function init() {

			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-general-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-default-element-options-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-labels-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-fonts-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-color-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-advanced-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-wc-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-automated-export-settings.php');

			//create new settings instance
			$tabs = array(
				'general' => __('General', 'radykal'),
				'element-properties' => __('Element Properties', 'radykal'),
				'colors' => __('Colors', 'radykal'),
				'fonts' => __('Fonts', 'radykal'),
				'labels' => __('Labels', 'radykal'),
			);

			if( function_exists('get_woocommerce_currency') ) {
				$tabs['woocommerce'] = __('WooCommerce', 'radykal');
			}

			$tabs['automated-export'] = __('Automated Export', 'radykal');
			$tabs['advanced'] = __('Advanced', 'radykal');

			$tabs = apply_filters( 'fpd_settings_tabs', $tabs );

			self::$radykal_settings = new Radykal_Settings( array(
					'page_id' => 'fpd_settings',
					'tabs' => $tabs
				)
			);

			//first add blocks
			$blocks = apply_filters('fpd_settings_blocks', array(
				'general' => array(
					'display' => __('Display', 'radykal'),
					'modules' => __('Modules', 'radykal'),
					'actions' => __('Actions', 'radykal'),
					'social-share' => __('Social Share', 'radykal'),
				),
				'element-properties' => array(
					'images' => __('Custom Images & Designs', 'radykal'),
					'custom-images' => __('Custom Images', 'radykal'),
					'all-images' => __('All Images', 'radykal'),
					'custom-texts' => __('Custom Texts', 'radykal'),
					'all-texts' => __('All Texts', 'radykal'),
					'general' => __('General', 'radykal'),
				),
				'fonts' => array(
					'fonts' => __('Fonts for the typeface dropdown', 'radykal'),
				),
				'colors' => array(
					'color-names' => __('Color Names', 'radykal'),
					'color-prices' => __('Color Prices', 'radykal'),
					'color-general' => __('General', 'radykal'),
				),
				'woocommerce' => array(
					'product-page' => __('Product Page', 'radykal'),
					'cart' => __('Cart', 'radykal'),
					'order' => __('Order', 'radykal'),
					'catalog-listing' => __('Catalog Listing', 'radykal'),
					'global-product-designer' => __('Global Product Designer', 'radykal'),
					'cross-sells' => __('Cross-Sells', 'radykal'),
				),
				'automated-export' => array(
					'ae-general' => __('Automated-export', 'radykal'),
				),
				'advanced' => array(
					'misc' => __('Miscellanous', 'radykal'),
					'troubleshooting' => __('Troubleshooting', 'radykal'),
				),
			));

			self::$radykal_settings->add_blocks($blocks);

			self::$radykal_settings->add_blocks_description(array(
				'images' 		=> __('Default properties for custom images added by the customers and designs added from the Design module.', 'radykal'),
				'custom-images' => __('Additional properties for custom images (Uploads, Facebook, Instagram, Pixabay, Depositphotos) added by the customers.', 'radykal'),
				'all-images' 	=> __('Additional properties for all image layers.', 'radykal'),
				'custom-texts'	=> __('Default properties for texts added by the customers.', 'radykal'),
				'all-texts' 	=> __('Additional properties for all text layers.', 'radykal'),
				'cross-sells' 	=> __('Whenever a customer adds a product to the cart, the linked Cross-Sells products will be displayed.', 'radykal'),
			));

			//add general settings
			$general_options = FPD_Settings_General::get_options();
			self::$radykal_settings->add_block_options( 'display', $general_options['display']);
			self::$radykal_settings->add_block_options( 'modules', $general_options['modules']);
			self::$radykal_settings->add_block_options( 'actions', $general_options['actions']);
			self::$radykal_settings->add_block_options( 'social-share', $general_options['social-share']);

			//add default element options settings
			$element_properties = FPD_Settings_Default_Element_Options::get_options();
			self::$radykal_settings->add_block_options( 'images', $element_properties['images']);
			self::$radykal_settings->add_block_options( 'custom-images', $element_properties['custom-images']);
			self::$radykal_settings->add_block_options( 'all-images', $element_properties['all-images']);
			self::$radykal_settings->add_block_options( 'custom-texts', $element_properties['custom-texts']);
			self::$radykal_settings->add_block_options( 'all-texts', $element_properties['all-texts']);
			self::$radykal_settings->add_block_options( 'general', $element_properties['general']);

			//add fonts settings
			$fonts_options = FPD_Settings_Fonts::get_options();
			self::$radykal_settings->add_block_options( 'fonts', $fonts_options['fonts']);

			//add advanced color settings
			$advanced_color_options = FPD_Settings_Colors::get_options();
			self::$radykal_settings->add_block_options( 'color-names', $advanced_color_options['color-names']);
			self::$radykal_settings->add_block_options( 'color-prices', $advanced_color_options['color-prices']);
			self::$radykal_settings->add_block_options( 'color-general', $advanced_color_options['color-general']);

			//add wc settings
			$wc_options = FPD_Settings_WooCommerce::get_options();
			self::$radykal_settings->add_block_options( 'product-page', $wc_options['product-page']);
			self::$radykal_settings->add_block_options( 'cart', $wc_options['cart']);
			self::$radykal_settings->add_block_options( 'order', $wc_options['order']);
			self::$radykal_settings->add_block_options( 'catalog-listing', $wc_options['catalog-listing']);
			self::$radykal_settings->add_block_options( 'global-product-designer', $wc_options['global-product-designer']);
			self::$radykal_settings->add_block_options( 'cross-sells', $wc_options['cross-sells']);

			//add automated export settings
			$automated_export_options = FPD_Settings_Automated_Export::get_options();
			self::$radykal_settings->add_block_options( 'ae-general', $automated_export_options['ae-general']);

			//add advanced settings
			$advanced_options = FPD_Settings_Advanced::get_options();
			self::$radykal_settings->add_block_options( 'misc', $advanced_options['misc']);
			self::$radykal_settings->add_block_options( 'troubleshooting', $advanced_options['troubleshooting']);

			//$fonts_options = FPD_Settings_Fonts::get_options();
			self::$radykal_settings->add_block_options( 'plus', $fonts_options['fonts']);

			do_action( 'fpd_block_options_end' );

		}
	}
}

new FPD_Settings();
?>