<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_IPS_Image') ) {

	class FPD_IPS_Image {

		public static function get_options() {

			return apply_filters('fpd_ips_image_settings', array(

				array(
					'title' => __('Custom Images & Designs', 'radykal'),
					'type' => 'section-title',
					'id' => 'custom-images-designs-section'
				),

				array(
					'title' 	=> __( 'Colors', 'radykal' ),
					'id' 		=> 'designs_parameter_colors',
					'placeholder'	=> get_option('fpd_designs_parameter_colors'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Color Link Group', 'radykal' ),
					'id' 		=> 'designs_parameter_colorLinkGroup',
					'placeholder'	=> get_option('fpd_designs_parameter_colorLinkGroup'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Price', 'radykal' ),
					'id' 		=> 'designs_parameter_price',
					'placeholder'	=> get_option('fpd_designs_parameter_price'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 0.01
					)
				),

				array(
					'title' 	=> __( 'Layer Depth', 'radykal' ),
					'id' 		=> 'designs_parameter_z',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => -1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Replace', 'radykal' ),
					'id' 		=> 'designs_parameter_replace',
					'placeholder'	=> get_option('fpd_designs_parameter_replace'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Replace In All Views', 'radykal' ),
					'id' 		=> 'designs_parameter_replaceInAllViews',
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
					'id' 		=> 'designs_parameter_bounding_box_control',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => array(
						"0" => __('Custom Bounding Box', 'radykal'),
						"1" => __('Use another element as bounding box', 'radykal'),
					),
				),

				array(
					'title' 	=> __( 'Bounding Box Mode', 'radykal' ),
					'id' 		=> 'designs_parameter_boundingBoxMode',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => FPD_Settings_Default_Element_Options::get_bounding_box_modi()
				),

				array(
					'title' 	=> __( 'Bounding Box Left Position', 'radykal' ),
					'id' 		=> 'designs_parameter_bounding_box_x',
					'placeholder'	=> get_option('fpd_designs_parameter_bounding_box_x'),
					'type' 		=> 'number',
					'class'		=> 'custom-bb',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Top Position', 'radykal' ),
					'id' 		=> 'designs_parameter_bounding_box_y',
					'placeholder'	=> get_option('fpd_designs_parameter_bounding_box_y'),
					'type' 		=> 'number',
					'class'		=> 'custom-bb',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Width', 'radykal' ),
					'id' 		=> 'designs_parameter_bounding_box_width',
					'placeholder'	=> get_option('fpd_designs_parameter_bounding_box_width'),
					'type' 		=> 'number',
					'class'		=> 'custom-bb',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Height', 'radykal' ),
					'id' 		=> 'designs_parameter_bounding_box_height',
					'placeholder'	=> get_option('fpd_designs_parameter_bounding_box_height'),
					'type' 		=> 'number',
					'class'		=> 'custom-bb',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Target', 'radykal' ),
					'id' 		=> 'designs_parameter_bounding_box_by_other',
					'placeholder'	=> get_option('fpd_designs_parameter_bounding_box_by_other'),
					'type' 		=> 'text',
					'class'		=> 'target-bb',
				),

				array(
					'title' => __('Custom Images', 'radykal'),
					'type' => 'section-title',
					'id' => 'custom-images-section'
				),

				array(
					'title' 	=> __( 'Minimum Width', 'radykal' ),
					'id' 		=> 'uploaded_designs_parameter_minW',
					'placeholder'	=> get_option('fpd_uploaded_designs_parameter_minW'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Minimum Height', 'radykal' ),
					'id' 		=> 'uploaded_designs_parameter_minH',
					'placeholder'	=> get_option('fpd_uploaded_designs_parameter_minH'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Maximum Width', 'radykal' ),
					'id' 		=> 'uploaded_designs_parameter_maxW',
					'placeholder'	=> get_option('fpd_uploaded_designs_parameter_maxW'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Maximum Height', 'radykal' ),
					'id' 		=> 'uploaded_designs_parameter_maxH',
					'placeholder'	=> get_option('fpd_uploaded_designs_parameter_maxH'),
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' => 1,
						'step' => 1
					)
				),

				array(
					'title' 	=> __( 'Scale To Width', 'radykal' ),
					'id' 		=> 'uploaded_designs_parameter_resizeToW',
					'placeholder'	=> get_option('fpd_uploaded_designs_parameter_resizeToW'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Scale To Height', 'radykal' ),
					'id' 		=> 'uploaded_designs_parameter_resizeToH',
					'placeholder'	=> get_option('fpd_uploaded_designs_parameter_resizeToH'),
					'type' 		=> 'text',
				),

				array(
					'title' 	=> __( 'Advanced Editing', 'radykal' ),
					'id' 		=> 'uploaded_designs_parameter_advancedEditing',
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
					'title' 	=> __( 'Filter', 'radykal' ),
					'id' 		=> 'uploaded_designs_parameter_filter',
					'default'	=> '',
					'type' 		=> 'select',
					'class'		=> 'semantic-select',
					'allowclear'=> true,
					'options'   => FPD_Settings_Default_Element_Options::get_image_filters()
				),

			));
		}

		public static function get_product_layouts() {


		}

	}

}

?>