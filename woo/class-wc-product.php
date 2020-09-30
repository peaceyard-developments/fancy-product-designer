<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_WC_Product') ) {

	class FPD_WC_Product {

		public function __construct() {

			//product listing
			add_filter( 'woocommerce_get_price_html', array( &$this, 'product_listing_price'), 10, 2 );

			//wp_head
			add_action( 'fpd_post_fpd_enabled', array( &$this, 'head_frontend'), 10, 2 );

			add_filter( 'post_class', array( &$this, 'product_css_class') );

			add_filter( 'woocommerce_product_single_add_to_cart_text', array( &$this, 'add_to_cart_text'), 20, 2 );
			//before product container
			add_action( 'woocommerce_before_single_product', array( &$this, 'before_product_container'), 1 );

			add_action( 'fpd_before_product_designer', array( &$this, 'before_product_designer'), 1 );
			add_action( 'fpd_before_js_fpd_init', array( &$this, 'before_js_product_designer'), 1 );
			add_action( 'fpd_after_product_designer', array( &$this, 'after_product_designer'), 1 );

			//add customize button
			$customize_btn_pos = fpd_get_option('fpd_start_customizing_button_position');
			if( $customize_btn_pos == 'under-short-desc' ) {
				add_action( 'woocommerce_single_product_summary', 'FPD_Frontend_Product::add_customize_button', 25 );
			}
			else if( $customize_btn_pos == 'before-add-to-cart-button') {
				add_action( 'woocommerce_before_add_to_cart_button', 'FPD_Frontend_Product::add_customize_button', 0 );
			}
			else {
				add_action( 'woocommerce_after_add_to_cart_button', 'FPD_Frontend_Product::add_customize_button', 0 );
			}

			//add additional form fields to cart form
			add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'add_product_designer_form') );

			//change product by variation
			add_filter( 'woocommerce_available_variation', array( &$this, 'set_variation_meta'), 20, 3 );
			add_action( 'woocommerce_after_variations_form', array( &$this, 'add_variation_handler') );

			//enable share for wc
			if( fpd_get_option('fpd_sharing') ) {
				add_action( 'woocommerce_share' , array( &$this, 'add_share' ) );
			}

			if(fpd_get_option('fpd_accountProductStorage')) {

				//modify account menu
				add_filter( 'woocommerce_account_menu_items', array( &$this, 'account__menu_items' ), 10, 1 );
				add_action( 'woocommerce_account_saved_products_endpoint', array( &$this, 'display_saved_product_in_account' ) );

			}

		}

		public function product_listing_price( $price, $product ) {


			if( is_shop() && is_fancy_product( $product->get_id() ) ) {

				$product_settings = new FPD_Product_Settings( $product->get_id() );

				if( $product_settings->get_option('get_quote') )
					$price = '';

			}

			return $price;

		}

		public function head_frontend( $post, $product_settings ) {

			$product_settings = new FPD_Product_Settings( $post->ID );
			$main_bar_pos = $product_settings->get_option('main_bar_position');

			if( $main_bar_pos === 'after_product_title' ) {
				add_action( 'woocommerce_single_product_summary', array( &$this, 'add_main_bar_container'), 7 );
			}
			else if( $main_bar_pos === 'after_excerpt' ) {
				add_action( 'woocommerce_single_product_summary', array( &$this, 'add_main_bar_container'), 25 );
			}

		}

		public function product_css_class( $classes ) {

			global $post;

			if( $post && isset($post->ID) ) {

				$product_settings = new FPD_Product_Settings( $post->ID );
				$cb_var_needed = $product_settings->get_option('wc_customize_variation_needed');

				if( $cb_var_needed ) {
					$classes[] = 'fpd-variation-needed';
				}

			}

			return $classes;

		}

		//add a main bar container
		public function add_main_bar_container() {

			echo '<div class="fpd-main-bar-position"></div>';

		}

		//custom text for the add-to-cart button in single page
		public function add_to_cart_text( $text, $product ) {

			if( is_fancy_product( $product->get_id() ) ) {

				$product_settings = new FPD_Product_Settings( $product->get_id() );

				if( is_product() ) { //only change text if on single product page and get quote is enabled
					if( $product_settings->get_option('get_quote') )
						return FPD_Settings_Labels::get_translation( 'woocommerce', 'get_a_quote' );
				}

			}

			return $text;

		}

		public function before_product_container() {

			global $post;

			if( is_fancy_product( $post->ID ) ) {

				//add product designer
				$product_settings = new FPD_Product_Settings( $post->ID );
				$position = $product_settings->get_option('placement');

				if( $position  == 'fpd-replace-image') {
					add_action( 'woocommerce_before_single_product_summary', 'FPD_Frontend_Product::add_product_designer', 15 );
				}
				else if( $position  == 'fpd-under-title') {
					add_action( 'woocommerce_single_product_summary', 'FPD_Frontend_Product::add_product_designer', 6 );
				}
				else if( $position  == 'fpd-after-summary') {
					add_action( 'woocommerce_after_single_product_summary', 'FPD_Frontend_Product::add_product_designer', 1 );
				}
				else {
					add_action( 'fpd_product_designer', 'FPD_Frontend_Product::add_product_designer' );
				}

				//remove product image, there you gonna see the product designer
				if( $product_settings->get_option('hide_product_image') || ($position == 'fpd-replace-image' && (!$product_settings->customize_button_enabled)) ) {
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				}

			}
		}

		public function before_product_designer( $post ) {

			if( get_post_type( $post ) !== 'product' )
				return;

			global $product, $woocommerce;

			//added to cart, recall added product
			if( isset($_POST['fpd_product']) ) {

				$views = $_POST['fpd_product'];
				FPD_Frontend_Product::$form_views = fpd_get_option('fpd_wc_add_to_cart_product_load') == 'customized-product' ? stripslashes($views) : null;

			}
			else if( isset($_GET['cart_item_key']) ) {

				//load from cart item
				$cart = $woocommerce->cart->get_cart();

				$cart_item = $woocommerce->cart->get_cart_item( $_GET['cart_item_key'] );
				if( !empty($cart_item) ) {

					if( isset($cart_item['fpd_data']) ) {

						if( isset( $cart_item['quantity'] ) )
							$_POST['quantity'] = $cart_item['quantity'];

						$views = $cart_item['fpd_data']['fpd_product'];
						FPD_Frontend_Product::$form_views = stripslashes($views);
					}

				}
				else {

					//cart item could not be found
					echo '<p><strong>';
					_e('Sorry, but the cart item could not be found!', 'radykal');
					echo '</strong></p>';
					return;

				}

			}
			else if( isset($_GET['order']) && isset($_GET['item_id']) ) {

				$order = wc_get_order( $_GET['order'] );

				//check if order belongs to customer
				if(!fpd_get_option('fpd_order_login_required')
					|| current_user_can(Fancy_Product_Designer::CAPABILITY)
					|| $order->get_user_id() === get_current_user_id()
				) {

					$item_meta = fpd_wc_get_order_item_meta( $_GET['item_id'], $_GET['order'] );

					//V3.4.9: only order is stored in fpd_data
					FPD_Frontend_Product::$form_views = is_array($item_meta) ? $item_meta['fpd_product'] : $item_meta;

					if( $order && $order->is_paid() ) {
						FPD_Frontend_Product::$remove_watermark = true;

						if( $product->is_downloadable() ) :
						?>
						<a href="#" id="fpd-extern-download-pdf" class="<?php echo trim(fpd_get_option('fpd_start_customizing_css_class')); ?>" style="display: inline-block; margin: 10px 10px 10px 0;">
							<?php echo FPD_Settings_Labels::get_translation( 'actions', 'download' ); ?>
						</a>
						<?php
						endif;

					}
					else {
						FPD_Frontend_Product::$remove_watermark = false;
					}

					$allowed_edit_status = array(
						'pending',
						'processing',
						'on-hold'
					);

					if( fpd_get_option('fpd_order_save_order') && in_array($order->get_status(), $allowed_edit_status) ) : ?>
						<a href="#" id="fpd-save-order" class="<?php echo trim(fpd_get_option('fpd_start_customizing_css_class')); ?>"  style="display: inline-block; margin: 10px 10px 10px 0;">
							<?php echo FPD_Settings_Labels::get_translation( 'woocommerce', 'save_order' ); ?>
						</a>
					<?php endif;

				}

			}
			else if( isset($_GET['start_customizing']) && isset($_GET['fpd_product']) ) {

				$get_fpd_product_id = intval($_GET['fpd_product']);

				if( FPD_Product::exists($get_fpd_product_id) ) {
					$fancy_product = new FPD_Product( $get_fpd_product_id );
					FPD_Frontend_Product::$form_views = $fancy_product->to_JSON();
				}

			}

		}

		public function before_js_product_designer() {

			global $product;

			if($product) {

				$download_permitted = 0;
				if( isset($_GET['order']) && $product->is_downloadable() ) {
					$order = wc_get_order( $_GET['order'] );
					$download_permitted = $order && $order->is_download_permitted();
				}

				?>
				var currencyPos = "<?php echo get_option('woocommerce_currency_pos'); ?>",
					currencySymbol = '<span class="woocommerce-Price-currencySymbol"><?php echo get_woocommerce_currency_symbol(); ?></span>',
					currencyFormat;
				if(currencyPos == 'right') {
					currencyFormat = '%d' + currencySymbol;
				}
				else if(currencyPos == 'right_space') {
					currencyFormat = '%d' + ' ' + currencySymbol;
				}
				else if(currencyPos == 'left_space') {
					currencyFormat = currencySymbol + ' ' + '%d';
				}
				else {
					currencyFormat = currencySymbol + '%d';
				}

				pluginOptions.priceFormat.currency = currencyFormat;
				pluginOptions.priceFormat.decimalSep = "<?php echo get_option('woocommerce_price_decimal_sep'); ?>";
				pluginOptions.priceFormat.thousandSep = "<?php echo get_option('woocommerce_price_thousand_sep'); ?>";

				//add download action for download-permitted orders
				if(<?php echo intval($download_permitted); ?>) {
					if(jQuery.isArray(pluginOptions.actions.top)) {
						pluginOptions.actions.top.push('download');
					}
					else {
						pluginOptions.actions.top = ['download'];
					}
				}

				<?php

			}

		}

		public function after_product_designer( $post ) {

			if( get_post_type( $post ) !== 'product' )
				return;

			global $product;
			$product_settings = new FPD_Product_Settings( $product->get_id() );

			$product_price = wc_get_price_to_display( $product );
			$product_price = $product_price && is_numeric($product_price) ? $product_price : 0;

			?>
			<script type="text/javascript">

				//WOOCOMMERCE JS

				var numberOfDecimals = <?php echo !apply_filters( 'woocommerce_price_trim_zeros', false ) ? get_option('woocommerce_price_num_decimals') : 0; ?>,
					variationSet = false;

				jQuery(document).ready(function() {

					var wcPrice = <?php echo $product_price; ?>;

					//check when variation has been selected
					jQuery(document)
					.on('found_variation', '.variations_form', function(evt, variation) {

						var variationPrice;
						if(variation.display_price !== undefined) {
							wcPrice = variation.display_price;
						}

						_setTotalPrice();

						variationSet = true;

					})
					.on('reset_data', '.variations_form', function(evt, variation) {
						variationSet = false;
					});

					//calculate initial price
					$selector.on('productCreate', function() {

						_setTotalPrice();

						if(<?php echo FPD_Frontend_Product::$form_views === null ? 0 : 1; ?>) {
							setTimeout(_setProductImage, 5);
						}

					});

					//listen when price changes
					$selector.on('priceChange', function() {
						_setTotalPrice();
					});

					$cartForm.on('fpdProductSubmit', function() {

						fancyProductDesigner.toggleSpinner(true);
						$cartForm.submit();



					})

					//fill custom form with values and then submit
					$cartForm.on('click', ':submit', function(evt) {

						evt.preventDefault();

						//validate min quantity input
						$quantityInput = $cartForm.find('.quantity input');
						if($quantityInput.length > 0 && parseInt($quantityInput.val()) < parseInt($quantityInput.attr('min'))) {
							return;
						}

						//check if product is created and all variations are selected
						if(!fpdProductCreated || $( this ).is('.wc-variation-selection-needed')) { return false; }

						var order = fancyProductDesigner.getOrder({
								customizationRequired: <?php echo $product_settings->get_option('customization_required') == 'none' ? 0 : 1; ?>
							});

						//PLUS
						var bulkVariations = null;
						if(fancyProductDesigner.bulkVariations) {

							bulkVariations = fancyProductDesigner.bulkVariations.getOrderVariations();
							if(bulkVariations === false) {
								FPDUtil.showModal("<?php echo FPD_Settings_Labels::get_translation( 'plus', 'bulk_add_variations_term' ); ?>");
								order.product = false;
							}

						}

						if(order.product != false) {

							FPDUtil.showMessage('<?php echo FPD_Settings_Labels::get_translation( 'woocommerce', 'add_to_cart' ) ?>');

							var priceSet = _setTotalPrice();
							jQuery('.single_add_to_cart_button').addClass('fpd-disabled');

							var tempDevicePixelRation = fabric.devicePixelRatio,
								viewOpts = fancyProductDesigner.viewInstances[0].options,
								multiplier = FPDUtil.getScalingByDimesions(viewOpts.stageWidth, viewOpts.stageHeight, <?php echo fpd_get_option('fpd_wc_cart_thumbnail_width'); ?>, <?php echo fpd_get_option('fpd_wc_cart_thumbnail_height'); ?>);

							fabric.devicePixelRatio = 1;
							fancyProductDesigner.viewInstances[0].toDataURL(function(dataURL) {

								$cartForm.find('input[name="fpd_product"]').val(JSON.stringify(order));
								$cartForm.find('input[name="fpd_product_thumbnail"]').val(dataURL);

								if(bulkVariations) {
									$cartForm.find('input[name="fpd_bulk_variations_order"]')
									.val(JSON.stringify(bulkVariations));
								}

								if(<?php echo empty(fpd_get_option('fpd_ae_admin_api_key')) && !class_exists('Fancy_Product_Designer_Export') ? 'false' : 'true'; ?>) {
									$cartForm.find('input[name="fpd_print_order"]').val(JSON.stringify(fancyProductDesigner.getPrintOrderData()));
								}

								if(priceSet) {
									$cartForm.trigger('fpdProductSubmit');
								}

								fabric.devicePixelRatio = tempDevicePixelRation;

							}, 'transparent', {format: 'png', multiplier: multiplier})

						}

					});

					jQuery('.fpd-modal-product-designer').on('click', '.fpd-done', function(evt) {

						evt.preventDefault();

						if($selector.parents('.woocommerce').length > 0) {
							_setProductImage();
						}

						if(<?php echo intval(fpd_get_option('fpd_lightbox_add_to_cart')); ?>) {
							$cartForm.find(':submit').click();
						}

					});

					jQuery('#fpd-extern-download-pdf').click(function(evt) {

						var $this = jQuery(this);

						evt.preventDefault();
						if(fpdProductCreated) {

							if(<?php echo class_exists('Fancy_Product_Designer_Export') && isset($_GET['order']) ? 'true' : 'false' ?>) {

								$this.addClass('fpd-disabled');
								fancyProductDesigner.toggleSpinner(true);

								var printData = fancyProductDesigner.getPrintOrderData();
								printData.name = "<?php echo isset($_GET['order']) ? $_GET['order'].'_'.$_GET['item_id'] : ''; ?>";

								var data = {
									action: 'fpd_pr_export',
									print_data: JSON.stringify(printData)
								};

								jQuery.post(adminAjaxURL, data, function(response) {

									if(response && response.file_url) {
										window.open(response.file_url, '_blank')
									}
									else {
										alert('Something went wrong. Please contact the site owner!');
									}

									$this.removeClass('fpd-disabled');
									fancyProductDesigner.toggleSpinner(false);

								}, 'json');

							}
							else {
								fancyProductDesigner.actions.downloadFile('pdf');
							}

						}
						else {
							FPDUtil.showModal("<?php _e('The product is not created yet, try again when the product has been fully loaded into the designer', 'fpd_label'); ?>");
						}

					});

					jQuery('#fpd-save-order').click(function(evt) {

						evt.preventDefault();

						if(fpdProductCreated) {

							fancyProductDesigner.toggleSpinner(true, '<?php echo FPD_Settings_Labels::get_translation( 'woocommerce', 'saving_order' ) ?>');

							var data = {
								action: 'fpd_save_order',
								item_id: <?php echo isset($_GET['item_id']) ? $_GET['item_id'] : -1; ?>,
								fpd_order: JSON.stringify(fancyProductDesigner.getOrder()),
								print_order: <?php echo empty(fpd_get_option('fpd_ae_admin_api_key')) ? 'false' : 'true'; ?> ? JSON.stringify(fancyProductDesigner.getPrintOrderData()) : ''
							};

							jQuery.post(
								'<?php echo admin_url('admin-ajax.php'); ?>',
								data,
								function(response) {

									fancyProductDesigner.toggleSpinner(false);

									if(typeof response === 'object') {
										FPDUtil.showMessage('<?php echo FPD_Settings_Labels::get_translation( 'woocommerce', 'order_saved' ) ?>');
									}
									else {
										FPDUtil.showMessage('<?php echo FPD_Settings_Labels::get_translation( 'woocommerce', 'order_saving_failed' ) ?>');
									}


							}, 'json');

						}
						else {
							FPDUtil.showModal("<?php _e('The product is not created yet, try again when the product has been fully loaded into the designer', 'fpd_label'); ?>");
						}

					});

					//set total price depending from wc and fpd price
					function _setTotalPrice() {

						//do not set price when wcbv is enabled, wcbv is doing price display
						if($body.hasClass('wcbv-product')) {
							return false;
						}

						$cartForm.find('input[name="fpd_quantity"]').val(fancyProductDesigner.orderQuantity);

						if(<?php echo fpd_get_option('fpd_wc_disable_price_calculation') ?>) {
							return true;
						}

						//PLUS: order quantity
						var totalPrice = (parseFloat(wcPrice) *  fancyProductDesigner.orderQuantity) + parseFloat(fancyProductDesigner.calculatePrice()),
							htmlPrice;

						totalPrice = totalPrice.toFixed(numberOfDecimals);

						if(!$priceElem || $priceElem.length == 0) {

							htmlPrice = fancyProductDesigner.formatPrice(totalPrice);

							//check if variations are used
							var $priceElem,
								selectorPriceAmount = "<?php echo apply_filters( 'fpd_price_selector', '.price:first .woocommerce-Price-amount:last' ); ?>";
							if($productWrapper.find('.variations_form').length > 0) {
								//check if amount contains 2 prices or sale prices. If yes different prices are used
								if($productWrapper.find('.price:first > .amount').length >= 2 || $productWrapper.find('.price:first ins > .amount').length >= 2) {
									//different prices
									$priceElem = $cartForm.find('.woocommerce-Price-amount:first').length > 0 ?
										$cartForm.find(selectorPriceAmount)
									:
										$productWrapper.find('.single_variation .price .amount:last'); //fallback older WC version

								}
								else {
									//same price
									$priceElem = $productWrapper.find('.woocommerce-Price-amount:first').length > 0 ?
										$productWrapper.find(selectorPriceAmount)
									:
										$productWrapper.find('.price:first .amount:last'); //fallback older WC version
								}

							}
							//no variations are used
							else {
								$priceElem = $productWrapper.find('.woocommerce-Price-amount').length > 0 ?
										$productWrapper.find(selectorPriceAmount)
									:
										$productWrapper.find('.price:first .amount:last'); //fallback older WC version
							}

						}

						if($priceElem && $priceElem.length > 0) {
							$priceElem.html(htmlPrice);
						}
						else {
							FPDUtil.log('No price element could be found in the document!', 'info');
						}

						if($modalPrice) {
							$modalPrice.html(htmlPrice);
						}


						if($cartForm.find('input[name="fpd_product_price"]').length > 0) {
							//set price without quantity
							$cartForm.find('input[name="fpd_product_price"]').val(parseFloat(wcPrice) + fancyProductDesigner.calculatePrice(false));
							return true;
						}
						else {
							return false;
						}


					};

					var fpdImage;
					function _updateProductImage(imageSrc) {

						var $firstProductImage = $productWrapper.find('.images'),
							//wc standard, flatsome theme, owl
							firstImageSelector = '.woocommerce-product-gallery__image:first img, .slide:first img, .owl-stage .img-thumbnail img';

						var image = new Image();
						image.onload = function() {
							$firstProductImage.find(firstImageSelector)
							.attr('data-large_image_width', this.width)
							.attr('data-large_image_height', this.height);
						};
						image.src = imageSrc;

						$firstProductImage
						.find(firstImageSelector)
						.attr('src', imageSrc).attr('srcset', imageSrc) //all images (display and zoom)
						.parent('a').attr('href', imageSrc)  //photoswipe image
						.children('img').attr('data-large_image', imageSrc); //photoswipe large image


						$firstProductImage
						.find('.flex-control-thumbs li:first img').attr('src', imageSrc); //thumb gallery

					}

					function _setProductImage() {

						if(jQuery('.fpd-modal-product-designer').length > 0 && <?php echo fpd_get_option('fpd_lightbox_update_product_image'); ?>) {


							fancyProductDesigner.viewInstances[0].toDataURL(function(dataURL) {

								_updateProductImage(dataURL);
								fpdImage = dataURL;

							}, 'transparent', {format: 'png'});

						}

					};

					//fix: do not change to variation image when using lightbox
					$productWrapper.find('.images').on('woocommerce_gallery_init_zoom', function() {

						if(fpdImage) {
							_updateProductImage(fpdImage);
						}

						//timeout fix: zoom image is not updating
						setTimeout(function() {
							if(fpdImage) {
								_updateProductImage(fpdImage);
							}
						}, 500);


					});

				}); //document.ready

			</script>
			<?php

		}

		public function add_variation_handler() {

			global $product;
			$product_settings = new FPD_Product_Settings( $product->get_id() );

			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					var $productWrapper = jQuery('.post-<?php echo $product->get_id(); ?>'),
						$customizeButton = jQuery('#fpd-start-customizing-button');

					//set url parameters from form if designer is opened on next page
					$customizeButton.click(function(evt) {

						if(!jQuery(this).hasClass('fpd-modal-mode-btn')) {

							evt.preventDefault();

							var serializedForm = jQuery('form.variations_form select').serialize();
							serializedForm = serializedForm.replace(/[^=&]+=(&|$)/g,"").replace(/&$/,""); //remove empty values

							window.open(this.href+'&'+serializedForm, '_self');

						}

					});

					jQuery('[name="variation_id"]:first').parents('form:first')
					.on('show_variation', function(evt, variation) {

						$customizeButton.css('display', 'inline-block');

						if(variation.fpd_variation_product_id) {

							var fpdProductID = variation.fpd_variation_product_id;
							if(typeof fpdProductCreated !== 'undefined' && fpdProductCreated) {

								fancyProductDesigner.toggleSpinner(true, '<?php echo FPD_Settings_Labels::get_translation( 'woocommerce', 'loading_product' ) ?>');

								var data = {
									action: 'fpd_load_product',
									product_id: fpdProductID
								};

								jQuery.post(
									'<?php echo admin_url('admin-ajax.php'); ?>',
									data,
									function(response) {

										if(typeof response === 'object') {

											if(response.length == 0) {

												alert('The product does not exists or has no views!');
												fancyProductDesigner.toggleSpinner(false);
												return;

											}

											fancyProductDesigner.loadProduct(
												response,
												<?php echo $product_settings->get_option('replace_initial_elements'); ?>,
												true
											);
										}
										else {
											FPDUtil.showMessage('<?php echo FPD_Settings_Labels::get_translation( 'woocommerce', 'product_loading_fail' ) ?>');
										}


								}, 'json');

							}
							else { //customize button activated and product designer will load in own page

								$customizeButton.attr('href', function(_, href){
								    return href.search('fpd_product') === -1 ? href+'&fpd_product='+fpdProductID : href.replace(/fpd_product=\d+/gi, 'fpd_product='+fpdProductID);
								});

							}

						}

					})
					.on('reset_data', function() {

						if($productWrapper.hasClass('fpd-variation-needed')) {
							$customizeButton.hide();
						}

					});

				});
			</script>
			<?php

		}

		//the additional form fields
		public function add_product_designer_form() {

			global $post;
			$product_settings = new FPD_Product_Settings($post->ID);

			if( $product_settings->show_designer() ) {
				?>
				<input type="hidden" value="<?php echo esc_attr( $post->ID ); ?>" name="add-to-cart" />
				<input type="hidden" value="" name="fpd_product" />
				<input type="hidden" value="<?php echo isset($_GET['cart_item_key']) ? $_GET['cart_item_key'] : ''; ?>" name="fpd_remove_cart_item" />
				<input type="hidden" value="" name="fpd_print_order" />
				<?php

				if( !fpd_get_option('fpd_wc_disable_price_calculation') )
					echo '<input type="hidden" value="" name="fpd_product_price" />';

				if( fpd_get_option('fpd_cart_custom_product_thumbnail') || fpd_get_option('fpd_order_product_thumbnail') )
					echo '<input type="hidden" value="" name="fpd_product_thumbnail" />';

				do_action('fpd_product_designer_form_end', $product_settings);
			}

		}

		//add variation product id to variation attributes
		public function set_variation_meta( $attrs, $instance, $variation ) {

			$variationProduct = get_post_meta( $variation->get_id(), 'fpd_variation_product', true );
			if( $variationProduct && !empty($variationProduct) )
				$attrs['fpd_variation_product_id'] = intval($variationProduct);

			return $attrs;

		}

		public function add_share() {

			global $post;

			$product_settings = new FPD_Product_Settings( $post->ID );
			if( $product_settings->show_designer() )
				echo FPD_Share::get_share_html();

		}

		public function account__menu_items( $items ) {

			$index_logout = array_search("customer-logout",array_keys($items));
			$menu_item = array('saved_products' =>  FPD_Settings_Labels::get_translation( 'misc', 'account_storage:saved_products' ) );

			$items = array_slice($items, 0, $index_logout, true) + $menu_item +  array_slice($items, $index_logout, count($items) - 1, true) ;

			return $items;

		}

		public function display_saved_product_in_account() {

			echo do_shortcode( '[fpd_saved_products]' );

		}

	}

}

new FPD_WC_Product();

?>