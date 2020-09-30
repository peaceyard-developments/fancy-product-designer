<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings_Default_Element_Options') ) {

	class FPD_Settings_Default_Element_Options {

		public static function get_options() {

			return apply_filters('fpd_default_element_options_settings', array(

				'images' => array(

					array(
						'title' => __( 'Left', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_x',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Top', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_y',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Layer Depth', 'radykal' ),
						'description' 		=> __( '-1 means that the element will be added at the top. Any value higher than that will add the element to that layer depth.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_z',
						'css' 		=> 'width:70px;',
						'default'	=> '-1',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> -1,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Minimum Scale Limit', 'radykal' ),
						'description' 		=> __( 'The minimum allowed scale value.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_minScaleLimit',
						'css' 		=> 'width:70px;',
						'default'	=> '0.01',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0.01,
							'step' 	=> 0.01
						)
					),

					array(
						'title' => __( 'Colors', 'radykal' ),
						'description' 		=> __( 'The available colors the user can choose from. Example: #000,#fff', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_colors',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'multi-color-input'
					),

					array(
						'title' => __( 'Color Link Group', 'radykal' ),
						'description' 		=> __( 'You can set color links between elements.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_colorLinkGroup',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Price', 'radykal' ),
						'description' 		=> __( 'Enter a price that will be charged when this image element is added. Use always a dot as decimal separator!', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_price',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'text',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Replace', 'radykal' ),
						'description' 		=> __( 'Elements with the same replace name will replace each other.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_replace',
						'css' 		=> 'width:150px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Replace In All Views', 'radykal' ),
						'description' 		=> __( 'Replace images with the same replace value in all views.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_replaceInAllViews',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Auto-Center', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_autoCenter',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Draggable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_draggable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Rotatable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_rotatable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Resizable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_resizable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Layer Depth Changeable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_zChangeable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Auto-Select', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_autoSelect',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Stay On Top', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_topped',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Allow Unproportional Scaling', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_uniScalingUnlockable',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Use another element as bounding box?', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_control',
						'class'		=> 'fpd-bounding-box-control',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
						'relations' =>  array(
							'fpd_designs_parameter_bounding_box_by_other' => true,
							'fpd_designs_parameter_bounding_box_x' => false,
							'fpd_designs_parameter_bounding_box_y' => false,
							'fpd_designs_parameter_bounding_box_width' => false,
							'fpd_designs_parameter_bounding_box_height' => false,
						)

					),

					array(
						'title' 	=> __( 'Bounding Box Target', 'radykal' ),
						'description' 		=> __( 'Enter the title of another element that should be used as bounding box for design elements.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_by_other',
						'css' 		=> 'width:150px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title'		=> __( 'Bounding Box Left Position', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_x',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Top Position', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_y',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Width', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_width',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Height', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_height',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Bounding Box Mode', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_boundingBoxMode',
						'default'	=> 'clipping',
						'type' 		=> 'select',
						'options'	=>  array(
							'inside' => __('Inside', 'radyal'),
							'clipping' => __('Clipping', 'radyal'),
							'limitModify' => __('Limit Modification', 'radyal'),
							'none' => __('None', 'radyal'),
						)
					),

					array(
						'title' => __( 'Scale Mode', 'radykal' ),
						'description' 		=> __( 'When the image is added, scales the image to fit into or to cover the bounding box.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_scaleMode',
						'default'	=> 'fit',
						'type' 		=> 'select',
						'options'	=>  array(
							'fit' => __('Fit', 'radyal'),
							'cover' => __('Cover', 'radyal')
						)
					),

					array(
						'title' 	=> __( 'Patterns', 'radykal' ),
						'description' 		=> __( 'Upload PNG or JPEG into wp-content/uploads/fpd_patterns_svg.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_patterns',
						'css' 		=> 	'width: 100%;',
						'default'	=> '',
						'type' 		=> 'multiselect',
						'options'	=> self::get_pattern_urls('svg')

					),

				), //default image options

				'custom-images' => array(

					array(
						'title' 	=> __( 'Minimum Width', 'radykal' ),
						'description' 		=> __( 'The minimum image width.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_minW',
						'css' 		=> 'width:70px;',
						'default'	=> '100',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Minimum Height', 'radykal' ),
						'description' 		=> __( 'The minimum image height.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_minH',
						'css' 		=> 'width:70px;',
						'default'	=> '100',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Maximum Width', 'radykal' ),
						'description' 		=> __( 'The maximum image width.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_maxW',
						'css' 		=> 'width:70px;',
						'default'	=> '10000',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Maximum Height', 'radykal' ),
						'description' 		=> __( 'The maximum image height.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_maxH',
						'css' 		=> 'width:70px;',
						'default'	=> '10000',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Scale To Width', 'radykal' ),
						'description' 		=> __( 'Scale the uploaded image to this width, when width is larger than height. Enter a pixel value (e.g. 400) or percentage value (e.g. 80%), that will scale relative to canvas width.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_resizeToW',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'text',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Scale To Height', 'radykal' ),
						'description' 		=> __( 'Scale the uploaded image to this height, when height is larger than width. Enter a pixel value or percentage value, that will scale relative to canvas height.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_resizeToH',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'text',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Maximum Image Size (MB)', 'radykal' ),
						'description' 		=> __( 'The maximum image size in Megabytes.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_maxSize',
						'css' 		=> 'width:70px;',
						'default'	=> '10',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Minimum JPEG DPI', 'radykal' ),
						'description' 		=> __( 'The minimum allowed DPI for JPEG images.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_minDPI',
						'css' 		=> 'width:70px;',
						'default'	=> '72',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Minimum Scale Limit', 'radykal' ),
						'description' 		=> __( 'The minimum allowed scale value.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_minScaleLimit',
						'css' 		=> 'width:70px;',
						'default'	=> '0.01',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0.01,
							'step' 	=> 0.01
						)
					),

					array(
						'title' => __( 'Advanced Editing', 'radykal' ),
						'description' 		=> __( 'The advanced image editor will be enabled, the user has the possibility to set a custom mask or to manipulate the image colors.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_advancedEditing',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' 	=> __( 'Filter', 'radykal' ),
						'description' 		=> __( 'Set a filter when the image is added (Only JPEG or PNG).', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_filter',
						'css' 		=> 	'width: 200px;',
						'default'	=> '',
						'type' 		=> 'select',
						'options'	=> self::get_image_filters()

					),

				), //default custom images

				'all-images' => array(
					array(
						'title' => __( 'Colors', 'radykal' ),
						'description' 		=> __( 'The available colors the user can choose from. Example: #000,#fff', 'radykal' ),
						'id' 		=> 'fpd_all_image_colors',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'multi-color-input'
					),

					array(
						'title' => __( 'Color Link Group', 'radykal' ),
						'description' 		=> __( 'You can set color links between elements.', 'radykal' ),
						'id' 		=> 'fpd_all_image_colorLinkGroup',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'text'
					),
				),// all images

				'custom-texts' => array(

					array(
						'title' => __( 'Left', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_x',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Top', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_y',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Layer Depth', 'radykal' ),
						'description' 		=> __( '-1 means that the element will be added at the top. Any value higher than that will add the element to that layer depth.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_z',
						'css' 		=> 'width:70px;',
						'default'	=> '-1',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> -1,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Default Color', 'radykal' ),
						'description' 		=> __( 'The default color for custom added text elements.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_fill',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Colors', 'radykal' ),
						'description' 		=> __( 'The available colors the user can choose from. Example: #000,#fff', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_colors',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'multi-color-input'
					),

					array(
						'title' => __( 'Color Link Group', 'radykal' ),
						'description' 		=> __( 'You can set color links between elements.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_colorLinkGroup',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Price', 'radykal' ),
						'description' 		=> __( 'Enter the additional price for a text element. Always use a dot as decimal separator!', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_price',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'text',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Replace', 'radykal' ),
						'description' 		=> __( 'Elements with the same replace name will replace each other.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_replace',
						'css' 		=> 'width:150px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Auto-Center', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_autoCenter',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Draggable', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_draggable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Rotatable', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_rotatable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Resizable', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_resizable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Layer Depth Changeable', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_zChangeable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Auto-Select', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_autoSelect',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Stay On Top', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_topped',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Allow Unproportional Scaling', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_uniScalingUnlockable',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					/*array(
						'title' => __( 'Patternable', 'radykal' ),
						'description' 		=> __( 'Let the customer choose a pattern?', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_patternable',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),*/

					array(
						'title' => __( 'Curvable', 'radykal' ),
						'description' 		=> __( 'Let the customer make the text curved?', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_curvable',
						'default'	=> 'yes',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Curve Spacing', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_curveSpacing',
						'default'	=> 10,
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 1,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Curve Radius', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_curveRadius',
						'default'	=> 80,
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 1,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Curve Reverse', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_curveReverse',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Use another element as bounding box?', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_control',
						'class'		=> 'fpd-bounding-box-control',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
						'relations' =>  array(
							'fpd_custom_texts_parameter_bounding_box_by_other' => true,
							'fpd_custom_texts_parameter_bounding_box_x' => false,
							'fpd_custom_texts_parameter_bounding_box_y' => false,
							'fpd_custom_texts_parameter_bounding_box_width' => false,
							'fpd_custom_texts_parameter_bounding_box_height' => false,
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Target', 'radykal' ),
						'description' 		=> __( 'Enter the title of another element that should be used as bounding box for design elements.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_by_other',
						'css' 		=> 'width:150px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title'		=> __( 'Bounding Box Left Position', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_x',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Top Position', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_y',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Width', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_width',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Height', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_height',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Bounding Box Mode', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_boundingBoxMode',
						'default'	=> 'clipping',
						'type' 		=> 'select',
						'options'	=>  array(
							'inside' => __('Inside', 'radyal'),
							'clipping' => __('Clipping', 'radyal'),
							'limitModify' => __('Limit Modification', 'radyal'),
							'none' => __('None', 'radyal'),
						)
					),

					array(
						'title' => __( 'Default Font Size', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_textSize',
						'css' 		=> 'width:70px;',
						'default'	=> '18',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Minimum Font Size', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_minFontSize',
						'css' 		=> 'width:70px;',
						'default'	=> '1',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Maximum Font Size', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_maxFontSize',
						'css' 		=> 'width:70px;',
						'default'	=> '1000',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Font Size To Width', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_widthFontSize',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Maximum Characters', 'radykal' ),
						'description' 		=> __( 'You can limit the number of characters. 0 means unlimited characters.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_maxLength',
						'css' 		=> 'width:70px;',
						'default'	=> 0,
						'type' 		=> 'number'
					),

					array(
						'title' 	=> __( 'Maximum Lines', 'radykal' ),
						'description' 		=> __( 'You can limit the number of lines. 0 means unlimited lines.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_maxLines',
						'css' 		=> 'width:70px;',
						'default'	=> 0,
						'type' 		=> 'number'
					),

					array(
						'title' 	=> __( 'Alignment', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_textAlign',
						'css' 		=> 'min-width:350px;',
						'default'	=> 'left',
						'type' 		=> 'radio',
						'options'   => array(
							'left' => __( 'Left', 'radykal' ),
							'center' => __( 'Center', 'radykal' ),
							'right' => __( 'Right', 'radykal' )
						)
					),

					array(
						'title' => __( 'Text Link Group', 'radykal' ),
						'description' 		=> __( 'Changing the text of one element will also change the text of the other elements with the same same "Text Link Group".', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_textLinkGroup',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

				), //default text options

				'all-texts' => array(

					array(
						'title' => __( 'Colors', 'radykal' ),
						'description' 		=> __( 'The available colors the user can choose from. Example: #000,#fff', 'radykal' ),
						'id' 		=> 'fpd_all_text_colors',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'multi-color-input'
					),

					array(
						'title' => __( 'Stroke Colors', 'radykal' ),
						'description' 		=> __( 'Define a color palette for the text stroke (e.g. #000000,#FFFFFF). By default a color wheel is displaying.', 'radykal' ),
						'id' 		=> 'fpd_all_text_strokeColors',
						'default'	=> '',
						'class'		=> 'widefat',
						'type' 		=> 'multi-color-input'
					),

					array(
						'title' => __( 'Default Font', 'radykal' ),
						'description' 		=> __( 'Enter the default font. If you leave it empty, the first font from the fonts dropdown will be used.', 'radykal' ),
						'id' 		=> 'fpd_font',
						'default'	=> '',
						'type' 		=> 'select',
						'css'		=> 'width: 200px',
						'options'   => self::get_fonts_options()
					),

					array(
						'title' 	=> __( 'Patterns', 'radykal' ),
						'description' 		=> __( 'Upload PNG or JPEG into wp-content/uploads/fpd_patterns_text.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_patterns',
						'css' 		=> 	'width: 100%;',
						'default'	=> '',
						'type' 		=> 'multiselect',
						'options'	=> self::get_pattern_urls()

					),

					array(
						'title' => __( 'Text Control Padding', 'radykal' ),
						'description' 		=> __( 'The padding of the corner controls when a text element is selected.', 'radykal' ),
						'id' 		=> 'fpd_padding_controls',
						'css' 		=> 'width:60px;',
						'default'	=> '10',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Replace In All Views', 'radykal' ),
						'description' 		=> __( 'Replace text elements with the same replace value in all views?', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_replaceInAllViews',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

				), //all texts

				'general' => array(

					array(
						'title' => __( 'Origin-X Point', 'radykal' ),
						'id' 		=> 'fpd_common_parameter_originX',
						'css' 		=> 'min-width:350px;',
						'default'	=> 'center',
						'type' 		=> 'radio',
						'options'   => array(
							'center'	 => __( 'Center', 'radykal' ),
							'left' => __( 'Left', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Origin-Y Point', 'radykal' ),
						'id' 		=> 'fpd_common_parameter_originY',
						'css' 		=> 'min-width:350px;',
						'default'	=> 'center',
						'type' 		=> 'radio',
						'options'   => array(
							'center'	 => __( 'Center', 'radykal' ),
							'top' => __( 'Top', 'radykal' ),
						)
					),

				), //default general options


			));
		}

		public static function get_bounding_box_modi() {

			return array(
				'inside'	 	=> __( 'Inside', 'radykal' ),
				'clipping'	 	=> __( 'Clipping', 'radykal' ),
				'limitModify'	=> __( 'Limit Modification', 'radykal' ),
				'none'	 		=> __( 'None', 'radykal' ),
			);

		}

		public static function get_pattern_urls($type='text') {

			$urls = array();

			$path = FPD_WP_CONTENT_DIR . '/uploads/fpd_patterns_'.$type.'/';

			if( file_exists($path) ) {

			  	$folder = opendir($path);

				$pic_types = array("jpg", "jpeg", "png");

				while ($file = readdir ($folder)) {

				  if(in_array(substr(strtolower($file), strrpos($file,".") + 1),$pic_types)) {
					  $urls[content_url('/uploads/fpd_patterns_'.$type.'/'.$file, FPD_PLUGIN_ROOT_PHP )] = $file;
				  }
				}

				closedir($folder);
			}

			return $urls;

		}

		public static function get_image_filters() {

			return array(
				'none' => 'None',
				'grayscale' => 'Grayscale',
				'sepia' => 'Sepia',
				'sepia2' => 'Sepia Two',
				'cold' => 'Cold',
				'black_white' => 'Black & White',
				'old' => 'Old',
				'milk' => 'Milk',
				'vintage' => 'Vintage',
				'kodachrome' => 'Kodachrome',
				'technicolor' => 'Technicolor',
				'monochrome' => 'Monochrome'
			);

		}

		public static function get_fonts_options() {

			$fonts_options = array();

			foreach( FPD_Fonts::get_enabled_fonts() as $font ) {
				$fonts_options[$font] = $font;
			}

			return $fonts_options;

		}

	}

}