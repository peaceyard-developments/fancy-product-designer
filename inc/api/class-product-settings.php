<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Product_Settings')) {

	class FPD_Product_Settings {

		public $id;
		public $master_id;
		public $individual_settings = array();
		public $customize_button_enabled = false;

		public function __construct( $id ) {

			$this->id = $id;

			//get master id if wpml is enabled
			global $sitepress;

			$this->master_id = $id;
			if($sitepress && method_exists($sitepress, 'get_original_element_id')) {

				$fpd_product_cats = get_post_meta( $id, 'fpd_product_categories', true );
				$fpd_products = get_post_meta( $id, 'fpd_products', true );
				if( empty($fpd_product_cats) && empty($fpd_products) )
					$this->master_id = $sitepress->get_original_element_id($id, 'post_product');

			}

			//get individual product options
			$product_settings_array = array();
			$product_settings = get_post_meta( $this->master_id, 'fpd_product_settings', true );
			if( !empty($product_settings) ) {

				$product_settings_array = json_decode(html_entity_decode($product_settings), true);
				//remove elements with empty value
				if( is_array($product_settings_array) ) {
					$product_settings_array = array_filter($product_settings_array, array( &$this, 'remove_empty_values'));
				}

			}

			$this->individual_settings = $product_settings_array;

			//check if customize button is enabled
			$this->customize_button_enabled = $this->get_option('product_designer_visibility') != 'page' && !isset($_GET['start_customizing']);

			if( (isset($_GET['cart_item_key']) && $this->get_option('product_designer_visibility') == 'page-customize') || ( isset($_GET['order']) && isset($_GET['item_id']) )) {
				$this->customize_button_enabled = false;
			}

		}

		public function show_designer() {

			if( is_fancy_product( $this->master_id ) ) {

				if( $this->get_option('product_designer_visibility') != 'page-customize' )
					return true;
				else
					//display when customize button is clicked, coming from cart or show order
					return isset($_GET['start_customizing']) || isset($_GET['cart_item_key']) || ( isset($_GET['order']) && isset($_GET['item_id'] ) );

			}
			else {
				return false;
			}

		}

		/**
		 * 	Returns an option from the individual settings. If no option is not found in the individual settings, it will return the option from the main settings.
		 */
		public function get_option( $name ) {

			if( isset($this->individual_settings[$name]) ) {
				$value = fpd_convert_string_value_to_int($this->individual_settings[$name]);
			}
			else {
				$value = fpd_get_option( 'fpd_'.$name );
			}

			return $value;

		}

		/**
		 * 	Returns an option from the individual settings.
		*/
		public function get_individual_option( $name ) {

			return isset($this->individual_settings[$name]) ?  $this->individual_settings[$name] : false;

		}

		/**
		 * 	Returns the assigned Fancy Categories of a woocoomerce product.
		*/
		public function get_content_ids( $check_global=true ) {

			$source_type = get_post_meta( $this->master_id, 'fpd_source_type', true );

			$content_ids = empty($source_type) || $source_type == 'category' ? get_post_meta( $this->master_id, 'fpd_product_categories', true ) : get_post_meta( $this->master_id, 'fpd_products', true );

			//check if mobile is used
			if( fpd_is_mobile() ) {

				$source_type_mobile = get_post_meta( $this->master_id, 'fpd_source_type_mobile', true );
				if( !empty($source_type_mobile) ) {

					$content_ids_mobile = $source_type_mobile == 'category' ? get_post_meta( $this->master_id, 'fpd_product_categories_mobile', true ) : get_post_meta( $this->master_id, 'fpd_products_mobile', true );

					if(!empty($content_ids_mobile) )
						$content_ids = $content_ids_mobile;

				}

			}

			if( empty($content_ids) && $check_global ) {

				if( fpd_get_option('fpd_global_product_designer') ) {

					$source_type = fpd_get_option('fpd_global_source_type');
					$global_ids = $source_type == 'category' ? fpd_get_option('fpd_global_fancy_product_cats') : fpd_get_option('fpd_global_fancy_products');

					if( !empty($global_ids) )
						return explode(',', $global_ids);

				}

				return array();

			}
			else if(is_array($content_ids))
				return $content_ids; //v2.0
			else if( !empty($content_ids) )
				return explode(',', $content_ids); //v3.0
			else
				return array();
		}

		public function get_source_type() {

			$source_type = get_post_meta( $this->master_id, 'fpd_source_type', true );

			//check if mobile is used
			if( fpd_is_mobile() ) {

				$source_type_mobile = get_post_meta( $this->master_id, 'fpd_source_type_mobile', true );
				if( !empty($source_type_mobile) ) {

					$content_ids_mobile = $source_type_mobile == 'category' ? get_post_meta( $this->master_id, 'fpd_product_categories_mobile', true ) : get_post_meta( $this->master_id, 'fpd_products_mobile', true );

					if(!empty($content_ids_mobile) )
						$source_type = $source_type_mobile;

				}

			}

			//post has no ids assigned and global product is enabled
			$post_ids = $this->get_content_ids( false );
			if( empty($post_ids) && fpd_get_option('fpd_global_product_designer') )
				$source_type = fpd_get_option('fpd_global_source_type');

			return $source_type;

		}

		public function get_image_parameters() {

			$strip_from_option_key = 'fpd_designs_parameter_';

			$images_parameters = array();
			$image_options = FPD_Settings_Default_Element_Options::get_options();
			$image_options = $image_options['images'];
			foreach( $image_options as $option ) {


				if( isset($option['default']) && strpos($option['id'], $strip_from_option_key) !== false ) {

					$parameter = str_replace($strip_from_option_key, '', $option['id']);
					$pure_key = str_replace('fpd_', '', $option['id']);
					$value = $this->get_option($pure_key);
					if( fpd_not_empty($value) )
						$images_parameters[$parameter] = $value;

				}

			}

			$images_parameters['removable'] = 1;

			return $images_parameters;

		}

		public function get_image_parameters_string() {

			return FPD_Parameters::to_json($this->get_image_parameters());

		}

		public function get_custom_text_parameters_string() {

			$strip_from_option_key = 'fpd_custom_texts_parameter_';

			$custom_texts_parameters = array();
			$custom_text_options = FPD_Settings_Default_Element_Options::get_options();

			$custom_text_options = $custom_text_options['custom-texts'];
			foreach( $custom_text_options as $option ) {

				if( isset($option['default']) && strpos($option['id'], $strip_from_option_key) !== false ) {


					$parameter = str_replace($strip_from_option_key, '', $option['id']);
					$pure_key = str_replace('fpd_', '', $option['id']);
					$value = $this->get_option($pure_key);
					if( fpd_not_empty($value) )
						$custom_texts_parameters[$parameter] = $value;

				}

			}

			$custom_texts_parameters['removable'] = 1;

			return FPD_Parameters::to_json($custom_texts_parameters, 'text');

		}

		public function get_custom_image_parameters_string() {

			$strip_from_option_key = 'fpd_uploaded_designs_parameter_';

			$custom_images_parameters = array();
			$custom_image_options = FPD_Settings_Default_Element_Options::get_options();

			$custom_image_options = $custom_image_options['custom-images'];
			foreach( $custom_image_options as $option ) {

				if( isset($option['default']) && strpos($option['id'], $strip_from_option_key) !== false ) {

					$parameter = str_replace($strip_from_option_key, '', $option['id']);
					$pure_key = str_replace('fpd_', '', $option['id']);
					$custom_images_parameters[$parameter] = $this->get_option($pure_key);

				}

			}

			return FPD_Parameters::to_json($custom_images_parameters);

		}

		public function get_add_to_cart_text() {

			return $this->get_option('get_quote') ? FPD_Settings_Labels::get_translation( 'woocommerce', 'get_a_quote' ) : FPD_Settings_Labels::get_translation( 'woocommerce', 'catalog:_add_to_cart' );


		}

		private function remove_empty_values($var){

			return ($var !== NULL && $var !== FALSE && $var !== '');

		}

	}

}

?>