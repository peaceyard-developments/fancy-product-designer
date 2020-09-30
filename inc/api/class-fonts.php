<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Fonts') ) {

	class FPD_Fonts {

		//returns an array with all active fonts
		public static function get_enabled_fonts() {

			$all_fonts = array();

			$common_fonts = get_option( 'fpd_common_fonts' );
			if( !empty($common_fonts) ) {
				$all_fonts = array_map('trim', explode(",", $common_fonts));
			}

			//google webfonts
			$google_webfonts = get_option( 'fpd_google_webfonts' );

			if( !empty($google_webfonts) && is_array($google_webfonts) ) {
				foreach($google_webfonts as $google_webfont) {

					$google_webfont = strpos($google_webfont, ':') === false ? $google_webfont : substr($google_webfont, 0, strpos($google_webfont, ':'));
					$google_webfont = str_replace('+', ' ', $google_webfont);

					if(!in_array($google_webfont, $all_fonts))
						$all_fonts['https://fonts.googleapis.com/css?family='.$google_webfont] = $google_webfont;
				}
			}

			//directory fonts
			$directory_fonts = get_option( 'fpd_fonts_directory' );
			if( !empty($directory_fonts) && is_array($directory_fonts) ) {
				foreach($directory_fonts as $directory_font) {
					$customFontName = str_replace('_', ' ', preg_replace("/\\.[^.\\s]{3,4}$/", "", $directory_font) );
					$all_fonts[content_url('/uploads/fpd_fonts/'.$directory_font)] = $customFontName;
				}
			}

			asort($all_fonts);

			return $all_fonts;

		}

		public static function to_json( $fonts ) {

			$custom_fonts_vars = array(
				'n7' => 'bold',
				'i4' => 'italic',
				'i7' => 'bolditalic'
			);

			$custom_fonts_dir = FPD_WP_CONTENT_DIR.'/uploads/fpd_fonts';

			$json_fonts = array();
			foreach($fonts as $key => $font_name) {

				$font_obj = new stdClass();
				$font_obj->name = $font_name;

				if( gettype($key) === 'string' ) {

					//custom fonts
					if(strpos($key, 'fonts.googleapis.com') == false) {
						$font_url = $key;

						$variants = array();
						foreach($custom_fonts_vars as $fv_key => $fv_value) {

							$font_filename = str_replace('.ttf', '', basename($font_url));
							$font_filename_var = $font_filename.'__'.$fv_value.'.ttf';

							if( file_exists($custom_fonts_dir.'/'.$font_filename_var) ) {
								$variants[$fv_key] = content_url('/uploads/fpd_fonts/'.$font_filename_var);
							}

						}

						$font_obj->variants = $variants;

					}
					//google fonts
					else {
						$font_url = 'google';
					}

					$font_obj->url = $font_url;



				}

				array_push($json_fonts, $font_obj);

			}

			return json_encode($json_fonts);

		}

	}

}

?>