<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_User_Products')) {

	class FPD_User_Products {

		public function __construct() {

			//store user's product in his account
			if(fpd_get_option('fpd_accountProductStorage')) {

				add_action( 'wp_ajax_fpd_saveuserproduct', array( &$this, 'save_user_product' ) );
				add_action( 'wp_ajax_fpd_loaduserproducts', array( &$this, 'load_user_products' ) );
				add_action( 'wp_ajax_fpd_removeuserproducts', array( &$this, 'remove_user_products' ) );

				add_action( 'fpd_after_product_designer', array( &$this, 'add_frontend_js' ) );

			}

			//display saved products
			add_shortcode( 'fpd_saved_products', array( &$this, 'shortcode_display_saved_products') );

		}

		public function shortcode_display_saved_products() {

			global $wpdb;

			$saved_products = self::get_user_products();

			if( empty($saved_products) )
				return '';

			ob_start(); ?>

			<div class="fpd-saved-products-grid"> <?php

			foreach($saved_products as $key => $saved_product) {

				$post_id = intval($saved_product['post_id']);
				$fpd_product = $saved_product['fpd_data'];

				if( get_post_status($post_id) !== false ) {

					$post_url = get_the_permalink( $post_id );

					?>
					<div style="background-image: url(<?php echo $fpd_product['thumbnail']; ?>)" data-key="<?php esc_attr_e( $key ); ?>">
						<a href="<?php echo esc_url( add_query_arg( 'fpd_saved_product', $key, $post_url ) ); ?>" ></a>
						<?php if( isset($fpd_product['title']) && !empty($fpd_product['title']) ): ?>
						<span><?php echo esc_html( $fpd_product['title'] ); ?></span>
						<?php endif; ?>
						<span class="fpd-sc-remove-saved-product">&times;</span>
					</div>
					<?php

				}

			}

			?>
			</div>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					jQuery('.fpd-saved-products-grid').on('click', '.fpd-sc-remove-saved-product', function(evt) {

						evt.stopPropagation();

						var $this = jQuery(this);

						var data = {
							action: 'fpd_removeuserproducts',
							key: $this.parent().data('key'),
						};

						jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response) {
						}, 'json');

						$this.parent().remove();

					});

				});

			</script>
			<?php

			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		public function save_user_product() {

			$current_user_id = get_current_user_id();

			if( $current_user_id !== 0 ) {

				$post_id = intval($_POST['post_id']);
				$product = fpd_strip_multi_slahes($_POST['product']);
				$product = json_decode($product, true);

				$fpd_data = array(
					'title' => $_POST['title'],
					'product' => $product,
					'thumbnail' => $_POST['thumbnail'],
				);

				$result = update_user_meta( $current_user_id, 'fpd_saved_product_'.uniqid($post_id.'_'), array(
					'post_id' => $post_id,
					'fpd_data' => $fpd_data
				) );

				if( $result )
					echo json_encode(array(
						'id' => $result,
						'message' => FPD_Settings_Labels::get_translation( 'misc', 'product_saved' ),
					));

			}

			die;

		}

		public function load_user_products() {

			$saved_products = self::get_user_products();

			$response_data = array();
			if( !empty($saved_products) )
				$response_data = $saved_products;

			echo json_encode(array(
				'data' => $response_data,
				'message' => FPD_Settings_Labels::get_translation( 'misc', 'account_storage:products_loaded' )
			));

			die;

		}

		public function remove_user_products() {

			$current_user_id = get_current_user_id();

			if( $current_user_id !== 0 ) {

				delete_user_meta($current_user_id, 'fpd_saved_product_'.$_POST['key']);

				echo json_encode(array(
					'data' => 1,
				));

			}

			die;

		}

		public function add_frontend_js( $post ) {

			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					var loginRequiredText = "<?php echo FPD_Settings_Labels::get_translation( 'misc', 'account_storage:login_required' ); ?>";

					$selector.on('actionSave', function(evt, title, thumbnail, product) {

						if(<?php echo get_current_user_id( ); ?> === 0) {
							FPDUtil.showMessage(loginRequiredText);
							return;
						}

						if(product) {

							fancyProductDesigner.toggleSpinner(true);

							var data = {
								action: 'fpd_saveuserproduct',
								title: title,
								thumbnail: thumbnail,
								product: JSON.stringify(product),
								post_id : <?php echo $post->ID; ?>
							};

							jQuery.post(adminAjaxURL, data, function(response) {

								FPDUtil.showMessage(response.error ? response.error : response.message);
								fancyProductDesigner.toggleSpinner(false);

							}, 'json');

						}

					})
					.on('actionLoad', function() {

						if(<?php echo get_current_user_id( ); ?> === 0) {
							FPDUtil.showMessage(loginRequiredText);
							return;
						}

						fancyProductDesigner.toggleSpinner(true);

						var data = {
							action: 'fpd_loaduserproducts',
							post_id : <?php echo $post->ID; ?>
						};

						jQuery.post(adminAjaxURL, data, function(response) {

							if(response.data) {

								fancyProductDesigner.mainBar
								.$content.find('.fpd-saved-designs-panel .fpd-empty-saved-designs')
								.toggleClass('fpd-hidden', Object.keys(response.data).length !== 0);

								Object.keys(response.data).forEach(function(metaKey) {

									var fpdData = response.data[metaKey].fpd_data;

									$item = fancyProductDesigner.actions.addSavedProduct(
										fpdData.thumbnail,
										fpdData.product,
										fpdData.title
									);

									$item.data('key', metaKey);

								});

							}

							fancyProductDesigner.toggleSpinner(false);

							FPDUtil.showMessage(response.error ? response.error : response.message);

						}, 'json');

					})
					.on('actionLoad:Remove', function(evt, index, $item) {

						var data = {
							action: 'fpd_removeuserproducts',
							key: $item.data('key'),
						};

						jQuery.post(adminAjaxURL, data, function(response) {}, 'json');

					});


				})




			</script>
			<?php

		}

		public static function get_user_products() {

			global $wpdb;

			$parsed_products = array();
			$current_user_id = get_current_user_id();

			if( $current_user_id !== 0 ) {

				//update to V4.0.2
				$old_saved_products = get_user_meta( $current_user_id, 'fpd_saved_products', true );
				if( !empty($old_saved_products) ) {

					foreach($old_saved_products as $post_id => $fpd_products_in_post) {

						foreach($fpd_products_in_post as $key => $old_saved_product) {

							if( !empty($old_saved_product) ) {

								update_user_meta( $current_user_id, 'fpd_saved_product_'.uniqid($post_id.'_'), array(
									'post_id' => $post_id,
									'fpd_data' => $old_saved_product
								) );

							}

						}


					}

				}
				delete_user_meta($current_user_id, 'fpd_saved_products');

				$saved_products = $wpdb->get_results("SELECT meta_key, meta_value FROM ".$wpdb->usermeta." WHERE user_id=$current_user_id AND meta_key LIKE 'fpd_saved_product_%'");

				foreach($saved_products as $saved_product) {

					$meta_key = str_replace('fpd_saved_product_', '', $saved_product->meta_key);
					$meta_data = unserialize($saved_product->meta_value);

					$parsed_products[$meta_key] = $meta_data;

				}

				return $parsed_products;

			}
			else
				return array();

		}

	}
}

new FPD_User_Products();

?>