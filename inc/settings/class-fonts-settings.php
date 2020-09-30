<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings_Fonts') ) {

	class FPD_Settings_Fonts {

		public static function get_options() {

			return apply_filters('fpd_fonts_settings', array(

				'fonts' => array(

					array(
						'title' => __( 'System Fonts', 'radykal' ),
						'description' 		=> 'Enter system fonts separated by comma, which are installed on all operating system, e.g. Arial.',
						'id' 		=> 'fpd_common_fonts',
						'css' 		=> 'width: 100%;',
						'type' 		=> 'text',
						'default'	=> '',
						'value' => 'Arial,Helvetica,Times New Roman,Verdana,Geneva'
					),

					array(
						'title' 	=> __( 'Google Webfonts', 'radykal' ),
						'description' 		=> __( "Choose fonts from Google Webfonts. Please be aware that too many fonts from Google may increase the loading time of the website. ", 'radykal' ),
						'id' 		=> 'fpd_google_webfonts',
						'css' 		=> 'width: 100%;',
						'default'	=> '',
						'type' 		=> 'multiselect',
						'options' 	=> self::get_google_webfonts()
					),

					array(
						'title' 	=> __( 'Fonts Directory', 'radykal' ),
						'description' 		=> __( "You can add your own fonts to wp-content-uploads/fpd_fonts. These font files need to be TrueType (.ttf) fonts. If you add font variants (italic, bold and bold-italic), these are not shown in this dropdown, these will be automatically loaded in the frontend.", 'radykal' ),
						'id' 		=> 'fpd_fonts_directory',
						'css' 		=> 'width: 100%;',
						'default'	=> '',
						'type' 		=> 'multiselect',
						'options' 	=> self::get_custom_fonts()
					),

				)

			));

		}

		/**
		 * Get google webfonts fonts
		 *
		 * @return array
		 */
		public static function get_google_webfonts() {

			$optimised_google_webfonts = array();

			//load fonts from google webfonts
			//delete_transient('fpd_google_webfonts');
			$optimised_google_webfonts = get_transient( 'fpd_google_webfonts' );
			if ( empty( $optimised_google_webfonts ) )	{

				$google_webfonts = fpd_admin_get_file_content( 'https://www.googleapis.com/webfonts/v1/webfonts?key='.Fancy_Product_Designer::GOOGLE_API_KEY );

				if($google_webfonts !== false ) {

					$google_webfonts = json_decode($google_webfonts);
					$optimised_google_webfonts = array();

					if( isset($google_webfonts->items) ) {
						foreach($google_webfonts->items as $item) {
							foreach($item->variants as $variant) {
								if($variant == 'regular') {
									$key = str_replace(' ', '+', $item->family).':'.$variant;
									$optimised_google_webfonts[$key] = $item->family;
								}

							}
						}
					}

				}

				if( empty($optimised_google_webfonts) )
					$optimised_google_webfonts = 'empty';

				//no webfonts could be loaded, try again in one min otherwise store them for one week
				set_transient('fpd_google_webfonts', $optimised_google_webfonts, !is_array($optimised_google_webfonts) ? 60 : 604800 );

			}

			return is_array($optimised_google_webfonts) ? $optimised_google_webfonts : array();

		}

		/**
		 * Get woff fonts
		 *
		 * @return array
		 */
		public static function get_custom_fonts() {

			//load woff fonts from fonts directory
			$font_files = array();
			$fonts_dir = FPD_WP_CONTENT_DIR.'/uploads/fpd_fonts';

			if( file_exists($fonts_dir) ) {

				$files = scandir($fonts_dir);
				foreach($files as $file) {
					if( preg_match("/.(woff|ttf|WOFF|TTF)/", strtolower($file)) ) {

						$count = 0;
						str_replace(array('__bold', '__italic', '__bolditalic'), '', $file, $count);

						//replace white in key, replace underscore with whitespace in value
						if($count == 0)
							$font_files[str_replace(' ', '_', $file)] = str_replace('_', ' ', preg_replace("/\\.[^.\\s]{3,4}$/", "", $file) );
					}
				}

			}

			return $font_files;

		}

	}

}