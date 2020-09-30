<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_IPS_Text') ) {

	class FPD_IPS_Text {

		public static function get_options() {

			return apply_filters('fpd_ips_text_settings', array(

				array(
					'title' 	=> __( 'Colors', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_colors',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_colors'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Color Link Group', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_colorLinkGroup',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_colorLinkGroup'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Default Color', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_fill',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_fill'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Price', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_price',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_price'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 0.01
					)
				),

				array(
					'title' 	=> __( 'Layer Depth', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_z',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_z'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => -1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Replace', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_replace',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_replace'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Replace In All Views', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_replaceInAllViews',
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
					'title' 	=> __( 'Bounding Box', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_bounding_box_control',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => array(
						"0" => __('Custom Bounding Box', 'radykal'),
						"1" => __('Use another element as bounding box', 'radykal'),
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Mode', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_boundingBoxMode',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => FPD_Settings_Default_Element_Options::get_bounding_box_modi()
				),

				array(
					'title' 	=> __( 'Bounding Box Left Position', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_bounding_box_x',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_bounding_box_x'),
					'type' 		=> 'number',
					'class'		=> 'custom-bb',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Top Position', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_bounding_box_y',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_bounding_box_y'),
					'type' 		=> 'number',
					'class'		=> 'custom-bb',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Width', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_bounding_box_width',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_bounding_box_width'),
					'type' 		=> 'number',
					'class'		=> 'custom-bb',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Height', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_bounding_box_height',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_bounding_box_height'),
					'type' 		=> 'number',
					'class'		=> 'custom-bb',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Target', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_bounding_box_by_other',
					'placeholder'	=> get_option('fpd_custom_texts_parameter_bounding_box_by_other'),
					'type' 		=> 'text',
					'class'		=> 'target-bb',
				),

				array(
					'title' 	=> __( 'Default Font Size', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_textSize',
					'placeholder'	=> fpd_get_option('fpd_custom_texts_parameter_textSize'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Minimum Font Size', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_minFontSize',
					'placeholder'	=> fpd_get_option('fpd_custom_texts_parameter_minFontSize'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Maximum Font Size', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_maxFontSize',
					'placeholder'	=> fpd_get_option('fpd_custom_texts_parameter_maxFontSize'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Maximum Characters', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_maxLength',
					'placeholder'	=> fpd_get_option('fpd_custom_texts_parameter_maxLength'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Maximum Lines', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_maxLines',
					'placeholder'	=> fpd_get_option('fpd_custom_texts_parameter_maxLines'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Text Link Group', 'radykal' ),
					'id' 		=> 'custom_texts_parameter_textLinkGroup',
					'placeholder'	=> fpd_get_option('fpd_custom_texts_parameter_textLinkGroup'),
					'type' 		=> 'text',
				),

			));
		}

		public static function get_product_layouts() {


		}

	}

}

?>