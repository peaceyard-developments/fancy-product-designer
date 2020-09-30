<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Labels_Settings') ) {

	class FPD_Labels_Settings {

		public static function get_labels() {

			return apply_filters('fpd_admin_labels_settings',
				array (
					'general' => __( 'General', 'radykal' ),
					'elementProperties' => __( 'Element Properties', 'radykal' ),
					'labels' => __( 'Labels', 'radykal' ),
					'fonts' => __( 'Fonts', 'radykal' ),
					'colors' => __( 'Colors', 'radykal' ),
					'woocommerce' => __( 'WooCommerce', 'radykal' ),
					'automatedExport' => __( 'Automated Export', 'radykal' ),
					'advanced' => __( 'Advanced', 'radykal' ),
					'plus' => __( 'Plus', 'radykal' ),
					'resetOptions' => __( 'Reset Options', 'radykal' ),
					'resetOptionsText' => __( 'Are you sure to reset current showing options?', 'radykal' ),
					'save' => __( 'Save Changes', 'radykal' ),
					'reset' => __( 'Reset', 'radykal' ),
					'searchOption' => __( 'Search Option...', 'radykal' ),
					'display' => __( 'Display', 'radykal' ),
					'modules' => __( 'Modules', 'radykal' ),
					'actions' => __( 'Actions', 'radykal' ),
					'social-share' => __( 'Social Share', 'radykal' ),
					'pricing-rules' => __( 'Pricing Rules', 'radykal' ),
					'rest-api' => __( 'REST API', 'radykal' ),
					'images' => __( 'Images', 'radykal' ),
					'custom-images' => __( 'Custom Images', 'radykal' ),
					'all-images' => __( 'All Images', 'radykal' ),
					'custom-texts' => __( 'Custom Texts', 'radykal' ),
					'all-texts' => __( 'All Texts', 'radykal' ),
					'toolbar' => __( 'Toolbar', 'radykal' ),
					'image_editor' => __( 'Image Editor', 'radykal' ),
					'misc' => __( 'Miscellaneous', 'radykal' ),
					'color-names' => __( 'Color Names', 'radykal' ),
					'color-prices' => __( 'Color Prices', 'radykal' ),
					'color-general' => __( 'Color General', 'radykal' ),
					'product-page' => __( 'Product Page', 'radykal' ),
					'cart' => __( 'Cart', 'radykal' ),
					'order' => __( 'Order', 'radykal' ),
					'catalog-listing' => __( 'Catalog Listing', 'radykal' ),
					'global-product-designer' => __( 'Global Product Designer', 'radykal' ),
					'cross-sells' => __( 'Cross Sells', 'radykal' ),
					'dokan' => __( 'Dokan', 'radykal' ),
					'troubleshooting' => __( 'Troubleshooting', 'radykal' ),
					'tools' => __( 'Tools', 'radykal' ),

					//container
					'loadingOptions' => __( 'Loading Options...', 'radykal' ),
					'updatingOptions' => __( 'Updating Options...', 'radykal' ),
					'selectImage' => __( 'Select Image', 'radykal' ),
				)
			);

		}
	}

}

?>