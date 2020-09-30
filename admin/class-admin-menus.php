<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Admin_Menus')) {

	class FPD_Admin_Menus {

		public function __construct() {

			add_action( 'admin_menu', array( &$this, 'add_menu_pages' ) );
			//add action links to plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( FPD_PLUGIN_DIR ).'/fancy-product-designer.php', array( &$this, 'action_links' ) );

			add_action( 'fpd_backend_react_page_start', array( &$this, 'react_backend_head' ) );

		}

		public function add_menu_pages() {

			//add top level menu
			add_menu_page(
				__('Fancy Product Designer', 'radykal'),
				__('Fancy Product Designer', 'radykal'),
				Fancy_Product_Designer::CAPABILITY,
				'fancy_product_designer',
				array( $this, 'output_react' ),
				'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSIzMHB4IiBoZWlnaHQ9IjMwcHgiIHZpZXdCb3g9IjAgMCAzMCAzMCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4gICAgICAgIDx0aXRsZT53aGl0ZTwvdGl0bGU+ICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPiAgICA8ZGVmcz48L2RlZnM+ICAgIDxnIGlkPSJQYWdlLTEiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPiAgICAgICAgPGcgaWQ9IkxvZ28xIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLjAwMDAwMCwgLTM2MS4wMDAwMDApIiBmaWxsPSIjRjFGMkYzIj4gICAgICAgICAgICA8ZyBpZD0id2hpdGUiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLCAzNjEuMDAwMDAwKSI+ICAgICAgICAgICAgICAgIDxnIGlkPSJpbm5lciIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNC45MDkwOTEsIDUuMDkwOTA5KSI+ICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMTYuOTA5MDkwOSwyLjkwODM2MzY0IEwxNS4wODk4MTgyLDQuNzI3NjM2MzYgTDE1LjA4NzYzNjQsNC43Mjk4MTgxOCBMMTQuNjgzNjM2NCw1LjEzMzgxODE4IEMxNS44OTU2MzY0LDYuMzQ1NjM2MzYgMTYuNjQ1MDkwOSw4LjAxOTgxODE4IDE2LjY0NTA5MDksOS44NjkyNzI3MyBDMTYuNjQ1MDkwOSwxMS43MTgzNjM2IDE1Ljg5NTYzNjQsMTMuMzkyNTQ1NSAxNC42ODM4MTgyLDE0LjYwNDE4MTggTDE1LjA4NzYzNjQsMTUuMDA4IEwxNS4wOSwxNS4wMSBMMTYuOTA5MjcyNywxNi44Mjk0NTQ1IEMyMC43NTM0NTQ1LDEyLjk4NDcyNzMgMjAuNzUzNDU0NSw2Ljc1MjU0NTQ1IDE2LjkwOTA5MDksMi45MDgzNjM2NCIgaWQ9IkZpbGwtOSI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTQuODA5NjM2MzYsNC43Mjk4MTgxOCBMNS4yMTM2MzYzNiw1LjEzMzgxODE4IEM2LjQyNTYzNjM2LDMuOTIyMTgxODIgOC4wOTk4MTgxOCwzLjE3MjcyNzI3IDkuOTQ4NTQ1NDUsMy4xNzI3MjcyNyBMOS45NDkwOTA5MSwzLjE3MjcyNzI3IEMxMS43OTgxODE4LDMuMTczMDkwOTEgMTMuNDcyLDMuOTIyMzYzNjQgMTQuNjgzNjM2NCw1LjEzMzgxODE4IEwxNS4wODc2MzY0LDQuNzI5ODE4MTggTDE1LjA4OTgxODIsNC43Mjc2MzYzNiBMMTYuOTA5MDkwOSwyLjkwODM2MzY0IEMxMy4wNjUwOTA5LC0wLjkzNTgxODE4MiA2LjgzMjcyNzI3LC0wLjkzNjE4MTgxOCAyLjk4ODM2MzY0LDIuOTA4NTQ1NDUgTDQuODA5NjM2MzYsNC43Mjk4MTgxOCIgaWQ9IkZpbGwtMTAiPjwvcGF0aD4gICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik0zLjI1Miw5Ljg2OTI3MjczIEMzLjI1Miw4LjAxOTgxODE4IDQuMDAxODE4MTgsNi4zNDU2MzYzNiA1LjIxMzYzNjM2LDUuMTMzODE4MTggTDIuOTg4MzYzNjQsMi45MDg1NDU0NSBMMi45ODgsMi45MDg3MjcyNyBDLTAuODU1NjM2MzY0LDYuNzUyNTQ1NDUgLTAuODU2MTgxODE4LDEyLjk4NiAyLjk4NzYzNjM2LDE2LjgzIEw0LjgwNzA5MDkxLDE1LjAxMDM2MzYgTDQuODA5MDkwOTEsMTUuMDA4NTQ1NSBMNS4yMTM0NTQ1NSwxNC42MDQgQzQuMDAxNjM2MzYsMTMuMzkyMzYzNiAzLjI1MiwxMS43MTgzNjM2IDMuMjUyLDkuODY5MjcyNzMiIGlkPSJGaWxsLTExIj48L3BhdGg+ICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMTUuMDksMTUuMDEgTDE1LjA4NzYzNjQsMTUuMDA4IEwxNC42ODM4MTgyLDE0LjYwNDE4MTggQzEzLjQ3MjE4MTgsMTUuODE1ODE4MiAxMS43OTgzNjM2LDE2LjU2NTA5MDkgOS45NDkwOTA5MSwxNi41NjUyNzI3IEw5Ljk0ODU0NTQ1LDE2LjU2NTI3MjcgQzguMDk5NjM2MzYsMTYuNTY1MjcyNyA2LjQyNTQ1NDU1LDE1LjgxNTgxODIgNS4yMTM0NTQ1NSwxNC42MDQgTDQuODA5MDkwOTEsMTUuMDA4NTQ1NSBMNC44MDcwOTA5MSwxNS4wMTAzNjM2IEwyLjk4NzYzNjM2LDE2LjgzIEM2LjgzMTgxODE4LDIwLjY3NDE4MTggMTMuMDY1MDkwOSwyMC42NzM2MzY0IDE2LjkwOTA5MDksMTYuODMgTDE2LjkwOTI3MjcsMTYuODI5NDU0NSBMMTUuMDksMTUuMDEiIGlkPSJGaWxsLTEyIj48L3BhdGg+ICAgICAgICAgICAgICAgIDwvZz4gICAgICAgICAgICAgICAgPGcgaWQ9Im91dGVyIj4gICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik0yNS4zNjM4MTgyLDQuNDUzODE4MTggTDIzLjM0MzA5MDksNi40NzUwOTA5MSBDMjguMDI5ODE4MiwxMS4xNjEyNzI3IDI4LjAyODU0NTUsMTguNzU5NjM2NCAyMy4zNDI1NDU1LDIzLjQ0NTI3MjcgTDIzLjM0MTgxODIsMjMuNDQ1NjM2NCBMMjUuMzYzMDkwOSwyNS40NjYzNjM2IEwyNS4zNjM0NTQ1LDI1LjQ2NjE4MTggQzMxLjE2NTI3MjcsMTkuNjY0MTgxOCAzMS4xNjU2MzY0LDEwLjI1NiAyNS4zNjM4MTgyLDQuNDUzODE4MTgiIGlkPSJGaWxsLTMiPjwvcGF0aD4gICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik02LjM3MjM2MzY0LDYuNDc2MTgxODIgTDQuMzUxNDU0NTUsNC40NTUyNzI3MyBDLTEuNDUwNTQ1NDUsMTAuMjU4MTgxOCAtMS40NTA1NDU0NSwxOS42NjQ5MDkxIDQuMzUxNjM2MzYsMjUuNDY2NTQ1NSBMNi4zNzIzNjM2NCwyMy40NDU2MzY0IEMxLjY4NjU0NTQ1LDE4Ljc1OTYzNjQgMS42ODYxODE4MiwxMS4xNjI5MDkxIDYuMzcyMzYzNjQsNi40NzYxODE4MiIgaWQ9IkZpbGwtNSI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTYuMzcyMzYzNjQsMjMuNDQ1NjM2NCBMNC4zNTE2MzYzNiwyNS40NjY1NDU1IEMxMC4xNTM2MzY0LDMxLjI2ODkwOTEgMTkuNTYsMzEuMjY4OTA5MSAyNS4zNjMwOTA5LDI1LjQ2NjM2MzYgTDIzLjM0MTgxODIsMjMuNDQ1NjM2NCBDMTguNjU1MjcyNywyOC4xMzIgMTEuMDU4NTQ1NSwyOC4xMzIgNi4zNzIzNjM2NCwyMy40NDU2MzY0IiBpZD0iRmlsbC02Ij48L3BhdGg+ICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNNi4zNzMwOTA5MSw2LjQ3NTI3MjczIEMxMS4wNTg1NDU1LDEuNzg5ODE4MTggMTguNjU3MDkwOSwxLjc4ODcyNzI3IDIzLjM0MzA5MDksNi40NzUwOTA5MSBMMjUuMzYzODE4Miw0LjQ1MzgxODE4IEMxOS41NjE2MzY0LC0xLjM0NzgxODE4IDEwLjE1MzYzNjQsLTEuMzQ2OTA5MDkgNC4zNTIsNC40NTQ3MjcyNyBMNC4zNTE0NTQ1NSw0LjQ1NTI3MjczIEw2LjM3MjM2MzY0LDYuNDc2MTgxODIgTDYuMzczMDkwOTEsNi40NzUyNzI3MyIgaWQ9IkZpbGwtNyI+PC9wYXRoPiAgICAgICAgICAgICAgICA8L2c+ICAgICAgICAgICAgPC9nPiAgICAgICAgPC9nPiAgICA8L2c+PC9zdmc+'
			);

			//add products menu
			add_submenu_page(
				'fancy_product_designer',
				 __('Products', 'radykal'),
				 __('Products', 'radykal'),
				 Fancy_Product_Designer::CAPABILITY,
				 'fancy_product_designer',
				 array( $this, 'output_react' )
			);

			//add product builder sub menu page to products menu
			add_submenu_page(
				'fancy_product_designer',
				 __('Product Builder', 'radykal'),
				 __('Product Builder', 'radykal'),
				 Fancy_Product_Designer::CAPABILITY,
				 'fpd_product_builder',
				 array( $this, 'output_react' )
			);

			//add ui&layou composer sub menu page to products menu
			add_submenu_page(
				'fancy_product_designer',
				 __('UI Composer', 'radykal'),
				 __('UI Composer', 'radykal'),
				 Fancy_Product_Designer::CAPABILITY,
				 'fpd_ui_layout_composer',
				 array( $this, 'output_react' )
			);

			//add designs sub menu page to products menu
			add_submenu_page(
				'fancy_product_designer',
				__('Designs', 'radykal'),
				__('Designs', 'radykal'),
				Fancy_Product_Designer::CAPABILITY,
				'fpd_manage_designs',
				array( $this, 'output_react' )
			);

			//add orders sub menu page to products menu
			add_submenu_page(
				'fancy_product_designer',
				__('Shortcode Orders', 'radykal'),
				__('Shortcode Orders', 'radykal'),
				Fancy_Product_Designer::CAPABILITY,
				'fpd_orders',
				array( $this, 'output_react' )
			);

			add_submenu_page(
				'fancy_product_designer',
				__('Settings', 'radykal'),
				__('Settings', 'radykal'),
				Fancy_Product_Designer::CAPABILITY,
				'fpd_settings',
				array( $this, 'output_react' )
			);

			add_submenu_page(
				'fancy_product_designer',
				__('Status & Tools', 'radykal'),
				__('Status & Tools', 'radykal'),
				Fancy_Product_Designer::CAPABILITY,
				'fpd_status',
				array( $this, 'status_page' )
			);

		}

		public function action_links( $links ) {

			return array_merge( array(
				'<a href="' . esc_url( 'http://support.fancyproductdesigner.com/' ) . '" target="_blank">' . __( 'Documentation', 'radykal' ) . '</a>',
			), $links );

		}

		public function output_react( ) {

			do_action( 'fpd_backend_react_page_start' );

			?>
			<div id="fpd-react-root"></div>
			<?php

			do_action( 'fpd_backend_react_page_end' );

		}

		public function status_page() {

			require_once(FPD_PLUGIN_ADMIN_DIR . '/views/status-tools.php' );

		}

		public function react_backend_head( $page ) {

			if( $_GET['page'] == 'fancy_product_designer' ) {

				$no_user_products = FPD_Product::get_products( array(
					'where' 	=> 'user_id IS NULL'
				) );

				//update V3.9.7
				if( isset($_POST['fpd_update_products_to_user']) ) {

					foreach( $no_user_products as $no_user_product ) {

						$fpd_update_product = new FPD_Product( $no_user_product->ID );
						$fpd_update_product->update( null, null, null, $_POST['fpd_update_products_to_user']);

					}

					update_option( 'fpd_updater_dokan_hidden', 'yes' );

				}

				if( sizeof($no_user_products) > 0 && get_option('fpd_updater_dokan_hidden', 'no') !== 'yes' ): ?>
				<div class="ui container">
					<h3>Update to V3.9.7</h3>
					<p>Your action is required to update to the latest version. Please select an user who will be assigned as creator for your current FPD products.</p>
					<form method="POST">
						<p>
							<select name="fpd_update_products_to_user">
								<?php

								$users = get_users( array('fields' => array('ID', 'user_nicename')) );

								foreach( $users as $user ) {
									echo '<option value="'.$user->ID.'">'.$user->user_nicename.'</option>';
								}

								?>
							</select>
							<input type="submit" name="run_updater" value="Run Updater" class="button-primary" style="margin-left: 20px;" />
						</p>
					</form>
				</div>
				<?php endif;

				require_once( FPD_PLUGIN_ADMIN_DIR.'/class-admin-import.php' );

				//test
				if ( isset($_GET['info']) && $_GET['info'] == 'activated' ) {
			        require_once(FPD_PLUGIN_ADMIN_DIR.'/views/modal-updated-installed-info.php');
			    }

				?>
				<form action="<?php echo admin_url('admin.php?page=fancy_product_designer'); ?>" method="POST" enctype="multipart/form-data" name="fpd_import" style="display: none !important;">
					<input type="checkbox" name="fpd_import_to_library" />
					<input name="fpd_import_file" type="file" accept=".zip" />
				</form>
				<?php

			}
			else if( $_GET['page'] == 'fpd_orders' ) {

				echo '<div class="ui container">';
				fpd_output_admin_notice(
					'info',
					 __( 'WooCommerce Orders', 'radykal' ),
					 __( 'Orders made with WooCommerce can be viewed in the order details of a WooCommerce order!', 'radykal' ),
					 class_exists('WooCommerce'),
					 'wc_orders_in_sc_orders',
					 true
				);
				echo '</div>';

			}

		}

	}
}

new FPD_Admin_Menus();

?>