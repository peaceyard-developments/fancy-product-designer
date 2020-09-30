<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Labels_Pricing_Rules') ) {

	class FPD_Labels_Pricing_Rules {

		public static function get_labels() {

			return apply_filters('fpd_admin_labels_pricing_rules',
				array (
					'addGroup' => __( 'Add Pricing Rules Group', 'radykal' ),
					'saveGroups' => __( 'Save Pricing Rules', 'radykal' ),
					'collapseToggle' => __( 'Collapse Toggle', 'radykal' ),
					'textLength' => __( 'Text Length', 'radykal' ),
					'fontSize' => __( 'Text Size', 'radykal' ),
					'linesLength' => __( 'Lines Length', 'radykal' ),
					'imageSize' => __( 'Image Size (Origin Width & Height)', 'radykal' ),
					'imageSizeScaled' => __( 'Image Size Scaled', 'radykal' ),
					'elementsLength' => __( 'Amount of elements', 'radykal' ),
					'colorsLength' => __( 'Amount of used colors', 'radykal' ),
					'canvasSize' => __( 'Canvas Size', 'radykal' ),
					'deletePricingGroup' => __( 'Delete Pricing Rules Group', 'radykal' ),
					'deletePricingGroupText' => __( 'Are you sure to delete the Pricing Rules Group?', 'radykal' ),
					'enterPricingGroupName' => __( 'Please enter a name for the Pricing Rules Group', 'radykal' ),
					'noEmptyName' => __( 'Empty names are not supported!', 'radykal' ),
					'groupNameExists' => __( 'A group with the same name already exists. Please choose another one!', 'radykal' ),
					'confirmRulesRemoval' => __( 'Rules Removal', 'radykal' ),
					'confirmRulesRemovalText' => __( 'All rules will be removed when changing the property. Are you sure?', 'radykal' ),
					'equal' => __( 'Equal', 'radykal' ),
					'greater' => __( 'Greater than', 'radykal' ),
					'less' => __( 'Less than', 'radykal' ),
					'greaterEqual' => __( 'Greater than or equal', 'radykal' ),
					'lessEqual' => __( 'Less than or equal', 'radykal' ),
					'width' => __( 'Width', 'radykal' ),
					'height' => __( 'Height', 'radykal' ),
					'value' => __( 'Value', 'radykal' ),
					'price' => __( 'Price', 'radykal' ),
					'propertyInfo' => __( 'Select a property that will be used for pricing.', 'radykal' ),
					'property' => __( 'Property', 'radykal' ),
					'targetsInfo' => __( 'The view(s) and the elements(s) in the view(s) you want to use for the pricing rules.', 'radykal' ),
					'targets' => __( 'Target(s)', 'radykal' ),
					'viewsInfo' => __( 'Set a numeric index to target specific views.0=first view, 1=second view...', 'radykal' ),
					'views' => __( 'View(s)', 'radykal' ),
					'elementsInfo' => __( 'The element(s) in the view(s) to target.', 'radykal' ),
					'elements' => __( 'Element(s)', 'radykal' ),
					'all' => __( 'ALL', 'radykal' ),
					'allImages' => __( 'All Images', 'radykal' ),
					'allTexts' => __( 'All texts', 'radykal' ),
					'allCustomImages' => __( 'All custom images', 'radykal' ),
					'allCustomTexts' => __( 'All custom texts', 'radykal' ),
					'singleElement' => __( 'Single Element', 'radykal' ),
					'elementTitle' => __( 'Enter title of an element', 'radykal' ),
					'matchInfo' => __( 'Define the match type.', 'radykal' ),
					'match' => __( 'Match', 'radykal' ),
					'anyInfo' => __( 'ONLY THE FIRST matching rule will be executed.', 'radykal' ),
					'any' => __( 'ANY', 'radykal' ),
					'allInfo' => __( 'ALL matching rules will be executed.', 'radykal' ),
					'rulesInfo' => __( 'The order is important when using the ANY match.', 'radykal' ),
					'rules' => __( 'Rules', 'radykal' ),
					'addRule' => __( 'Add Rule', 'radykal' ),

					//container
					'updatingPricingRules' => __( 'Updating Pricing Rules...', 'radykal' ),
				)
			);

		}
	}

}

?>