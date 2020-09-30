<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_UI_Layout_Composer') ) {

	class FPD_UI_Layout_Composer {

		public static function get_default_json_url() {

			return FPD_PLUGIN_DIR.'/assets/json/default_ui_layout.json';

		}

		public static function get_layouts() {

			global $wpdb;
			return $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s", "fpd_ui_layout_%") );

		}

		public static function get_layout( $id='' ) {

			if( get_option('fpd_ui_layout_'.$id) !== false ) {
				return json_decode( stripslashes(get_option('fpd_ui_layout_'.$id)), true);
			}
			else if( get_option('fpd_ui_layout_default') !== false ) {
				return json_decode( stripslashes(get_option('fpd_ui_layout_default') ), true);
			}
			else {

				$default_layout = file_get_contents(self::get_default_json_url());
				$default_layout = json_encode(json_decode($default_layout));
				update_option('fpd_ui_layout_default', $default_layout);

				return json_decode($default_layout, true);

			}

		}

		public static function save_layout( $id, $saved_layout) {

			if( is_array( $saved_layout ) )
				$saved_layout = (object) $saved_layout;

			$primary_color = @$saved_layout->css_colors && @$saved_layout->css_colors->primary_color ? $saved_layout->css_colors->primary_color : '#000000';
			$secondary_color = @$saved_layout->css_colors && @$saved_layout->css_colors->secondary_color ? $saved_layout->css_colors->secondary_color : '#27ae60';
			$css_result = FPD_UI_Layout_Composer::parse_css('@primaryColor: '.$primary_color.'; @secondaryColor: '.$secondary_color.';');

			if( is_array($css_result) ) {//error while parsing

				return $css_result;

			}
			else { //successful parsing

				$saved_layout->css = $css_result;
				update_option( 'fpd_ui_layout_'.$id, addslashes(json_encode($saved_layout)) );

				return array(
					'message' => __('Layout saved.', 'radykal'),
					'type'    => 'success'
				);

			}

		}

		public static function get_css_from_layout( $layout ) {

			if( isset($layout->css) && !empty($layout->css) ) {

				return $layout->css;

			}
			else {

				if( is_array($layout) )
					$layout = json_decode(json_encode($layout)); //convert array to stdclass

				$primary_color = @$layout->css_colors && @$layout->css_colors->primary_color ? $layout->css_colors->primary_color : '#000000';
				$secondary_color = @$layout->css_colors && @$layout->css_colors->secondary_color ? $layout->css_colors->secondary_color : '#27ae60';
				$css_result = FPD_UI_Layout_Composer::parse_css('@primaryColor: '.$primary_color.'; @secondaryColor: '.$secondary_color.';');

				if( !is_array($css_result) ) {

					$layout_id = sanitize_key($layout->name);
					$layout->css = $css_result;
					update_option( 'fpd_ui_layout_'.$layout_id, addslashes(json_encode($layout)) );

					return $css_result;

				}
				else {
					return '';
				}
			}

		}

		public static function parse_css( $parse_str='' ) {

			if( !class_exists('Less_Parser') )
				require_once(FPD_PLUGIN_ADMIN_DIR.'/vendors/libs/less/Less.php');

			$less_file = FPD_PLUGIN_ADMIN_DIR.'/css/less/colors.less';

			try {

				$options = array( 'compress'=>true );
				$parser = new Less_Parser($options);
				$parser->parseFile( $less_file );
				$parser->parse( $parse_str );
				return $parser->getCss(); //save ins json

			}
			catch(Exception $e){

				return array(
					'message' => $e->getMessage(),
					'type'    => 'error'
				);

			}

		}

	}

}

?>