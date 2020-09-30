<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_IPS_General') ) {

	class FPD_IPS_General {

		public static function get_options() {

			return apply_filters('fpd_ips_general_settings', array(

				array(
					'title' 	=> __( 'Main User Interface', 'radykal' ),
					'id' 		=> 'product_designer_ui_layout',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => FPD_Settings_General::get_saved_ui_layouts()
				),

				array(
					'title' 	=> __( 'Open Product Designer in...', 'radykal' ),
					'id' 		=> 'product_designer_visibility',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => FPD_Settings_General::get_product_designer_visibilities()
				),

				array(
					'title' 	=> __( 'Main Bar Position', 'radykal' ),
					'id' 		=> 'main_bar_position',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => FPD_Settings_General::get_main_bar_positions()
				),


				array(
					'title' 	=> __( 'Design Categories', 'radykal' ),
					'placeholder' => __( 'All Design Categories', 'radykal' ),
					'id' 		=> 'design_categories',
					'default'	=> '',
					'type' 		=> 'multiselect',
					'class'		=> 'semantic-select',
					'options'   => fpd_output_top_level_design_cat_options(false, true)
				),

				array(
					'title' 	=> __( 'Available Fonts', 'radykal' ),
					'placeholder' => __( 'All Fonts', 'radykal' ),
					'id' 		=> 'font_families',
					'default'	=> '',
					'type' 		=> 'multiselect',
					'class'		=> 'semantic-select',
					'options'   => self::get_fonts_options()
				),

				array(
					'title' 	=> __( 'Background', 'radykal' ),
					'id' 		=> 'background_type',
					'default'	=> 'image',
					'type' 		=> 'radio',
					'options'   => array(
						'image' => __('Image', 'radykal'),
						'color' => __('Color', 'radykal')
					),
					'relations' => array(
						'image' => array(
							'background_image' => true,
							'background_color' => false
						),
						'color' => array(
							'background_image' => false,
							'background_color' => true
						)
					)
				),

				array(
					'title' 	=> __( 'Background Image', 'radykal' ),
					'id' 		=> 'background_image',
					'default'	=> plugins_url( '/assets/img/grid.png', FPD_PLUGIN_ROOT_PHP ),
					'type' 		=> 'upload',
				),

				array(
					'title' 	=> __( 'Background Color', 'radykal' ),
					'id' 		=> 'background_color',
					'default'	=> '#ffffff',
					'type' 		=> 'colorpicker',
				),

				array(
					'title' 	=> __( 'Replace Initial Elements', 'radykal' ),
					'id' 		=> 'replace_initial_elements',
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
					'title' 	=> __( 'Color Prices for Images', 'radykal' ),
					'id' 		=> 'enable_image_color_prices',
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
					'title' 	=> __( 'Color Prices for Texts', 'radykal' ),
					'id' 		=> 'enable_text_color_prices',
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
					'title' 	=> __( 'Hide Dialog On Add', 'radykal' ),
					'id' 		=> 'hide_dialog_on_add',
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
					'title' 	=> __( 'Customization Required', 'radykal' ),
					'id' 		=> 'customization_required',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => array(
						"none" => __('None', 'radykal'),
						"any" => __('ANY view needs to be customized.', 'radykal'),
						"all" => __('ALL views needs to be customized.', 'radykal'),
					)
				),

				array(
					'title' 	=> __( 'Layouts', 'radykal' ),
					'id' 		=> 'layouts',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => self::get_product_layouts()
				),


			));
		}

		public static function get_fonts_options() {

			$fonts_options = array();

			foreach(FPD_Fonts::get_enabled_fonts()  as $key => $font) {
				$fonts_options[$font] = $font;
			}

			return $fonts_options;

		}

		public static function get_product_layouts() {

			$fpd_products = FPD_Product::get_products( array(
				'cols' => "ID, title",
				'order_by' 	=> "ID ASC",
			) );

			$layouts_options = array(
				'' => __( 'None', 'radykal' )
			);

			foreach($fpd_products as $fpd_product) {

				$layouts_options[$fpd_product->ID] = '#'.$fpd_product->ID.' - '.$fpd_product->title;

			}

			return $layouts_options;

		}

	}

}

?>