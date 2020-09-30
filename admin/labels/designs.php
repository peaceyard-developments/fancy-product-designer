<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Labels_Designs') ) {

	class FPD_Labels_Designs {

		public static function get_labels() {

			return apply_filters('fpd_admin_labels_designs',

				array (
					'enterCategoryTitle' => __( 'Enter a category title', 'radykal' ),
					'deleteCategory' => __( 'Delete Category', 'radykal' ),
					'deleteCategoryText' => __( 'Are you sure to delete the category?', 'radykal' ),
					'editCategoryTitle' => __( 'Edit Title', 'radykal' ),
					'addNewCategory' => __( 'Add New Category', 'radykal' ),
					'save' => __( 'Save', 'radykal' ),
					'addDesigns' => __( 'Add Designs', 'radykal' ),
					'categoryOptions' => __( 'Category Options', 'radykal' ),
					'manageCategories' => __( 'Manage Categories', 'radykal' ),
					'cardBackground' => __( 'Card Background', 'radykal' ),
					'light' => __( 'Light', 'radykal' ),
					'dark' => __( 'Dark', 'radykal' ),
					'editOptions' => __( 'Edit Options', 'radykal' ),
					'designOptions' => __( 'Design Options', 'radykal' ),
					'general_tab' => __( 'General', 'radykal' ),
					'colors_tab' => __( 'Colors', 'radykal' ),
					'designOptionsText' => __( 'Set individual options for the design.', 'radykal' ),
					'categoryOptionsText' => __( 'Set individual options for all designs in the category.', 'radykal' ),
					'thumbnail' => __( 'Thumbnail', 'radykal' ),
					'enableOptions' => __( 'Enable Options', 'radykal' ),
					'set' => __( 'Set', 'radykal' ),
					'custom_props_tab' => __( 'Customizable Properties', 'radykal' ),
					'bounding_box_tab' => __( 'Bounding Box', 'radykal' ),
					'x' => __( 'Left', 'radykal' ),
					'y' => __( 'Top', 'radykal' ),
					'z' => __( 'Layer Depth', 'radykal' ),
					'price' => __( 'Price', 'radykal' ),
					'_scaleBy' => __( 'Scale By', 'radykal' ),
					'scale' => __( 'Scale Factor', 'radykal' ),
					'resizeToW' => __( 'Scale To Width', 'radykal' ),
					'resizeToW_desc' => __( 'Pixel value (e.g. 400) or percentage value (e.g. 80%) to scale relative to canvas width.', 'radykal' ),
					'resizeToH' => __( 'Scale To Height', 'radykal' ),
					'resizeToH_desc' => __( 'Pixel value (e.g. 400) or percentage value (e.g. 80%) to scale relative to canvas height.', 'radykal' ),
					'scaleMode' => __( 'Scale Mode', 'radykal' ),
					'minScaleLimit' => __( 'Min. Scale Factor Limit', 'radykal' ),
					'sku' => __( 'SKU', 'radykal' ),
					'replace' => __( 'Replace', 'radykal' ),
					'replaceInAllViews' => __( 'Replace In All Views', 'radykal' ),
					'autoSelect' => __( 'Auto-Select', 'radykal' ),
					'topped' => __( 'Stay On Top', 'radykal' ),
					'autoCenter' => __( 'Auto-Center', 'radykal' ),
					'excludeFromExport' => __( 'Exclude From Export', 'radykal' ),
					'colorPicker' => __( 'Color Picker', 'radykal' ),
					'colors' => __( 'Palette', 'radykal' ),
					'colorLinkGroup' => __( 'Color Link Group', 'radykal' ),
					'draggable' => __( 'Movable', 'radykal' ),
					'rotatable' => __( 'Rotatable', 'radykal' ),
					'resizable' => __( 'Resizable', 'radykal' ),
					'removable' => __( 'Removable', 'radykal' ),
					'zChangeable' => __( 'Layer Depth Changeable', 'radykal' ),
					'uniScalingUnlockable' => __( 'Allow Unproportional Scaling', 'radykal' ),
					'advancedEditing' => __( 'Advanced Editing', 'radykal' ),
					'bounding_box_control' => __( 'Use another element as bounding box?', 'radykal' ),
					'bounding_box_by_other' => __( 'Bounding Box Target', 'radykal' ),
					'bounding_box_x' => __( 'Bounding Box Left Position', 'radykal' ),
					'bounding_box_y' => __( 'Bounding Box Top Position', 'radykal' ),
					'bounding_box_width' => __( 'Bounding Box Width', 'radykal' ),
					'bounding_box_height' => __( 'Bounding Box Height', 'radykal' ),
					'boundingBoxMode' => __( 'Bounding Box Mode', 'radykal' ),

					//container
					'loadingDesignCategories' => __( 'Loading Design Categories...', 'radykal' ),
					'loadingDesignCategory' => __( 'Loading Design Category...', 'radykal' ),
					'creatingDesignCategory' => __( 'Creating Design Category...', 'radykal' ),
					'updatingDesignCategory' => __( 'Updating Design Category...', 'radykal' ),
					'deletingDesignCategory' => __( 'Deleting Design Category...', 'radykal' ),
					'selectThumbnail' => __( 'Select Thumbnail', 'radykal' ),
					'selectImages' => __( 'Select Images', 'radykal' ),
					'noCategories' => __( 'No categories created yet. Please create a category first!', 'radykal' ),
					'loadingOptions' => __( 'Loading Options...', 'radykal' ),
					'updatingOptions' => __( 'Updating Options...', 'radykal' ),
				)

			);

		}
	}

}

?>