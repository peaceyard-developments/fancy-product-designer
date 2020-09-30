<?php

if( !class_exists('FPD_Resource_Options') ) {

	class FPD_Resource_Options {

		public static function get_options( $option_keys = array() ) {

			$options = array();

			foreach($option_keys as $option_key) {

				if( $option_key == 'enabled_fonts' ) { //get enabled fonts
					$options[$option_key] = json_decode( FPD_Fonts::to_json(FPD_Fonts::get_enabled_fonts()) );
				}
				else if( $option_key == 'design_categories' ) { //get design categories

					$categories = get_terms( 'fpd_design_category', array(
					 	'hide_empty' => false,
					 	'parent' => 0
					));

					$design_categories = array();
					foreach($categories as $category) {
						$design_categories[$category->name] = $category->name;
					}

					$options[$option_key] = $design_categories;
				}
				else if( $option_key == 'primary_layout_props' ) { //get primary layout properties

					$main_ui = FPD_UI_Layout_Composer::get_layout(fpd_get_option('fpd_product_designer_ui_layout'));
					$plugin_options = $main_ui['plugin_options'];

					$options[$option_key] = array(
						'stageWidth' 	=> $plugin_options['stageWidth'],
						'stageHeight' 	=> $plugin_options['stageHeight']
					);

				}
				else if( $option_key == 'plus_enabled' ) {
					$options[$option_key] = class_exists('Fancy_Product_Designer_Plus');
				}
				else if( $option_key == 'fpd_custom_texts_parameter_patterns' || $option_key == 'fpd_designs_parameter_patterns' ) {

					$options[$option_key] = fpd_check_file_list(
						fpd_get_option($option_key),
						FPD_WP_CONTENT_DIR . ($option_key == 'fpd_custom_texts_parameter_patterns' ? '/uploads/fpd_patterns_text/'  : '/uploads/fpd_patterns_svg/')
					);

				}
				else { //get option by key
					$options[$option_key] = fpd_get_option($option_key);
				}

			}

			return $options;

		}

		public static function get_options_group( $args = array() ) {

			$defaults = array(
				'group' 		=> 'general',
				'option_keys' 	=> null,
				'lang_code' 	=> null
			);

			$args = wp_parse_args( $args, $defaults );

			//check: get options by keys
			if( isset( $args['option_keys'] ) && is_array( $args['option_keys'] ) ) {
				return self::get_options( $args['option_keys'] );
			}

			$options_group = $args['group'];
			$global_options = array();
			$error = null;

			$all_settings = FPD_Settings::$radykal_settings->settings;

			if( $options_group == 'labels' ) {

				$textarea_keys = array(
					'uploaded_image_size_alert',
					'not_supported_device_info',
					'info_content',
					'login_required_info'
				);

				$languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc&skip_missing=0' );
				if( $args['lang_code'] ) //get lang code from url
					$current_lang_code = $args['lang_code'];
				else if($languages) { //get first lang code from wpml languages
					$first = reset($languages);
					$current_lang_code = $first['language_code'];
				}
				else { //get locale code
					$current_lang_code = FPD_Settings_Labels::get_current_lang_code();
				}

				$current_lang = FPD_Settings_Labels::get_current_lang($current_lang_code);
				$default_lang = FPD_Settings_Labels::get_default_lang();

				$labels_options = array();
				foreach($default_lang as $key_section => $section) {

					$labels_options[$key_section] = array();
					foreach($section as $key_option_entry => $option_entry) {

						$label_option_data = array(
							'title' => str_replace( ':', ': ', str_replace('_', ' ', $key_option_entry) ), //replace _ with whitespace and : with :whitespace
							'default' => $option_entry,
							'id' => $key_option_entry,
							'type' => in_array($key_option_entry, $textarea_keys) ? 'textarea' : 'text',
							'value' => isset($current_lang[$key_section][$key_option_entry]) ? $current_lang[$key_section][$key_option_entry] : $option_entry
						);

						array_push($labels_options[$key_section], $label_option_data);

					}

				}

				$global_options[$current_lang_code] = $labels_options;

			}
			else if( $options_group == 'plus' ) {

				if( class_exists('FPD_Plus_Settings') )
					$global_options = FPD_Plus_Settings::get_options();
				else
					$error = array(
						'code' => 'fpd_options_group_not_exist',
						'message' => __('The Fancy Product Designer PLUS add-on is not activated or installed!')
					);

			}
			else
				$global_options = $all_settings[$options_group];

			if( !is_null($error) ) {

				return new WP_Error(
					$error['code'],
					$error['message']
				);

			}

			if( $options_group !== 'labels' ) {

				foreach($global_options as $key_section => $section) {

					foreach($section as $key_option_entry => $option_entry) {

						$global_options[$key_section][$key_option_entry]['value'] = fpd_get_option($option_entry['id'], false);

					}

				}

			}

			return $global_options;

		}

		public static function update_options( $options = array() ) {

			$options = is_array($options) ? $options : json_decode($options, true);

			if( isset($options['labels_lang']) ) {

				$labels = apply_filters( 'fpd_labels_update', $options['labels'], $options['labels_lang'] );

				update_option('fpd_lang_'.$options['labels_lang'], json_encode($labels, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) );

			}
			else {

				foreach($options as $key => $value) {

					if( is_bool($value) )
						$value = $value ? 'yes' : 'no';

					$value = apply_filters( 'fpd_option_update', $value, $key );

					update_option( $key, $value );
				}

			}

			return array(
				'message' => __('Options updated.', 'radykal')
			);

		}

		public static function get_languages() {

			$wpml_langs = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc&skip_missing=0' );

			return is_null( $wpml_langs ) ? array() : $wpml_langs;

		}


	}

}

?>