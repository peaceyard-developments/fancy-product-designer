<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin_Scripts_Styles') ) {

	class FPD_Admin_Scripts_Styles {

		const REACT_NO_CONFLICT_JS = 'window.lodash = _.noConflict(); window.underscore = _.noConflict();';

		public function __construct() {

			add_action( 'init', array( &$this, 'register'), 20 );
			add_action( 'admin_head', array( &$this, 'global_admin_head'), 100 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles_scripts' ), 1000 );
			add_action( 'admin_head-fancy-product-designer_page_fpd_ui_layout_composer', array( &$this, 'print_css_string' ), 100 );

		}

		public function global_admin_head () {
			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					/*----- NOTIFICATIONS ----------*/

					jQuery('body').on('click', '.fpd-dismiss-notification .notice-dismiss', function(evt) {

						evt.preventDefault();

						jQuery.ajax({
							url: '<?php echo admin_url('admin-ajax.php'); ?>',
							data: {
								action: 'fpd_dismiss_notification',
								_ajax_nonce: '<?php echo FPD_Admin::$ajax_nonce; ?>',
								name: this.value
							},
							type: 'post',
							dataType: 'json',
							success: function(data) {}
						});

						jQuery(this).parent('.fpd-dismiss-notification').remove();

					});

				});



			</script>
			<?php
		}

		public function register() {

			//register radykal css files
			wp_register_style( 'radykal-select2', plugins_url('/vendors/css/select2.min.css', __FILE__), false, '4.0.3' );
			wp_register_style( 'fpd-admin', plugins_url('/css/admin.css', __FILE__), array(
			), Fancy_Product_Designer::VERSION );

			//register radykal js files
			wp_register_script( 'radykal-select2', plugins_url('/vendors/js/select2.min.js', __FILE__), array( 'jquery' ), '4.0.3' );
			wp_register_script( 'radykal-select-sortable', plugins_url('/vendors/js/selectSortable.js', __FILE__), array( 'jquery' ), '1.0.0' );
			wp_register_script( 'fpd-admin', plugins_url('/js/admin.js', __FILE__), array(
				'jquery',
			), Fancy_Product_Designer::VERSION );

			wp_register_style( 'fpd-mdi', plugins_url('/vendors/mdi/css/materialdesignicons.min.css', __FILE__), '4.5.95' );
			wp_register_style( 'fpd-alertifyjs', plugins_url('/vendors/alertifyjs/css/alertify.min.css', __FILE__), '1.12.0' );
			wp_register_style( 'fpd-alertifyjs-default', plugins_url('/vendors/alertifyjs/css/themes/default.min.css', __FILE__), '1.12.0' );
			wp_register_style( 'fpd-semantic-ui', plugins_url('/vendors/semantic-ui/semantic.min.css', __FILE__), array(
				'fpd-mdi',
				'fpd-alertifyjs',
				'fpd-alertifyjs-default'
			) );

			wp_register_script( 'fpd-alertifyjs', plugins_url('/vendors/alertifyjs/alertify.min.js', __FILE__), '1.12.0' );
			wp_register_script( 'fpd-semantic-ui', plugins_url('/vendors/semantic-ui/semantic.js', __FILE__), array(
				'fpd-alertifyjs'
			) );

		}

		public function print_css_string() {

			//get css (colors)
			$selected_layout_id =  isset($_POST['fpd_selected_layout']) ? sanitize_key($_POST['fpd_selected_layout']) : 'default';
			$ui_layout = FPD_UI_Layout_Composer::get_layout($selected_layout_id);
			$css_str = FPD_UI_Layout_Composer::get_css_from_layout($ui_layout);

			if( !empty($css_str) )
				echo '<style type="text/css">'.$css_str.'</style>';

		}

		public function enqueue_styles_scripts( $hook ) {

			global $post;

			$version = Fancy_Product_Designer::LOCAL ? time() : Fancy_Product_Designer::VERSION;

			//order viewer
			require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/order-viewer.php' );
			$order_viewer_opts = array(
				'labels' => json_encode( FPD_Labels_Order_Viewer::get_labels() ),
				'templatesDirectory' => plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP ),
				'printReadyExportEnabled' => class_exists('Fancy_Product_Designer_Export'),
				'options' => array(
					'enabled_fonts' => json_decode(FPD_Fonts::to_json(FPD_Fonts::get_enabled_fonts())),
					'fpd_depositphotosApiKey' => fpd_get_option('fpd_depositphotosApiKey'),
					'fpd_depositphotosUsername' => fpd_get_option('fpd_depositphotosUsername'),
					'fpd_depositphotosPassword' => fpd_get_option('fpd_depositphotosPassword'),
					'fpd_depositphotosImageSize' => fpd_get_option('fpd_depositphotosImageSize'),
				)
			);

			wp_localize_script( 'fpd-admin', 'fpd_admin_opts', array(
					'adminAjaxUrl' => admin_url('admin-ajax.php'),
					'ajaxNonce' => FPD_Admin::$ajax_nonce,
					'adminUrl' => admin_url(),
					'localTest' => Fancy_Product_Designer::LOCAL
				)
			);

		    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

				//wc order details
		        if( 'shop_order' === $post->post_type ) {

			        wp_enqueue_style( 'fpd-react-order-viewer', plugins_url('/react-app/css/wc-order-viewer.css', __FILE__), array(
						'fpd-semantic-ui',
						'jquery-fpd'
					), $version );

					wp_enqueue_script( 'fpd-react-order-viewer', plugins_url('/react-app/js/wc-order-viewer.js', __FILE__), array(
						'fpd-semantic-ui',
						'fpd-admin',
						'jquery-fpd',
					), Fancy_Product_Designer::VERSION);

		        }
		        //post/pages
		        else {

			        wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_style( 'fpd-admin' );
					wp_enqueue_style( 'fpd-semantic-ui' );

					wp_enqueue_script( 'wp-color-picker' );
					wp_enqueue_script( 'fpd-admin' );

					if( !wp_script_is('select2') && !wp_script_is('select2-avada-js') ) {
						wp_enqueue_style( 'radykal-select2' );
						wp_enqueue_script( 'radykal-select2' );
					}
					wp_enqueue_script( 'radykal-select-sortable' );

		        }
		    }

			//manage fancy products
		    if( $hook == 'toplevel_page_fancy_product_designer' ) {

			     require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/products.php' );

			    wp_enqueue_media();

				wp_enqueue_style( 'fpd-react-products', plugins_url('/react-app/css/products.css', __FILE__), array(
					'fpd-semantic-ui'
				), $version );

				wp_enqueue_script( 'fpd-react-products', plugins_url('/react-app/js/products.js', __FILE__), array(
					'jquery-ui-core',
					'jquery-ui-mouse',
					'jquery-ui-sortable',
					'jquery-ui-droppable',
					'fpd-semantic-ui',
					'fpd-admin'
				), $version );

				wp_add_inline_script( 'fpd-react-products', self::REACT_NO_CONFLICT_JS, 'after' );

				wp_localize_script( 'fpd-react-products', 'fpd_fancy_products_opts', array(
					'labels' => json_encode(FPD_Labels_Products::get_labels()),
					'productBuilderUri' => admin_url().'admin.php?page=fpd_product_builder',
					'currentUserId' => get_current_user_id(),
					'dokanUsers' => class_exists('WeDevs_Dokan') ? get_users( array('fields' => array('ID', 'user_nicename')) ) : null,
				));

			}

			//product builder
		    if( $hook == 'fancy-product-designer_page_fpd_product_builder' ) {

			    require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/product-builder.php' );

		    	wp_enqueue_media();

		    	wp_enqueue_style( 'fpd-react-product-builder', plugins_url('/react-app/css/product-builder.css', __FILE__), array(
			    	'radykal-select2',
			    	'jquery-fpd',
					'fpd-semantic-ui'
				), $version );

				wp_enqueue_script( 'fpd-react-product-builder', plugins_url('/react-app/js/product-builder.js', __FILE__), array(
					'jquery-ui-core',
					'jquery-ui-mouse',
					'jquery-ui-sortable',
					'jquery-ui-droppable',
					'fpd-semantic-ui',
					'radykal-select2',
					'fpd-admin',
					'jquery-fpd'
				), $version );

				wp_add_inline_script( 'fpd-react-product-builder', self::REACT_NO_CONFLICT_JS, 'after' );

				$script_options = FPD_Resource_Options::get_options(array(
					'fpd_common_parameter_originX',
					'fpd_common_parameter_originY',
					'fpd_uploadZonesTopped',
					'fpd_fabricjs_texture_size',
					'fpd_font',
					'enabled_fonts',
					'primary_layout_props',
					'design_categories',
					'plus_enabled',
					'fpd_custom_texts_parameter_maxFontSize',
					'fpd_custom_texts_parameter_patterns',
					'fpd_designs_parameter_patterns'
				));

				$script_options['templates_directory'] = plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP );
				$script_options['products'] = FPD_Resource_Products::get_products( array('limit' => -1) );
				$script_options['adminUrl'] = admin_url();
				$script_options['labels'] = FPD_Labels_Product_Builder::get_labels();

				wp_localize_script( 'fpd-react-product-builder', 'fpd_product_builder_opts', $script_options );

		    }

		    //ui composer
		    if( $hook == 'fancy-product-designer_page_fpd_ui_layout_composer' ) {

			    require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/ui-composer.php' );

				wp_enqueue_style( 'fpd-ui-layout-composer', plugins_url('/react-app/css/ui-composer.css', __FILE__), array(
					'fpd-semantic-ui',
					'fpd-admin',
					'jquery-fpd'
				), $version );

				wp_enqueue_script( 'fpd-ui-layout-composer', plugins_url('/react-app/js/ui-composer.js', __FILE__), array(
					'jquery-ui-core',
					'jquery-ui-mouse',
					'jquery-ui-sortable',
					'jquery-ui-droppable',
					'fpd-semantic-ui',
					'jquery-fpd',
					'fpd-admin'
				), $version );

				wp_add_inline_script( 'fpd-ui-layout-composer', self::REACT_NO_CONFLICT_JS, 'after' );

				$script_options = array(
					'labels' => json_encode(FPD_Labels_UI_Composer::get_labels()),
					'templates_directory' => plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP ),
					'languages' => FPD_Resource_Options::get_languages(),
					'dynamic_designs_modules' => fpd_get_option('fpd_dynamic_designs_modules')
				);

				wp_localize_script( 'fpd-ui-layout-composer', 'fpd_ui_layout_composer_opts', $script_options );

			}

			//manage designs
		    if( $hook == 'fancy-product-designer_page_fpd_manage_designs') {

			    require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/designs.php' );

		    	wp_enqueue_media();

		    	wp_enqueue_style( 'fpd-react-designs', plugins_url('/react-app/css/designs.css', __FILE__), array(
					'fpd-semantic-ui'
				), $version );

				wp_enqueue_script( 'fpd-nestedSortable', plugins_url('/vendors/js/jquery.mjs.nestedSortable.js', __FILE__), array(
					'jquery',
					'jquery-ui-sortable',
					'fpd-admin'
				), $version );

				wp_enqueue_script( 'fpd-react-designs', plugins_url('/react-app/js/designs.js', __FILE__), array(
					'fpd-semantic-ui',
				), $version );

				wp_add_inline_script( 'fpd-react-designs', self::REACT_NO_CONFLICT_JS, 'after' );

				wp_localize_script( 'fpd-react-designs', 'fpd_fancy_designs_opts', array(
					'labels' => json_encode(FPD_Labels_Designs::get_labels()),
				));

		    }

		    //shortcode orders
		    if( $hook == 'fancy-product-designer_page_fpd_orders' ) {

				wp_enqueue_style( 'fpd-shortcode-orders', plugins_url('/react-app/css/shortcode-orders.css', __FILE__), array(
					'fpd-semantic-ui',
					'jquery-fpd'
				), $version );

				wp_enqueue_script( 'fpd-admin' );

				wp_enqueue_script( 'fpd-shortcode-orders', plugins_url('/react-app/js/shortcode-orders.js', __FILE__), array(
					'fpd-semantic-ui',
					'jquery-fpd'
				), $version );

				wp_add_inline_script( 'fpd-shortcode-orders', self::REACT_NO_CONFLICT_JS, 'after' );
				wp_localize_script( 'fpd-shortcode-orders', 'fpd_order_viewer_opts', $order_viewer_opts );

	        }

			//settings
			if( $hook == 'fancy-product-designer_page_fpd_settings') {

				require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/settings.php' );

				wp_enqueue_media();

				wp_enqueue_style( 'fpd-react-settings', plugins_url('/react-app/css/settings.css', __FILE__), array(
					'fpd-semantic-ui'
				), $version );

				wp_enqueue_script( 'fpd-react-settings', plugins_url('/react-app/js/settings.js', __FILE__), array(
					'fpd-semantic-ui',
					'fpd-admin'
				), $version );

				wp_add_inline_script( 'fpd-react-settings', self::REACT_NO_CONFLICT_JS, 'after' );

				$api_key = get_option( 'fpd_ae_admin_api_key', '' );

				wp_localize_script( 'fpd-react-settings', 'fpd_settings_opts', array(
					'labels' => FPD_Labels_Settings::get_labels(),
					'configs' => array(
						'plus_enabled' => class_exists('Fancy_Product_Designer_Plus'),
						'all_options' => FPD_Settings::$radykal_settings->settings,
						'option_tabs' => FPD_Settings::$radykal_settings->tabs,
						'option_blocks' => FPD_Settings::$radykal_settings->block_titles,
					),
					'languages' => FPD_Resource_Options::get_languages(),
					'automated_export_disabled' => empty($api_key) && !class_exists('Fancy_Product_Designer_Export')
				) );

			}

			//status
			if( $hook == 'fancy-product-designer_page_fpd_status') {

				wp_enqueue_style( 'fpd-semantic-ui' );
				wp_enqueue_style( 'fpd-admin' );
				wp_enqueue_style( 'jquery-fpd-static' );

				wp_enqueue_script( 'fpd-semantic-ui' );
				wp_enqueue_script( 'fpd-admin' );
				wp_enqueue_script( 'jquery-fpd' );

				wp_enqueue_script( 'fpd-status-tools', plugins_url('/js/status-tools.js', __FILE__), false, $version );

				wp_localize_script( 'fpd-status-tools', 'fpd_status_tools_opts', array(
						'label_no_valid_url' => __('Entered string is not a valid URL!', 'radykal')
					)
				);

			}

			$order_id = 0;
			if( isset($_GET['post']) ) //wc
				$order_id = $_GET['post'];
			else if( isset($_GET['lid']) ) //gravity form
				$order_id = $_GET['lid'];

			$order_type = 'wc';
			if( isset($_GET['page']) ) {

				if( $_GET['page'] == 'fpd_orders')
					$order_type = 'shortcode';
				else if( $_GET['page'] == 'gf_entries' )
					$order_type = 'gf';

			}

			wp_add_inline_script( 'fpd-react-order-viewer', self::REACT_NO_CONFLICT_JS, 'after' );
			wp_localize_script( 'fpd-react-order-viewer', 'fpd_order_viewer_opts', $order_viewer_opts );

		}
	}
}

new FPD_Admin_Scripts_Styles();

?>