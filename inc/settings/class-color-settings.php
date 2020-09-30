<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings_Colors') ) {

	class FPD_Settings_Colors {

		public static function get_options() {

			return apply_filters('fpd_advanced_color_settings', array(

				'color-names' => array(

					array(
						'title' => __( 'Color Names', 'radykal' ),
						'description' 		=> __( 'Show a custom name instead the hexadecimal value in the color palettes.', 'radykal' ),
						'id' 		=> 'fpd_hex_names',
						'css' 		=> 'width:500px;',
						'default'	=> '',
						'type' 		=> 'values-group',
						'fullwidth'	=> true,
						'options'   => array(
							'hex_key' => 'HEX-Color',
							'name' => 'Name'
						),
						'prefixes' => array(
							'hex_key' => '#',
							'name' => ''
						),
						'regexs' => array(
							'hex_key' => '^[0-9a-f]{6}$',
							'name' => '^[^, ]+$'
						)
					),

				),

				'color-prices' => array(

					array(
						'title' 	=> __( 'Enable for Texts', 'radykal' ),
						'description' 		=> __( 'Use the color prices for all text elements.', 'radykal' ),
						'id' 		=> 'fpd_enable_text_color_prices',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' 	=> __( 'Enable for Images', 'radykal' ),
						'description' 		=> __( 'Use the color prices for all image elements.', 'radykal' ),
						'id' 		=> 'fpd_enable_image_color_prices',
						'default'	=> 'no',
						'type' 		=> 'checkbox'
					),

					array(
						'title' => __( 'Color Prices', 'radykal' ),
						'description' 		=> __( 'You can set different prices based on the selected color. This works only for color palette.', 'radykal' ),
						'id' 		=> 'fpd_color_prices',
						'css' 		=> 'width:500px;',
						'default'	=> '',
						'type' 		=> 'values-group',
						'options'   => array(
							'hex_key' => 'HEX-Color',
							'price' => 'Price'
						),
						'prefixes' => array(
							'hex_key' => '#',
							'price' => ''
						),
						'regexs' => array(
							'hex_key' => '^[0-9a-f]{6}$',
							'price' => '^\d+(\.\d{1,2})?$'
						)
					),

				), //color-prices

				'color-general' => array(

					array(
						'title' => __( 'Color Picker Swatches', 'radykal' ),
						'description' 		=> __( 'Display color suggestions in the color picker. Enter hexadecimal color(s) separated by comma. E.g. #000,#fff,#990000', 'radykal' ),
						'id' 		=> 'fpd_color_colorPickerPalette',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'multi-color-input'
					),

				), //color-general

			));
		}

		public static function get_hex_names_object_string() {

			return self::convert_object_string_to_json( fpd_get_option( 'fpd_hex_names' ) );

		}

		public static function get_color_prices() {

			return self::convert_object_string_to_json( fpd_get_option( 'fpd_color_prices' ), true );

		}

		private static function convert_object_string_to_json( $obj_str, $val_num = false ) {

			if( empty($obj_str) )
				return '{}';

			$entries = explode(',', $obj_str);

			$json_arr = array();
			foreach( $entries as $entry ) {

				$pair_val = explode(':', $entry);
				$json_arr[$pair_val[0]] = $val_num ? floatval( $pair_val[1] ) : $pair_val[1];

			}

			return json_encode( $json_arr );

		}

	}

	//fallback
	class FPD_Settings_Advanced_Colors extends FPD_Settings_Colors{}
}

?>