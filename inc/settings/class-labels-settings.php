<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings_Labels') ) {

	class FPD_Settings_Labels {

		public static function get_default_json_url() {

			return FPD_PLUGIN_DIR.'/assets/json/default_lang.json';

		}

		public static function get_current_lang_code() {

			if( defined('ICL_LANGUAGE_CODE') )
				return ICL_LANGUAGE_CODE;
			else
				return get_locale();

		}

		public static function get_default_lang() {

			$default_lang = get_option('fpd_lang_default');
			if( empty($default_lang) ) {

				$default_lang = file_get_contents(self::get_default_json_url());
				$default_lang = json_encode(json_decode($default_lang));
				update_option('fpd_lang_default', $default_lang);

			}

			return json_decode($default_lang, true);

		}

		public static function update_default_lang() {

			$default_lang = file_get_contents(self::get_default_json_url());
			$default_lang = json_encode(json_decode($default_lang));
			update_option('fpd_lang_default', $default_lang);

			return $default_lang;

		}

		//checks if the saved languages are missing translations from the default translation
		public static function update_all_languages() {

			global $wpdb;

			$default_lang = json_decode(self::update_default_lang(), true);

			$db_langs = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s AND option_name NOT LIKE 'fpd_lang_default'", "fpd_lang_%") );

			foreach($db_langs as $db_lang) {

				$lang = json_decode($db_lang->option_value, true);
				$new_lang = array_merge_recursive($default_lang, $lang); //merge default into db lang
				$new_lang = array_replace_recursive($default_lang, $lang);
				$new_lang = json_encode($new_lang);

				update_option($db_lang->option_name, $new_lang);

			}

		}

		public static function get_current_lang( $lang_code= false ) {

			if($lang_code == false)
				$lang_code = self::get_current_lang_code();

			$current_lang = get_option('fpd_lang_'.$lang_code);
			if( empty($current_lang) ) {

				$current_lang = file_get_contents(self::get_default_json_url());
				$current_lang = json_encode(json_decode($current_lang));
				update_option('fpd_lang_'.$lang_code, $current_lang);

			}

			return json_decode($current_lang, true);

		}

		public static function reset( $lang_code= false ) {

			if($lang_code == false)
				$lang_code = self::get_current_lang_code();

			self::update_default_lang();

			$current_lang = file_get_contents(self::get_default_json_url());
			$current_lang = json_encode(json_decode($current_lang));
			update_option('fpd_lang_'.$lang_code, $current_lang);

		}

		public static function get_translation($section, $key) {

			$lang_code = self::get_current_lang_code();

			$current_lang = get_option('fpd_lang_'.$lang_code);
			if( empty($current_lang) ) {

				$current_lang = file_get_contents(self::get_default_json_url());
				$current_lang = json_encode(json_decode($current_lang));
				update_option('fpd_lang_'.$lang_code, $current_lang);

			}

			$current_lang = json_decode($current_lang, true);

			if( isset($current_lang[$section]) ) {

				if( isset($current_lang[$section][$key]) ) {
					return htmlspecialchars( $current_lang[$section][$key], ENT_QUOTES );
				}
				else {
					$default_lang = self::get_default_lang();
					return $default_lang[$section][$key];
				}

			}
			else
				return '';

		}

		public static function get_labels_object_string() {

			$lang_code = self::get_current_lang_code();

			$current_lang = get_option('fpd_lang_'.$lang_code);
			if( empty($current_lang) ) {

				$current_lang = file_get_contents(self::get_default_json_url());
				$current_lang = json_encode(json_decode($current_lang));
				update_option('fpd_lang_'.$lang_code, $current_lang);

			}

			return $current_lang;

		}
	}

}