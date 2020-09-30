<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_IPS_WC') ) {

	class FPD_IPS_WC {

		public static function get_options() {

			return apply_filters('fpd_ips_wc_settings', array(

				array(
					'title' 	=> __( 'Product Designer Position', 'radykal' ),
					'id' 		=> 'placement',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => FPD_Settings_WooCommerce::get_product_designer_positions()
				),

				array(
					'title' 	=> __( 'Hide Product Image', 'radykal' ),
					'id' 		=> 'hide_product_image',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => array(
						"0" => __('No', 'radykal'),
						"1" => __('Yes', 'radykal'),
					)
				),

				array(
					'title' 	=> __( 'Fullwidth Summary', 'radykal' ),
					'id' 		=> 'fullwidth_summary',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => array(
						"0" => __('No', 'radykal'),
						"1" => __('Yes', 'radykal'),
					)
				),

				array(
					'title' 	=> __( 'Get A Quote', 'radykal' ),
					'id' 		=> 'get_quote',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => array(
						"0" => __('No', 'radykal'),
						"1" => __('Yes', 'radykal'),
					)
				),

				array(
					'title' 	=> __( 'Customize Button: Variation Needed', 'radykal' ),
					'id' 		=> 'wc_customize_variation_needed',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => array(
						"0" => __('No', 'radykal'),
						"1" => __('Yes', 'radykal'),
					)
				),

				array(
					'title' 	=> __( 'Cross-Sells Display', 'radykal' ),
					'id' 		=> 'cross_sells_display',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => array(
						"none" => __('None', 'radykal'),
					)
				),

			));
		}

		public static function get_product_layouts() {


		}

	}

}

?>