<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Frontend_Product')) {

	class FPD_Frontend_Product {

		public static $form_views = null;
		public static $remove_watermark = false;

		public function __construct() {

			require_once(FPD_PLUGIN_DIR.'/inc/api/class-parameters.php');
			require_once(FPD_PLUGIN_DIR.'/inc/class-share.php');
			require_once(FPD_PLUGIN_DIR.'/inc/class-user-products.php');

			add_action( 'wp_head', array( &$this, 'head_frontend') );

			//SINGLE PRODUCT
			add_filter( 'body_class', array( &$this, 'add_body_classes') );
			add_action( 'fpd_after_product_designer', array( &$this, 'output_shortcode_js'), 1 );

			//order via shortcode
			add_shortcode( 'fpd', array( &$this, 'fpd_shortcode_handler') );
			add_shortcode( 'fpd_form', array( &$this, 'fpd_form_shortcode_handler') );
			add_action( 'wp_ajax_fpd_newshortcodeorder', array( &$this, 'create_shortcode_order' ) );
			add_action( 'wp_ajax_nopriv_fpd_newshortcodeorder', array( &$this, 'create_shortcode_order' ) );

			//action shortcode
			add_shortcode( 'fpd_action', array( &$this, 'shortcode_action_handler') );

			//module shortcode
			add_shortcode( 'fpd_module', array( &$this, 'shortcode_module_handler') );

			//print-ready download for customers
			add_action( 'wp_ajax_fpd_pr_export', array( &$this, 'create_print_ready_file' ) );
			add_action( 'wp_ajax_nopriv_fpd_pr_export', array( &$this, 'create_print_ready_file' ) );

			//instagram access token
			add_action( 'wp_ajax_fpd_insta_get_token', array( &$this, 'insta_get_token' ) );
			add_action( 'wp_ajax_nopriv_fpd_insta_get_token', array( &$this, 'insta_get_token' ) );

			//custom image upload
			add_action( 'wp_ajax_fpd_custom_uplod_file', array( &$this, 'custom_uplod_file' ) );
			add_action( 'wp_ajax_nopriv_fpd_custom_uplod_file', array( &$this, 'custom_uplod_file' ) );

		}

		public function head_frontend() {

			if( !is_admin() ) {

				global $post;
				if( isset($post->ID) && is_fancy_product( $post->ID ) ) {

					$product_settings = new FPD_Product_Settings( $post->ID );
					$main_bar_pos = $product_settings->get_option('main_bar_position');
					if( $main_bar_pos === 'shortcode' ) {
						add_shortcode( 'fpd_main_bar', array( &$this, 'return_main_bar_container') );
					}

					do_action( 'fpd_post_fpd_enabled', $post, $product_settings );

				}

			}

		}

		//add fancy-product class in body
		public function add_body_classes( $classes ) {

			global $post;

			if( isset($post->ID) && is_fancy_product( $post->ID ) ) {

				$product_settings = new FPD_Product_Settings( $post->ID );

				$classes[] = 'fancy-product';

				if( $product_settings->customize_button_enabled ) {
					$classes[] = 'fpd-customize-button-visible';
				}
				else {
					$classes[] = 'fpd-customize-button-hidden';
				}

				//check if tablets are supported
				if( fpd_get_option( 'fpd_disable_on_tablets' ) )
					$classes[] = 'fpd-hidden-tablets';

				//check if smartphones are supported
				if( fpd_get_option( 'fpd_disable_on_smartphones' ) )
					$classes[] = 'fpd-hidden-smartphones';

				if( $product_settings->get_option( 'fullwidth_summary' ) || ( isset($_GET['order']) && isset($_GET['item_id']) ) )
					$classes[] = 'fpd-fullwidth-summary';

				if( $product_settings->get_option('hide_product_image') || ( isset($_GET['order']) && isset($_GET['item_id']) ) )
					$classes[] = 'fpd-product-images-hidden';

				if( $product_settings->get_option('get_quote') )
					$classes[] = 'fpd-get-quote-enabled';

				if( $product_settings->get_option('customization_required') != 'none' )
					$classes[] = 'fpd-customization-required';

				if( isset($_GET['order']) && isset($_GET['item_id']) )
					$classes[] = 'fpd-order-display';

			}

			return $classes;

		}

		//return main bar container
		public function return_main_bar_container() {

			return '<div class="fpd-main-bar-position"></div>';

		}

		//the actual product designer will be added
		public static function add_product_designer() {

			global $post;

			$product_settings = new FPD_Product_Settings( $post->ID );
			$visibility = $product_settings->get_option('product_designer_visibility');

			//do not show in lightbox when viewing order
			$visibility = ( isset($_GET['order']) && isset($_GET['item_id']) ) ? false : $visibility;

			if( $product_settings->show_designer() ) {

				do_action( 'fpd_before_product_designer' );

				//load product from share
				if( isset($_GET['share_id']) ) {

					$transient_key = 'fpd_share_'.$_GET['share_id'];
					$transient_val = get_transient($transient_key);
					if($transient_val !== false)
						self::$form_views = stripslashes($transient_val['product']);

				}
				else if( isset($_GET['fpd_saved_product']) ) {

					$current_user_id = get_current_user_id();

					if( $current_user_id !== 0 ) {

						$post_id = intval($post->ID);

						$saved_products = get_user_meta( $current_user_id, 'fpd_saved_product_'.$_GET['fpd_saved_product'], true );

						if( $saved_products && isset($saved_products['fpd_data']) )
							self::$form_views = json_encode($saved_products['fpd_data']);

					}

				}

				FPD_Scripts_Styles::$add_script = true;


				//get availabe fonts
				if($product_settings->get_option('font_families[]') === false) {
					$available_fonts = FPD_Fonts::get_enabled_fonts();
				}
				else {

					$available_fonts = array();
					$enabled_fonts = FPD_Fonts::get_enabled_fonts();
					$ind_product_fonts = $product_settings->get_option('font_families[]');
					if( !is_array($ind_product_fonts) ) //only when one is set
						$ind_product_fonts = str_split($ind_product_fonts, strlen($ind_product_fonts));

					//search for font url from enabled fonts
					foreach($ind_product_fonts as $value) {
						$font_key = array_search($value, $enabled_fonts);
						if( gettype($font_key) === 'string' ) {
							$available_fonts[$font_key] = $value;
						}
						else {
							$available_fonts[] = $value;
						}
					}

				}

				//make default font
				$default_font = 'Arial';
				$db_default_font = fpd_get_option('fpd_font');
				if( !empty($db_default_font) )
					$default_font = $db_default_font;
				else if( $available_fonts && !empty($available_fonts) ) {
					$available_fonts_values = array_values($available_fonts);
					$default_font = array_shift($available_fonts_values); //get first array element
				}

				//get ui layout
				$ui_layout = FPD_UI_Layout_Composer::get_layout($product_settings->get_option('product_designer_ui_layout'));

				//remove slashes, happening since WC3.1.0
				if( !is_null(self::$form_views) ) {
					self::$form_views = fpd_strip_multi_slahes(self::$form_views);
				}

				//create guided tour json
				$guided_tour = 'null';
				if( isset($ui_layout['guided_tour']) ) {
					$guided_tour = $ui_layout['guided_tour'];

					if( defined('ICL_LANGUAGE_CODE') ) //wpml active
						$guided_tour = isset($guided_tour[ICL_LANGUAGE_CODE]) ? $guided_tour[ICL_LANGUAGE_CODE] : null;

					if( !empty($guided_tour) )
						$guided_tour = json_encode($guided_tour, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
					else
						$guided_tour = 'null';

				}

				$products_json_str = array();
				$source_type = $product_settings->get_source_type();

				//get assigned categories/products
				$fancy_content_ids = fpd_has_content( $product_settings->master_id );
				$fancy_content_ids = $fancy_content_ids === false ? array() : $fancy_content_ids;

				foreach($fancy_content_ids as $fancy_content_id) {

					if( empty($source_type) || $source_type == 'category' ) { //categories are used

						$fancy_category = new FPD_Category($fancy_content_id);

						if( $fancy_category->get_data() ) {

							$fancy_products_data = $fancy_category->get_products();

							$category_products = array();
							foreach($fancy_products_data as $fancy_product_data) {

								$fpd_product = new FPD_Product( $fancy_product_data->ID );
								$category_products[] = $fpd_product->to_JSON( false );

							}

							$products_json_str[] = array(
								'category' => esc_attr($fancy_category->get_data()->title),
								'products' => $category_products
							);
						}


					}
					else {

						$fpd_product = new FPD_Product( $fancy_content_id );
						$fpd_product_json = $fpd_product->to_JSON( false );
						if( !empty($fpd_product_json) )
							$products_json_str[] = $fpd_product_json;

					}

				}

				$products_json_str = json_encode($products_json_str, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

				//output designs
				$designs_json_str = 'null';
				if( !intval($product_settings->get_option('hide_designs_tab')) ) {



					$fpd_designs = new FPD_Designs(
						$product_settings->get_option('design_categories[]') ? $product_settings->get_option('design_categories[]') : array()
						,$product_settings->get_image_parameters()
					);

					$designs_json_str = $fpd_designs->get_json();

				}

				//create ID and class attribute
				$selector = 'fancy-product-designer-'.$product_settings->master_id.'';
				$selector_classes = str_replace( ' fpd-disable-touch-scrolling', '', $ui_layout['container_classes'] );
				$selector_classes .= ' '.($visibility == 'lightbox' ? 'fpd-hidden' : '');

				$product_id_layouts = $product_settings->get_option('layouts');
				$fpd_layouts = '[]';
				if( !empty($product_id_layouts) ) {

					$fpd_product_layouts = new FPD_Product( $product_id_layouts );
					$fpd_layouts= $fpd_product_layouts->to_JSON();

				}

				?>
				<div class="fpd-product-designer-wrapper">
					<div id="<?php echo $selector; ?>" class="<?php echo $selector_classes; ?>"></div>
				</div>
				<script type="text/javascript">

					//all designs for the product designer
					var fpdProductsJSON = <?php echo apply_filters( 'fpd_products_json_string', $products_json_str, $post->ID ) ; ?>,
						fpdDesignsJSON = <?php echo apply_filters( 'fpd_designs_json_string', $designs_json_str, $post->ID ); ?>;

					var fancyProductDesigner,
						$body,
						$selector,
						$productWrapper,
						$cartForm,
						$mainBarCon = null,
						$modalPrice = null,
						fpdProductCreated = false,
						fpdIsReady = false,
						adminAjaxURL = "<?php echo admin_url('admin-ajax.php'); ?>";

					jQuery(document).ready(function($) {

						//return;
						fabric.textureSize = <?php echo fpd_get_option('fpd_fabricjs_texture_size'); ?>;

						$body = jQuery('body');
						$selector = jQuery('#<?php echo $selector; ?>');
						$productWrapper = jQuery('.post-<?php echo $post->ID; ?>');
						$cartForm = jQuery('[name="fpd_product"]:first').parents('form:first');
						$mainBarCon = jQuery('.fpd-main-bar-position');

						//merge image parameters with custom image parameters
						var customImageParams = jQuery.extend(
							<?php echo $product_settings->get_image_parameters_string(); ?>,
							<?php echo $product_settings->get_custom_image_parameters_string(); ?>
						);

						var modalModeOpt = false;
						if(<?php echo intval($visibility == 'lightbox'); ?>) {
							modalModeOpt = '#fpd-start-customizing-button';
						}

						//get plugin options from UI Layout
						var uiLayoutOptions = <?php echo json_encode($ui_layout['plugin_options']); ?>,
							uiLayoutOptions = typeof uiLayoutOptions === 'object' ? uiLayoutOptions : {};

						//call fancy product designer plugin
						var pluginOptions = {
							langJSON: <?php echo FPD_Settings_Labels::get_labels_object_string(); ?>,
							fonts: <?php echo FPD_Fonts::to_json($available_fonts); ?>,
							templatesDirectory: "<?php echo plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP ); ?>",
							facebookAppId: "<?php echo fpd_get_option('fpd_facebook_app_id'); ?>",
							instagramClientId: "<?php echo fpd_get_option('fpd_instagram_client_id'); ?>",
							instagramRedirectUri: "<?php echo fpd_get_option('fpd_instagram_redirect_uri'); ?>",
							instagramTokenUri: adminAjaxURL+'?action=fpd_insta_get_token',
							zoomStep: <?php echo fpd_get_option('fpd_zoom_step'); ?>,
							maxZoom: <?php echo fpd_get_option('fpd_max_zoom'); ?>,
							hexNames: <?php echo FPD_Settings_Colors::get_hex_names_object_string(); ?>,
							replaceInitialElements: <?php echo $product_settings->get_option('replace_initial_elements'); ?>,
							lazyLoad: <?php echo fpd_get_option('fpd_lazy_load'); ?>,
							improvedResizeQuality: <?php echo fpd_get_option('fpd_improvedResizeQuality'); ?>,
							uploadZonesTopped: <?php echo fpd_get_option('fpd_uploadZonesTopped'); ?>,
							mainBarContainer: $mainBarCon.length ? $mainBarCon : false,
							responsive: <?php echo fpd_get_option('fpd_responsive'); ?>,
							priceFormat: {},
							modalMode: modalModeOpt,
							templatesType: ['php', 'html'],
							watermark: "<?php echo self::$remove_watermark ? '' : fpd_get_option('fpd_watermark_image'); ?>",
							loadFirstProductInStage: <?php echo self::$form_views === null ? 1 : 0; ?>,
							unsavedProductAlert: <?php echo fpd_get_option('fpd_unsaved_customizations_alert'); ?>,
							hideDialogOnAdd: <?php echo $product_settings->get_option('hide_dialog_on_add'); ?>,
							snapGridSize: [<?php echo fpd_get_option('fpd_action_snap_grid_width'); ?>, <?php echo fpd_get_option('fpd_action_snap_grid_height'); ?>],
							fitImagesInCanvas: <?php echo $product_settings->get_option('fitImagesInCanvas'); ?>,
							inCanvasTextEditing: <?php echo $product_settings->get_option('inCanvasTextEditing'); ?>,
							openTextInputOnSelect: <?php echo $product_settings->get_option('openTextInputOnSelect'); ?>,
							saveActionBrowserStorage: <?php echo fpd_get_option('fpd_accountProductStorage') ? 0 : 1; ?>,
							uploadAgreementModal: <?php echo fpd_get_option('fpd_uploadAgreementModal'); ?>,
							autoOpenInfo: <?php echo fpd_get_option('fpd_autoOpenInfo'); ?>,
							allowedImageTypes: <?php echo json_encode(fpd_get_option('fpd_allowedImageTypes', false)); ?>,
							replaceColorsInColorGroup: <?php echo fpd_get_option('fpd_replaceColorsInColorGroup'); ?>,
							pixabayApiKey: "<?php echo fpd_get_option('fpd_pixabayApiKey'); ?>",
							pixabayHighResImages: <?php echo fpd_get_option('fpd_pixabayHighResImages'); ?>,
							pixabayLang: "<?php echo fpd_get_option('fpd_pixabayLang'); ?>",
							openModalInDesigner: <?php echo fpd_get_option('fpd_openModalInDesigner'); ?>,
							imageSizeTooltip: <?php echo fpd_get_option('fpd_imageSizeTooltip'); ?>,
							applyFillWhenReplacing: <?php echo fpd_get_option('fpd_applyFillWhenReplacing'); ?>,
							highlightEditableObjects: "<?php echo fpd_get_option('fpd_highlightEditableObjects'); ?>",
							depositphotosApiKey: "<?php echo fpd_get_option('fpd_depositphotosApiKey'); ?>",
							depositphotosPrice: <?php echo fpd_get_option('fpd_depositphotosPrice'); ?>,
							depositphotosLang: "<?php echo fpd_get_option('fpd_depositphotosLang'); ?>",
							layouts: <?php echo $fpd_layouts; ?>,
							disableTextEmojis: <?php echo fpd_get_option('fpd_disableTextEmojis'); ?>,
							smartGuides: <?php echo fpd_get_option('fpd_smartGuides'); ?>,
							colorPickerPalette: <?php echo json_encode(fpd_string_to_array(fpd_get_option('fpd_color_colorPickerPalette'))); ?>,
							customizationRequiredRule: "<?php echo $product_settings->get_option('customization_required'); ?>",
							swapProductConfirmation: <?php echo fpd_get_option('fpd_swapProductConfirmation'); ?>,
							toolbarTextareaPosition: "<?php echo fpd_get_option('fpd_toolbarTextareaPosition'); ?>",
							setTextboxWidth: <?php echo $product_settings->get_option('setTextboxWidth'); ?>,
							textLinkGroupProps: <?php echo json_encode( fpd_get_option('fpd_textLinkGroupProps', false) ); ?>,
							dynamicDesigns: <?php echo get_option('fpd_dynamic_designs_modules', '{}'); ?>,
							customImageAjaxSettings: {
								url: adminAjaxURL+'?action=fpd_custom_uplod_file',
								data: {
									saveOnServer: <?php echo (int) (fpd_get_option('fpd_type_of_uploader') === 'php'); ?>,
									uploadsDir: "<?php echo FPD_WP_CONTENT_DIR . '/uploads/fancy_products_uploads/'; ?>",
									uploadsDirURL: "<?php echo content_url() . '/uploads/fancy_products_uploads/'; ?>"
								}
							},
							elementParameters: {
								originX: "<?php echo fpd_get_option('fpd_common_parameter_originX'); ?>",
								originY: "<?php echo fpd_get_option('fpd_common_parameter_originY'); ?>",
							},
							imageParameters: {
								colors: "<?php echo fpd_get_option('fpd_all_image_colors'); ?>",
								colorLinkGroup: "<?php echo fpd_get_option('fpd_all_image_colorLinkGroup'); ?>",
								colorPrices: <?php echo $product_settings->get_option('enable_image_color_prices') ? FPD_Settings_Colors::get_color_prices() : '{}'; ?>,
								replaceInAllViews: <?php echo $product_settings->get_option('designs_parameter_replaceInAllViews'); ?>,
								patterns: <?php echo json_encode(fpd_check_file_list($product_settings->get_option('designs_parameter_patterns'), FPD_WP_CONTENT_DIR . '/uploads/fpd_patterns_svg/')); ?>,
								padding:  0
							},
							textParameters: {
								padding:  <?php echo fpd_get_option('fpd_padding_controls'); ?>,
								fontFamily: "<?php echo $default_font; ?>",
								colorPrices: <?php echo $product_settings->get_option('enable_text_color_prices') ? FPD_Settings_Colors::get_color_prices() : '{}'; ?>,
								replaceInAllViews: <?php echo $product_settings->get_option('custom_texts_parameter_replaceInAllViews'); ?>,
								patterns: <?php echo json_encode(fpd_check_file_list($product_settings->get_option('custom_texts_parameter_patterns'), FPD_WP_CONTENT_DIR . '/uploads/fpd_patterns_text/')); ?>,
								strokeColors: <?php echo json_encode( FPD_Parameters::parse_property('strokeColors', fpd_get_option('fpd_all_text_strokeColors'), 'text') ); ?>,
								colors: "<?php echo fpd_get_option('fpd_all_text_colors'); ?>",
							},
							customImageParameters: customImageParams,
							customTextParameters: <?php echo $product_settings->get_custom_text_parameters_string(); ?>,
							fabricCanvasOptions: {
								allowTouchScrolling: <?php echo fpd_get_option('fpd_canvas_touch_scrolling'); ?>,
								perPixelTargetFind: <?php echo fpd_get_option('fpd_canvas_per_pixel_detection'); ?>,
							},
							qrCodeProps: {
								price: <?php echo fpd_get_option('fpd_qr_code_prop_price'); ?>,
								resizeToW: <?php echo fpd_get_option('fpd_qr_code_prop_resizeToW'); ?>,
								resizeToH: <?php echo fpd_get_option('fpd_qr_code_prop_resizeToW'); ?>,
								draggable: <?php echo fpd_get_option('fpd_qr_code_prop_draggable'); ?>,
								resizable: <?php echo fpd_get_option('fpd_qr_code_prop_resizable'); ?>,
								boundingBox: customImageParams.boundingBox,
								boundingBoxMode: customImageParams.boundingBoxMode
							},
							boundingBoxProps: {
								strokeWidth: <?php echo fpd_get_option('fpd_bounding_box_stroke_width'); ?>
							},
							imageEditorSettings: {
								masks: <?php echo json_encode(fpd_get_files_from_uploads_by_type('fpd_masks', array('svg')), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
							},
							guidedTour: <?php echo $guided_tour; ?>,
							productsJSON: fpdProductsJSON,
							designsJSON: fpdDesignsJSON
						};

						pluginOptions = jQuery.extend({}, pluginOptions, uiLayoutOptions);

						<?php do_action( 'fpd_before_js_fpd_init', $product_settings ); ?>
						fancyProductDesigner = new FancyProductDesigner($selector, pluginOptions);

						//when load from cart or order, use loadProduct
						$selector
						.on('uiSet', function() {

							//custom module placements
							jQuery('.fpd-sc-module-wrapper').each(function(i, moduleWrapper) {

								if(typeof FancyProductDesigner === 'undefined') {
									return;
								}

								var $moduleWrapper = jQuery(moduleWrapper),
									moduleType = $moduleWrapper.data('type'),
									$moduleClone = fancyProductDesigner.translatedUI.find('.fpd-modules > [data-module="'+moduleType+'"]').clone();

								$moduleClone.appendTo($moduleWrapper);

								switch(moduleType) {
									case 'products':
										new FPDProductsModule(fancyProductDesigner, $moduleClone);
										break;
									case 'text':
										new FPDTextModule(fancyProductDesigner, $moduleClone);
										break;
									case 'designs':
										new FPDDesignsModule(fancyProductDesigner, $moduleClone);
										break;
									case 'images':
										new FPDImagesModule(fancyProductDesigner, $moduleClone);
										break;
									case 'layouts':
										new FPDLayoutsModule(fancyProductDesigner, $moduleClone);
										break;
								}

								if(typeof FancyProductDesignerPlus !== 'undefined') {

									switch(moduleType) {
										case 'drawing':
											var fpdDrawingModule = new FPDDrawingModule(fancyProductDesigner, $moduleClone);
											fpdDrawingModule.drawCanvas.setWidth($moduleClone.width());
											break;
									}

								}

							});

						})
						.on('ready', function() {

							//load product from url query
							if(<?php echo self::$form_views === null ? 0 : 1; ?>) {

								var order = <?php echo empty(self::$form_views) ? 0 : self::$form_views; ?>,
									product = order.product ? order.product : order; //deprecated: getProduct() as used instead getOrder()

								fancyProductDesigner.toggleSpinner(true);
								fancyProductDesigner.loadProduct(product);

								//PLUS
								if(fancyProductDesigner.bulkVariations && fancyProductDesigner.bulkVariations.setup && order.bulkVariations) {
									fancyProductDesigner.bulkVariations.setup(order.bulkVariations);
								}

							}

							//requires login to upload images
							<?php $login_required = fpd_get_option('fpd_upload_designs_php_logged_in') !== 0 && !is_user_logged_in() ? 1 : 0; ?>
							if ( <?php echo $login_required; ?> ) {
								jQuery('.fpd-upload-zone').replaceWith('<p class="fpd-login-info"><?php echo htmlspecialchars_decode(FPD_Settings_Labels::get_translation( 'misc', 'login_required_info' )); ?></p>');
							}
							fpdIsReady = true;

							//add price to modal
							$modalPrice = jQuery('<span class="fpd-modal-price fpd-right"></span>');
							jQuery('.fpd-modal-product-designer .fpd-done').after($modalPrice);

							//shortcode: actions
							var $uiActions = fancyProductDesigner.translatedUI.children('.fpd-actions');
							jQuery('.fpd-sc-action-placeholder').each(function(i, item) {

								var $item = jQuery(item),
									actionName = $item.data('action'),
									layout = $item.data('layout'),
									$action = $uiActions.children('[data-action="'+actionName+'"]');

								var $cloneAction = $action.clone().addClass('fpd-sc-action fpd-layout--'+layout);

								$cloneAction.removeClass('fpd-disabled');

								if(layout === 'icon-text' || layout === 'text') {
									$cloneAction.removeClass('fpd-tooltip')
									.children(':first').after('<span class="fpd-label">'+$cloneAction.attr('title')+'</span>');
								}

								$cloneAction.click(function() {
									if(fancyProductDesigner && fancyProductDesigner.actions && fpdProductCreated) {
										fancyProductDesigner.actions.doAction(jQuery(this));
									}
								});

								jQuery(item).replaceWith($cloneAction);

							});

						})
						.on('productCreate', function() {

							fpdProductCreated = true;

							//calculate initial elemens length for customization required
							initialElementsLength = 0;
							fancyProductDesigner.getElements().forEach(function(view) {
								initialElementsLength += view.length;
							});

						})
						.on('undoRedoSet', function(evt, undos, redos) {

							var customizationChecker = false,
								jsMethod = fancyProductDesigner.mainOptions.customizationRequiredRule == 'all' ? 'every' : 'some';

							customizationChecker = fancyProductDesigner.viewInstances[jsMethod](function(viewInst) {
								return viewInst.isCustomized;
							})

							if(customizationChecker) {
								$body.removeClass('fpd-customization-required');
							}

						})
						.on('viewSelect', function() {

							jQuery('.fpd-sc-module-wrapper[data-type="names-numbers"]').each(function(i, moduleWrapper) {

								if(typeof FPDNamesNumbersModule !== 'undefined') {
									var $moduleWrapper = jQuery(moduleWrapper);
									FPDNamesNumbersModule.setup(fancyProductDesigner, $moduleWrapper.children('.fpd-module'));
								}

							});


						})
						.on('layersListUpdate', function() {

							jQuery('.fpd-sc-module-wrapper[data-type="manage-layers"]').each(function(i, moduleWrapper) {

								if(typeof FancyProductDesigner !== 'undefined') {

									var $moduleWrapper = jQuery(moduleWrapper);
									FPDLayersModule.createList(fancyProductDesigner, $moduleWrapper.children('.fpd-module'));

								}

							});

							jQuery('.fpd-sc-module-wrapper[data-type="text-layers"]').each(function(i, moduleWrapper) {

								if(typeof FancyProductDesigner !== 'undefined') {

									var $moduleWrapper = jQuery(moduleWrapper);
									FPDTextLayersModule.createList(fancyProductDesigner, $moduleWrapper.children('.fpd-module'));

								}

							});

						});

					});

				</script>

				<?php

				if( fpd_get_option('fpd_sharing') )
					echo FPD_Share::get_javascript();

				do_action('fpd_after_product_designer', $post);

			}

		}

		public function fpd_shortcode_handler( $atts ) {

			extract( shortcode_atts( array(
			), $atts, 'fpd' ) );

			ob_start();

			echo $this->add_customize_button();
			echo $this->add_product_designer();

			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		public function fpd_form_shortcode_handler( $atts ) {

			extract( shortcode_atts( array(
				'price_format' => '$%d',
			), $atts, 'fpd_form' ) );

			$name_placeholder = FPD_Settings_Labels::get_translation( 'misc', 'shortcode_form:name_placeholder' );
			$email_placeholder = FPD_Settings_Labels::get_translation( 'misc', 'shortcode_form:email_placeholder' );
			$submit_text = FPD_Settings_Labels::get_translation( 'misc', 'shortcode_form:send' );

			ob_start();
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {

					$selector.on('templateLoad', function(evt, url) {
						fancyProductDesigner.mainOptions.priceFormat.currency = "<?php echo empty($price_format) ? '' : $price_format; ?>";
					});

				})

			</script>
			<form name="fpd_shortcode_form">
				<?php if( !empty($price_format) ) : ?>
				<p class="fpd-shortcode-price-wrapper">
					<span class="fpd-shortcode-price" data-priceformat="<?php echo $price_format; ?>"></span>
				</p>
				<?php endif; ?>
				<input type="text" name="fpd_shortcode_form_name" placeholder="<?php echo $name_placeholder ?>" class="fpd-shortcode-form-text-input" />
				<input type="email" name="fpd_shortcode_form_email" placeholder="<?php echo $email_placeholder ?>" class="fpd-shortcode-form-text-input" />
				<input type="hidden" name="fpd_product" />
				<input type="submit" value="<?php echo $submit_text; ?>" class="fpd-disabled <?php echo fpd_get_option('fpd_start_customizing_css_class'); ?>" />
			</form>
			<?php

			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		//adds a customize button to the summary
		public static function add_customize_button( ) {

			global $post;
			$product_settings = new FPD_Product_Settings($post->ID);

			$fancy_content_ids = fpd_has_content( $post->ID );
			if( !is_array($fancy_content_ids) || sizeof($fancy_content_ids) === 0 ) { return; }

			if( $product_settings->customize_button_enabled ) {

				$button_class = trim(fpd_get_option('fpd_start_customizing_css_class')) == '' ? 'fpd-start-customizing-button' : fpd_get_option('fpd_start_customizing_css_class');
				$button_class .= fpd_get_option('fpd_start_customizing_button_position') === 'under-short-desc' ? ' fpd-block' : ' fpd-inline';
				$label = FPD_Settings_Labels::get_translation('misc', 'customization_button');

				$inline_js = '';
				if( $product_settings->get_option('product_designer_visibility') == 'lightbox' )
					$inline_js = 'onclick="return false"';

				?>
				<a href="<?php echo esc_url( add_query_arg( 'start_customizing', '' ) ); ?>" id="fpd-start-customizing-button" class="<?php echo $button_class; ?>" <?php echo $inline_js; ?>><?php echo $label; ?></a>
				<?php

			}

		}

		public function output_shortcode_js( $post ) {

			if( get_post_type( $post ) === 'product' )
				return;

			$product_settings = new FPD_Product_Settings($post->ID);
			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					var $shortcodePrice = $cartForm.find('.fpd-shortcode-price');

					//calculate initial price
					$selector.on('productCreate', function() {

						$cartForm.find(':submit').removeClass('fpd-disabled');
						_setTotalPrice();


					});

					//listen when price changes
					$selector.on('priceChange', _setTotalPrice);

					jQuery('[name="fpd_shortcode_form"]').on('click', ':submit', function(evt) {

						evt.preventDefault();

						if(!fpdProductCreated) { return false; }

						var order = fancyProductDesigner.getOrder({
								customizationRequired: <?php echo $product_settings->get_option('customization_required') == 'none' ? 0 : 1; ?>
							});

						var $submitBtn = jQuery(this),
							data = {
								action: 'fpd_newshortcodeorder'
							};

						if(order.product != false) {

							var $nameInput = $cartForm.find('[name="fpd_shortcode_form_name"]').removeClass('fpd-error'),
								$emailInput = $cartForm.find('[name="fpd_shortcode_form_email"]').removeClass('fpd-error'),
								emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;


							if( $nameInput.val() === '' ) {
								$nameInput.focus().addClass('fpd-error');
								return false;
							}
							else {
								data.name = $nameInput.val();
							}

							if( !emailRegex.test($emailInput.val()) ) {
								$emailInput.focus().addClass('fpd-error');
								return false;
							}
							else {
								data.email = $emailInput.val();
							}

							//PLUS
							if(fancyProductDesigner.bulkVariations) {

								if(fancyProductDesigner.bulkVariations.getOrderVariations() === false) {
									FPDUtil.showModal("<?php echo FPD_Settings_Labels::get_translation( 'plus', 'bulk_add_variations_term' ); ?>");
									return false;
								}

							}

							if(<?php echo empty(fpd_get_option('fpd_ae_admin_api_key')) && !class_exists('Fancy_Product_Designer_Export') ? 'false' : 'true'; ?>) {
								data.print_order = JSON.stringify(fancyProductDesigner.getPrintOrderData());
							}

							data.order = JSON.stringify(order);
							$submitBtn.addClass('fpd-disabled');
							$selector.find('.fpd-full-loader').show();

							jQuery.post(adminAjaxURL, data, function(response) {

								FPDUtil.showMessage(response.id ? response.message : response.error);
								$submitBtn.removeClass('fpd-disabled');
								$selector.find('.fpd-full-loader').hide();

							}, 'json');

							$nameInput.val('');
							$emailInput.val('');

						}

					});

					//set total price depending from wc and fpd price
					function _setTotalPrice() {

						if($shortcodePrice.data('priceformat')) {

							var htmlPrice = $shortcodePrice.data('priceformat').replace('%d', parseFloat(fancyProductDesigner.calculatePrice()).toFixed(2));

							$shortcodePrice.html(htmlPrice)
							.parent().addClass('fpd-show-up');

							if($modalPrice) {
								$modalPrice.html(htmlPrice);
							}

						}

					};

				});

			</script>
			<?php

		}

		public function create_shortcode_order() {

			if( !isset($_POST['order']) )
				die;

			$insert_id = FPD_Shortcode_Order::create( $_POST['name'], $_POST['email'], $_POST['order'], isset($_POST['print_order']) ? $_POST['print_order'] : null );

			if( $insert_id ) {
				echo json_encode(array(
					'id' => $insert_id,
					'message' => FPD_Settings_Labels::get_translation( 'misc', 'shortcode_order:success_sent' ),
				));
			}
			else {

				echo json_encode(array(
					'error' => FPD_Settings_Labels::get_translation( 'misc', 'shortcode_order:fail_sent' ),
				));

			}

			die;

		}

		public function shortcode_action_handler( $atts ) {

			extract( shortcode_atts( array(
				'type' => null,
				'layout' => 'icon-tooltip' //icon-tooltip, icon-text, text
			), $atts, 'fpd_action' ) );

			ob_start();
			?>
			<span class="fpd-sc-action-placeholder" data-action="<?php echo esc_attr( $type ); ?>" data-layout="<?php echo esc_attr( $layout ); ?>"></span>
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		public function shortcode_module_handler( $atts ) {

			extract( shortcode_atts( array(
				'type' => null,
				'css' => ''
			), $atts, 'fpd_module' ) );

			ob_start();
			?>
			<div class="fpd-sc-module-wrapper fpd-container" data-type="<?php echo esc_attr( $type ); ?>" style="<?php echo esc_attr( $css ); ?>"></div>
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		public function create_print_ready_file() {

			if( isset($_POST['print_data']) && class_exists('Fancy_Product_Designer_Export') ) {

				$print_data = json_decode(stripslashes($_POST['print_data']), true);
				$file = Fancy_Product_Designer_Export::create_print_ready_file( $print_data );
				$file_url = content_url( '/fancy_products_orders/print_ready_files/' . $file );

				echo json_encode(array(
					'file_url' => $file_url
				));

			}

			die;

		}

		public function insta_get_token() {

			$client_app_id = isset($_GET['client_app_id']) ? $_GET['client_app_id'] : null;
			$redirect_uri = isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : null;
			$code = isset($_GET['code']) ? $_GET['code'] : null;
			$client_secret = fpd_get_option('fpd_instagram_secret_id');

			if( $client_app_id && $redirect_uri && $code && !empty($client_secret) ) {

				$url = 'https://api.instagram.com/oauth/access_token';

				$curlPost = 'client_id='. $client_app_id . '&redirect_uri=' . $redirect_uri . '&app_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
				$data = json_decode(curl_exec($ch), true);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				echo json_encode( $data );

			}
			else {

				echo json_encode(array(
					'error_message' => 'Client App ID, Redirect URI or Code is not set!'
				));

			}

			die;
		}

		public function custom_uplod_file() {

			require_once(FPD_PLUGIN_DIR . '/inc/custom-image-handler.php');
			die;

		}

	}
}

new FPD_Frontend_Product();

?>