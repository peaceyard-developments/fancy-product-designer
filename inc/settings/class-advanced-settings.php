<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Settings_Advanced') ) {

	class FPD_Settings_Advanced {

		public static function get_options() {

			return apply_filters('fpd_advanced_settings', array(

				'misc' => array(

					array(
						'title' 	=> __( 'Customization Required', 'radykal' ),
						'description' 		=> __( 'The user must customize any or all views of a product in order to proceed.', 'radykal' ),
						'id' 		=> 'fpd_customization_required',
						'default'	=> 'none',
						'type' 		=> 'radio',
						'options'   => array(
							'none'	 => __( 'None', 'radykal' ),
							'any'	 => __( 'ANY view needs to be customized.', 'radykal' ),
							'all'	 => __( 'ALL views needs to be customized.', 'radykal' ),
						)
					),

					array(
						'title' 	=> __( 'Toolbar Textarea Position', 'radykal' ),
						'description' 		=> __( 'The position of the textarea in the toolbar for editing text elements.', 'radykal' ),
						'id' 		=> 'fpd_toolbarTextareaPosition',
						'default'	=> 'sub',
						'type' 		=> 'radio',
						'options'   => array(
							'sub'	 => __( 'Sub-Panel: Textarea opens via button in toolbar.', 'radykal' ),
							'top'	 => __( 'Top-Level: Textarea is at the top when toolbar opens.', 'radykal' ),
						)
					),

					array(
						'title' 	=> __( 'Text Link Group Properties', 'radykal' ),
						'description' 		=> __( 'Define additional properties that will be applied to all elements in the same "Text Link Group", when one element in this group is changing.', 'radykal' ),
						'id' 		=> 'fpd_textLinkGroupProps',
						'css' 		=> 	'width: 100%;',
						'default'	=> array(),
						'type' 		=> 'multiselect',
						'options'	=> array(
							'fontFamily' => __( 'Font Family', 'radykal' ),
							'fontSize' => __( 'Font Size', 'radykal' ),
							'lineHeight' => __( 'Line Height', 'radykal' ),
							'letterSpacing' => __( 'Letter Spacing', 'radykal' ),
							'fontStyle' => __( 'Font Style (italic)', 'radykal' ),
							'fontWeight' => __( 'Font Weight (bold)', 'radykal' ),
							'textDecoration' => __( 'Text Decoration (underline)', 'radykal' ),
						)

					),

					array(
						'title' 	=> __( 'Smart Guides', 'radykal' ),
						'description' 		=> __( 'Snap the selected object to the edges of the other objects and to the canvas center.', 'radykal' ),
						'id' 		=> 'fpd_smartGuides',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Canvas Touch Scrolling', 'radykal' ),
						'description'	 => __( 'Enable touch gesture to scroll on canvas.', 'radykal' ),
						'id' 		=> 'fpd_canvas_touch_scrolling',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Per-Pixel Detection', 'radykal' ),
						'description'	 => __( 'Object detection happens on per-pixel basis rather than on per-bounding-box. This means transparency of an object is not clickable.', 'radykal' ),
						'id' 		=> 'fpd_canvas_per_pixel_detection',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Fit Images In Canvas', 'radykal' ),
						'description'	 => __( 'If the image (custom uploaded or design) is larger than the canvas, it will be scaled down to fit into the canvas.', 'radykal' ),
						'id' 		=> 'fpd_fitImagesInCanvas',
						'default'	=> 'yes',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Set Textbox Width', 'radykal' ),
						'description'	 => __( 'The users are able to set or change the width of a textbox in the frontend.', 'radykal' ),
						'id' 		=> 'fpd_setTextboxWidth',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Upload zones always on top', 'radykal' ),
						'description'	 	=> __( 'Upload zones will be always on top of all elements.', 'radykal' ),
						'id' 		=> 'fpd_uploadZonesTopped',
						'default'	=> 'yes',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Unsaved Customizations Alert', 'radykal' ),
						'description'	 => __( 'The user will see a notification alert when he leaves the page without saving or adding the product to the cart.', 'radykal' ),
						'id' 		=> 'fpd_unsaved_customizations_alert',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Hide Dialog On Add', 'radykal' ),
						'description'	 => __( 'The dialog/off-canvas panel will be closed as soon as an element is added to the canvas.', 'radykal' ),
						'id' 		=> 'fpd_hide_dialog_on_add',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Lazy Load', 'radykal' ),
						'description'	 	=> __( 'Enable lazy loading for the images in the products and designs containers.', 'radykal' ),
						'id' 		=> 'fpd_lazy_load',
						'default'	=> 'yes',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Improved Image Resize Quality', 'radykal' ),
						'description'	 	=> __( 'Enable a filter that improves the quality of a resized bitmap image. This could take a long time for large images.', 'radykal' ),
						'id' 		=> 'fpd_improvedResizeQuality',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Responsive', 'radykal' ),
						'description'	 	=> __( 'Resizes the canvas and all elements in the canvas, so that all elements are displaying properly in the canvas container. This is useful, when your canvas is larger than the available space in the parent container.', 'radykal' ),
						'id' 		=> 'fpd_responsive',
						'default'	=> 'yes',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'In Canvas Text Editing', 'radykal' ),
						'description'	 => __( 'The user can edit the text via double click or tap(mobile).', 'radykal' ),
						'id' 		=> 'fpd_inCanvasTextEditing',
						'default'	=> 'yes',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Open Text Input On Select', 'radykal' ),
						'description'	 => __( 'The textarea in the toolbar to change an editbale text opens when the text is selected.', 'radykal' ),
						'id' 		=> 'fpd_openTextInputOnSelect',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Replace Colors In Color Group', 'radykal' ),
						'description'	 => __( ' As soon as an element with a color link group is added, the colours of this element will be used for the color group.', 'radykal' ),
						'id' 		=> 'fpd_replaceColorsInColorGroup',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Show Modal In Designer', 'radykal' ),
						'description'	 => __( 'Display some modals (info, qr-code etc.) in the designer instead in the whole page.', 'radykal' ),
						'id' 		=> 'fpd_openModalInDesigner',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Image Size Tooltip', 'radykal' ),
						'description'	 => __( 'Display the image size in pixels of the current selected image in a tooltip.', 'radykal' ),
						'id' 		=> 'fpd_imageSizeTooltip',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Apply Fill When Replacing', 'radykal' ),
						'description'	 => __( 'When an element is replaced, apply fill(color) from replaced element to added element.', 'radykal' ),
						'id' 		=> 'fpd_applyFillWhenReplacing',
						'default'	=> 'yes',
						'type' 		=> 'checkbox',
					),

					array(
						'title' => __( 'Bounding Box Stroke Width', 'radykal' ),
						'description' 		=> __( 'The stroke width of the bounding box when an element is selected.', 'radykal' ),
						'id' 		=> 'fpd_bounding_box_stroke_width',
						'css' 		=> 'width:60px;',
						'default'	=> '1',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Highlight Editable Objects', 'radykal' ),
						'description' 		=> __( 'Highlight objects (editable texts and upload zones) with a dashed border. To enable this just define a hexadecimal color value.', 'radykal' ),
						'id' 		=> 'fpd_highlightEditableObjects',
						'css' 		=> 'width:100px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'FabricJS Texture Size', 'radykal' ),
						'description' 		=> __( 'When applying a filter to an image, e.g. the colorization filter on PNG images, this is the max. size in pixels that will be painted. The image parts that are exceeding the max. size are not visible. The max. value should be lower than 5000. <a href="http://fabricjs.com/fabric-filters" target="_blank">More infos about FabricJS filters</a>.', 'radykal' ),
						'id' 		=> 'fpd_fabricjs_texture_size',
						'css' 		=> 'width:60px;',
						'default'	=> '4096',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),


				), //layout-skin

				'troubleshooting' => array(

					array(
						'title' 	=> __( 'Debug Mode', 'radykal' ),
						'description' 		=> __( 'Enables Theme-Check modal and loads the unminified Javascript files.', 'radykal' ),
						'id' 		=> 'fpd_debug_mode',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' 	=> __( 'jQuery No-Conflict Mode', 'radykal' ),
						'description' 		=> __( 'Enables jQuery No-Conflict mode. If your website uses another library using the $ as an alias, enable this option.', 'radykal' ),
						'id' 		=> 'fpd_jquery_no_conflict',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' 	=> __( 'FabricJS Version', 'radykal' ),
						'description' 		=> __( 'Switch between FabricJS versions.', 'radykal' ),
						'id' 		=> 'fpd_fabric_version',
						'default'	=> '3.0.0',
						'type' 		=> 'radio',
						'options'   => array(
							'1.6.7'	 => __( '1.6.7', 'radykal' ),
							'2.2'	 => __( '2.+', 'radykal' ),
							'3.0.0'	 => __( '3.0.0', 'radykal' ),
						)
					),

				),

			));
		}

	}
}

?>