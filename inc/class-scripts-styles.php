<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Scripts_Styles')) {

	class FPD_Scripts_Styles {

		public static $add_script = false;

		public function __construct() {

			add_action( 'init', array( &$this, 'register'), 20 );
			add_action( 'wp_enqueue_scripts',array( &$this,'enqueue_styles' ) );
			add_action( 'wp_head',array( &$this,'print_css' ), 100 );
			add_action( 'wp_footer', array(&$this, 'footer_handler') );

		}

		public function register() {

			$local_test = Fancy_Product_Designer::LOCAL;
			$debug_mode = fpd_get_option('fpd_debug_mode');
			$timestamp = time();

			//only local testing
			if($local_test) {
				wp_enqueue_style( 'fpd-test-webfont', '//radykal.dep/fpd/src/FontFPD/style.css', false, Fancy_Product_Designer::VERSION );
				wp_enqueue_style( 'fpd-test-plugins', '//radykal.dep/fpd/dist/css/plugins.min.css', false, Fancy_Product_Designer::FPD_VERSION );
			}

			$fpd_css_url = $local_test ? '//radykal.dep/fpd/dist/css/FancyProductDesigner.css?'.$timestamp : plugins_url('/assets/css/FancyProductDesigner-all.min.css', FPD_PLUGIN_ROOT_PHP);
			$fpd_js_url = $local_test ? '//radykal.dep/fpd/dist/js/FancyProductDesigner.js?'.$timestamp : plugins_url('/assets/js/FancyProductDesigner-all.min.js', FPD_PLUGIN_ROOT_PHP);
			$fpd_js_plugins_url = $local_test ? '//radykal.dep/fpd/dist/js/plugins.js' : plugins_url('/assets/js/plugins.js', FPD_PLUGIN_ROOT_PHP);
			$fpd_js_url = $debug_mode ?  plugins_url('/assets/js/FancyProductDesigner.js', FPD_PLUGIN_ROOT_PHP) : $fpd_js_url;

			wp_register_style( 'jquery-fpd-static', plugins_url('/assets/css/static.min.css', FPD_PLUGIN_ROOT_PHP), false, Fancy_Product_Designer::FPD_VERSION );
			wp_register_style( 'jquery-fpd', $fpd_css_url, false, Fancy_Product_Designer::FPD_VERSION );
			wp_register_style( 'fpd-jssocials-theme', plugins_url('/assets/jssocials/jssocials-theme-flat.css', FPD_PLUGIN_ROOT_PHP), false, '1.4.0' );
			wp_register_style( 'fpd-jssocials', plugins_url('/assets/jssocials/jssocials.css', FPD_PLUGIN_ROOT_PHP), array('fpd-jssocials-theme'), '1.4.0' );

			//register js files
			wp_register_script( 'fpd-plugins', $fpd_js_plugins_url, false, Fancy_Product_Designer::FPD_VERSION );

			$fabric_version = fpd_get_option('fpd_fabric_version');
			$fabric_version = $fabric_version == '2.2' ? '' : '-'.$fabric_version;
			$fabricjs_file = $local_test || $debug_mode ? 'fabric'.$fabric_version.'.js' : 'fabric'.$fabric_version.'.min.js';
			wp_register_script( 'fabric', plugins_url('/assets/js/'.$fabricjs_file, FPD_PLUGIN_ROOT_PHP), false, empty($fabric_version) ? '2.2.1' : '1.6.7' );
			wp_register_script( 'fpd-jssocials', plugins_url('/assets/jssocials/jssocials.min.js', FPD_PLUGIN_ROOT_PHP), false, '1.4.0' );

			$fpd_dep = array(
				'jquery',
				'jquery-ui-draggable',
				'jquery-ui-sortable',
				'fabric',
			);

			if( $local_test || $debug_mode )
				array_push($fpd_dep, 'fpd-plugins');

			//PLUS
			if( wp_script_is( 'fpd-plus', 'registered' ) )
				array_push($fpd_dep, 'fpd-plus');

			wp_register_script( 'jquery-fpd', $fpd_js_url, $fpd_dep, $debug_mode ? $timestamp : Fancy_Product_Designer::FPD_VERSION );

		}

		//includes scripts and styles in the frontend
		public function enqueue_styles() {

			global $post;

			if( !isset($post->ID) )
				return;

			if( fpd_get_option('fpd_sharing') )
				wp_enqueue_style( 'fpd-jssocials' );

			wp_enqueue_style( 'jquery-fpd' );
			wp_enqueue_style( 'fpd-single-product', plugins_url('/assets/css/fancy-product.css', FPD_PLUGIN_ROOT_PHP), false, Fancy_Product_Designer::VERSION );

			wp_enqueue_script( 'jquery' );

		}

		public function print_css() {

			global $post;

			if( isset($post->ID) && is_fancy_product($post->ID) ) {

				//only enqueue css and js files when necessary
				$product_settings = new FPD_Product_Settings( $post->ID );
				//get ui layout
				$ui_layout = FPD_UI_Layout_Composer::get_layout($product_settings->get_option('product_designer_ui_layout'));
				$css_str = FPD_UI_Layout_Composer::get_css_from_layout($ui_layout);

				?>
				<style type="text/css">

					<?php if( $product_settings->get_option('background_type') ): ?>
					.fpd-container .fpd-main-wrapper {
						background: <?php echo $product_settings->get_option('background_type') == 'color' ? $product_settings->get_option('background_color') : 'url('.$product_settings->get_option('background_image').')'; ?> !important;
					}
					<?php endif; ?>
					<?php
						if( !empty($css_str) )
							echo $css_str;
						echo stripslashes( $ui_layout['custom_css'] );
					?>

					<?php
					//hide tools
					if( isset($ui_layout['toolbar_exclude_tools'])  && is_array($ui_layout['toolbar_exclude_tools']) ) {

						foreach( $ui_layout['toolbar_exclude_tools'] as $tb_tool ) {
							echo '[class^="fpd-element-toolbar"] .fpd-tool-'.$tb_tool.'{ display: none !important; }';
						}

					}
					?>

				</style>
				<?php

			}

		}

		public function footer_handler() {

			if( self::$add_script ) {

				if(fpd_get_option('fpd_jquery_no_conflict')) {
					wp_add_inline_script( 'jquery-fpd' , '$ = jQuery.noConflict();', 'before');
				}

				wp_enqueue_script( 'jquery-fpd' );
				if( fpd_get_option('fpd_sharing') )
					wp_enqueue_script( 'fpd-jssocials' );

			}

		}

	}

}

new FPD_Scripts_Styles();

?>