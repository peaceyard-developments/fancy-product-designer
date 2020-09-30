<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin') ) {

	class FPD_Admin {

		public static $ajax_nonce;
		public static $admin_notice = null;

		public function __construct() {

			require_once( FPD_PLUGIN_ADMIN_DIR . '/fpd-admin-functions.php' );

			//Resources
			require_once( FPD_PLUGIN_ADMIN_DIR . '/resources/class-resource-products.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/resources/class-resource-templates.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/resources/class-resource-views.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/resources/class-resource-options.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/resources/class-resource-ui-layouts.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/resources/class-resource-designs.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/resources/class-resource-orders.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/resources/class-resource-pricing-rules.php' );

			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-ajax.php');
			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-scripts-styles.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-menus.php' );

			add_action( 'admin_init', array( &$this, 'init_admin' ) );
			add_filter( 'admin_body_class', array(&$this, 'add_body_classes') );
			add_action( 'add_meta_boxes', array( &$this, 'add_custom_box' ) );
			add_action( 'save_post', array( &$this,'update_custom_meta_fields' ) );
			add_action( 'admin_notices',  array( &$this, 'display_admin_notices' ) );
			add_filter( 'upload_mimes', array( &$this, 'allow_svg_upload') );
			add_filter( 'plugin_row_meta', array( &$this, 'add_plugin_nav_link'), 10, 2 );

			add_action( 'woocommerce_product_duplicate', array( &$this, 'duplicate_fancy_product' ), 10, 2 );
			add_action( 'admin_footer-post.php', array( &$this, 'add_modal' ) );
			add_action( 'admin_footer-post-new.php', array( &$this, 'add_modal' ) );

			//remove temp dir for files when mail has sent
			add_action( 'phpmailer_init', array(&$this, 'init_phpmailer') );

			//delete category parameters if design category is deleted
			add_action( 'delete_term',  array( &$this, 'term_delete' ), 10, 4 );

		}

		public function init_admin() {

			self::$ajax_nonce = wp_create_nonce( 'fpd_ajax_nonce' );

			//add capability to administrator
			$role = get_role( 'administrator' );
			$role->add_cap( Fancy_Product_Designer::CAPABILITY );

			if ( get_option('fpd_plugin_activated', false) ) {

				delete_option('fpd_plugin_activated');
				wp_redirect( esc_url_raw( admin_url('admin.php?page=fancy_product_designer&info=activated') ) );
				exit;

		    }

		    //delete sharing images
			if( intval(fpd_get_option('fpd_sharing_cache_days')) !== 0 && get_transient('fpd_check_shares_dir') === false ) {

				$cache_days_in_sec = intval(fpd_get_option('fpd_sharing_cache_days')) * DAY_IN_SECONDS;
			    $share_dir = FPD_WP_CONTENT_DIR . '/uploads/fpd_shares/';
			    $files_in_share_dir = glob($share_dir.'*');

			    if( is_array($files_in_share_dir) ) {

				     $dirs = array_filter($files_in_share_dir, 'is_dir');

				    foreach($dirs as $dir) {
					    $time = strtotime(basename($dir)); //folder date in seconds
					    $seconds = time() - $time; //past seconds
					    if($seconds > $cache_days_in_sec) {
						    $this->delete_files($dir);
					    }
				    }

			    }

			    set_transient('fpd_check_shares_dir', 'yes', DAY_IN_SECONDS);

			}

			//ui&layout composer actions
			if( isset($_POST['fpd_selected_layout']) ) {

				check_admin_referer( 'fpd_save_layout' );

				$selected_layout_id =  sanitize_key($_POST['fpd_selected_layout']);

				if( $_POST['fpd_method'] == 'save' || $_POST['fpd_method'] == 'save-new' ) {

					$saved_layout = json_decode(stripslashes($_POST['fpd_ui_layout']));

					$save_result = FPD_UI_Layout_Composer::save_layout( $selected_layout_id, $saved_layout);

					FPD_Admin::set_notice( $save_result );


				}
				else if( $_POST['fpd_method'] == 'delete' ) {

					delete_option( 'fpd_ui_layout_'.$selected_layout_id );
					$selected_layout_id = 'default';

				}

			}

		}

		//add meta box in the post and page
		public function add_custom_box() {

			add_meta_box(
				'fpd-meta-box',
				__('Fancy Product Designer', 'radykal'),
				array( &$this, 'output_meta_box'),
				'post',
				'side'
			);

			add_meta_box(
				'fpd-meta-box',
				__('Fancy Product Designer', 'radykal'),
				array( &$this, 'output_meta_box'),
				'page',
				'side'
			);


			$custom_post_types = get_post_types( array(
				'public' => true,
				'_builtin' => false
			));

			foreach($custom_post_types as $custom_post_type) {

				add_meta_box(
					'fpd-meta-box',
					__('Fancy Product Designer', 'radykal'),
					array( &$this, 'output_meta_box'),
					$custom_post_type,
					'side'
				);

			}

		}

		public function output_meta_box() {

			global $wpdb, $post;

			$custom_fields = get_post_custom($post->ID);

			require_once(FPD_PLUGIN_ADMIN_DIR.'/views/post-meta-box.php');

		}

		public function update_custom_meta_fields( $post_id )	{

			$post_type = get_post_type( $post_id );

			//disable autosave,so custom fields will not be empty
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		        return $post_id;

			//prevents custom metas to get deleted when saving via quick edit
		    if ( $post_type === 'product' && isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
				return $post_id;

			if ($post_type === 'product' &&  isset($_POST['bulk_edit']))
				return $post_id;

			if(isset($_POST["fpd_product_settings"]))
				update_post_meta( $post_id, 'fpd_product_settings', htmlentities($_POST['fpd_product_settings']) );

			//DESKTOP
			if(isset($_POST["fpd_source_type"]))
				update_post_meta( $post_id, 'fpd_source_type', $_POST['fpd_source_type'] );

			if(isset($_POST["fpd_products"]))
				update_post_meta( $post_id, 'fpd_products', $_POST['fpd_products'] );

			if(isset($_POST["fpd_product_categories"]))
				update_post_meta( $post_id, 'fpd_product_categories', $_POST['fpd_product_categories'] );

			//MOBILE
			if(isset($_POST["fpd_source_type_mobile"]))
				update_post_meta( $post_id, 'fpd_source_type_mobile', $_POST['fpd_source_type_mobile'] );

			if(isset($_POST["fpd_products_mobile"]))
				update_post_meta( $post_id, 'fpd_products_mobile', $_POST['fpd_products_mobile'] );

			if(isset($_POST["fpd_product_categories_mobile"]))
				update_post_meta( $post_id, 'fpd_product_categories_mobile', $_POST['fpd_product_categories_mobile'] );

		}

		public function display_admin_notices() {

			global $woocommerce;

			if( FPD_Admin::$admin_notice ) {

				fpd_output_admin_notice(
					FPD_Admin::$admin_notice['type'],
					'',
					FPD_Admin::$admin_notice['message']
				);

				FPD_Admin::unset_notice();

			}

			fpd_output_admin_notice(
				'error',
				'',
				__( 'Please update WooCommerce to the latest version! Fancy Product Designer only works with version 3.0 or newer.', 'radykal' ),
				function_exists('get_woocommerce_currency') && version_compare($woocommerce->version, '3.0', '<')
			);

			fpd_output_admin_notice(
				'error',
				'',
				__( 'GD library is not installed on your web server. If you do not know how to install GD library, please ask your server provider!', 'radykal' ),
				!extension_loaded('gd') || !function_exists('gd_info')
			);

			fpd_output_admin_notice(
				'error',
				'',
				__( 'Fancy Product Designer requires a minimum PHP version of 5.4!', 'radykal' ),
				version_compare(phpversion(), '5.4', '<')
			);

			fpd_output_admin_notice(
				'error',
				'',
				sprintf( __( 'Please update Multistep Product Configurator plugin. Minimum version %s required!', 'radykal' ), Fancy_Product_Designer::MSPC_MIN_VERSION),
				class_exists('Multistep_Product_Configurator') && version_compare(Multistep_Product_Configurator::VERSION, Fancy_Product_Designer::MSPC_MIN_VERSION, '<')
			);

			fpd_output_admin_notice(
				'error',
				'',
				sprintf( __( 'Please update Fancy Product Designer REST API plugin. Minimum version %s required!', 'radykal' ), Fancy_Product_Designer::REST_API_MIN_VERSION),
				class_exists('FPD_Rest_Api') && (defined('FPD_Rest_Api::VERSION') && version_compare(FPD_Rest_Api::VERSION, Fancy_Product_Designer::REST_API_MIN_VERSION, '<') )
			);

			fpd_output_admin_notice(
				'error',
				 __( 'allow_url_fopen is disabled', 'radykal' ),
				 __( 'For some features (Facebook/Instagram Images & Loading Google Webfonts) in Fancy Product Designer <i>allow_url_fopen</i> needs to be enabled in the php.ini. Please enable <i>allow_url_fopen</i> in your php.ini. <a href="http://php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">What is allow_url_fopen?</a>', 'radykal' ),
				 !ini_get('allow_url_fopen'),
				 'allow_url_fopen',
				 true
			);

			fpd_output_admin_notice(
				'success',
				 __( 'Fancy Product Designer successfully updated', 'radykal' ),
				 __( 'Please check out the <a href="http://support.fancyproductdesigner.com/support/discussions/forums/5000283646" target="_blank">Changelog</a> and <a href="http://support.fancyproductdesigner.com/support/solutions/articles/5000582931-changelog-upgrading" target="_blank">Upgrading</a> instructions.', 'radykal' ),
				 get_option( 'fpd_update_notice', false )
			);

			update_option( 'fpd_update_notice', false );

		}

		public function allow_svg_upload( $upload_mimes ) {

			$upload_mimes['svg'] = 'image/svg+xml';
			return $upload_mimes;

		}

		public function add_plugin_nav_link( $links, $file ) {

			if ( strpos( $file, 'fancy-product-designer.php' ) !== false ) {

				$new_links = array(
						'doc' => '<a href="http://support.fancyproductdesigner.com/support/solutions/5000115463" aria-label="' . esc_attr__( 'View Documentation', 'radykal' ) . '" target="_blank">' . esc_html__( 'Documentation', 'radykal' ) . '</a>',
						'changelog' => '<a href="http://support.fancyproductdesigner.com/support/discussions/forums/5000283646" aria-label="' . esc_attr__( 'View Changelog', 'radykal' ) . '" target="_blank">' . esc_html__( 'Changelog', 'radykal' ) . '</a>'
						);

				$links = array_merge( $links, $new_links );
			}

			return $links;

		}

		//duplicate fancy products, all views will be available in the duplicated product
		public function duplicate_fancy_product( $duplicate, $product ) {

			$custom_fields = get_post_custom($product->get_id());

			if(isset($custom_fields["fpd_product_categories"]))
				update_post_meta( $duplicate->get_id(), 'fpd_product_categories', maybe_unserialize($custom_fields['fpd_product_categories'][0]) );

			if(isset($custom_fields["fpd_products"]))
				update_post_meta( $duplicate->get_id(), 'fpd_products', maybe_unserialize($custom_fields['fpd_products'][0]) );

			if(isset($custom_fields["fpd_source_type"]))
				update_post_meta( $duplicate->get_id(), 'fpd_source_type', $custom_fields['fpd_source_type'][0] );

			if(isset($custom_fields["fpd_product_settings"]))
				update_post_meta( $duplicate->get_id(), 'fpd_product_settings', $custom_fields['fpd_product_settings'][0] );

		}

		public function add_modal() {

			global $post;

			$screen = get_current_screen();
			if($screen->post_type !== 'shop_order')
				require_once(FPD_PLUGIN_ADMIN_DIR.'/views/modal-individual-product-settings.php');

		}

		public static function set_notice( $notice ) {

			FPD_Admin::$admin_notice = $notice;

		}

		public static function unset_notice() {

			FPD_Admin::$admin_notice = null;

		}

		public function init_phpmailer( $phpmailer ) {

			$phpmailer->action_function = array(&$this, 'wp_mail_sent');

		}

		public function wp_mail_sent( $is_sent, $to, $cc, $bcc, $subject, $body, $from ) {

			if( file_exists(FPD_TEMP_DIR) ) {

				$files = glob(FPD_TEMP_DIR."*");
				$now   = time();

				foreach ($files as $file) {
					if (is_file($file)) {
				    	if ($now - filemtime($file) >= 60 * 3) { // delete files older than 3 mins
				        	unlink($file);
				      	}
					}
				}

			}

		}

		public function add_body_classes( $classes ) {

			$screen = get_current_screen();
			$screen_id = $screen->id;

			if( strpos($screen_id, 'fancy_product_designer') !== false || strpos($screen_id, 'fancy-product-designer') !== false )
				$classes .= ' fpd-backend';

			return $classes;

		}

		public function term_delete( $term_id, $tax_id, $tax_slug, $term ) {

			delete_option( 'fpd_category_parameters_'.$term->slug );

		}

		private function delete_files($dir) {

		    if (!file_exists($dir)) {
		        return true;
		    }

		    if (!is_dir($dir)) {
		        return unlink($dir);
		    }

		    foreach (scandir($dir) as $item) {

		        if ($item == '.' || $item == '..') {
		            continue;
		        }

		        if (!$this->delete_files($dir . DIRECTORY_SEPARATOR . $item)) {
		            return false;
		        }

		    }

		    return rmdir($dir);

		}
	}
}

new FPD_Admin();

?>