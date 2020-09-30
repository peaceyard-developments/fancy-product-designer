<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_WC_Dokan')) {

	class FPD_WC_Dokan {

		public function __construct() {

			add_action( 'admin_init', array( &$this, 'init_admin' ) );
			add_filter( 'admin_body_class', array(&$this, 'add_body_classes') );
			add_action( 'admin_menu', array( &$this, 'remove_menu_pages' ), 100 );
			add_action( 'admin_notices',  array( &$this, 'display_admin_notices' ) );

			//Dokan Dashboard
			add_filter( 'woocommerce_admin_order_actions', array( &$this, 'dashboard_orders_actions' ), 20, 2 );
			add_filter( 'dokan_get_dashboard_nav', array( &$this, 'dashboard_nav' ) );

			//Settings
			add_filter( 'fpd_woocommerce_settings', array( &$this, 'add_settings' ) );
			add_filter( 'fpd_settings_blocks', array( &$this, 'add_settings_block' ) );
			add_action( 'fpd_block_options_end', array(&$this, 'add_block_options') );

			//API filters
			add_filter( 'fpd_get_products_sql_attrs', array( &$this, 'get_products_sql_attrs' ) );
			add_filter( 'fpd_get_categories_sql_attrs', array( &$this, 'get_categories_sql_attrs' ) );

		}

		public function init_admin() {

			$dokan_seller_role = get_role( 'seller' );

			if( fpd_get_option( 'fpd_wc_dokan_vendor_admin_access' ) )
				$dokan_seller_role->add_cap( Fancy_Product_Designer::CAPABILITY );
			else
				$dokan_seller_role->remove_cap( Fancy_Product_Designer::CAPABILITY );


		}

		public function remove_menu_pages() {

			if( self::user_is_vendor() ) {

				remove_submenu_page( 'fancy_product_designer', 'fpd_ui_layout_composer' );
				remove_submenu_page( 'fancy_product_designer', 'fpd_manage_designs' );
				remove_submenu_page( 'fancy_product_designer', 'fpd_orders' );
				remove_submenu_page( 'fancy_product_designer', 'fpd_settings' );
				remove_submenu_page( 'fancy_product_designer', 'fpd_status' );
				remove_submenu_page( 'fancy_product_designer', 'fpd_pricing_builder' );

			}

		}

		public function display_admin_notices() {

			fpd_output_admin_notice(
				'error',
				'',
				__( 'In order to use Dokan with Fancy Product Designer, you need to uncheck the "Admin area access" option in the General settings of Dokan, so vendors can access the WordPress admin to create and manage own products for Fancy Product Designer!', 'radykal' ),
				dokan_get_option( 'admin_access', 'dokan_general' ) === 'on',
				'dokan_admin_access',
				true
			);

		}

		public function dashboard_orders_actions( $actions, $the_order ) {

			if( !is_admin() ) {

				$actions['fpd_view'] = array(
	                'url' => $the_order->get_edit_order_url(),
	                'name' => __( 'View Customized Product', 'radykal' ),
	                'action' => "view_fpd",
	                'icon' => '<i class="fa fa-folder-open">&nbsp;</i>'
	            );

			}

			return $actions;

		}

		public function dashboard_nav( $urls ) {

			if( dokan_get_option( 'admin_access', 'dokan_general' ) === 'off' ) {

				$urls['fpd_admin'] = array(
		            'title'      => __( 'Product Designer', 'radykal'),
		            'icon'       => '<i class="fa fa-image"></i>',
		            'url'        => admin_url( 'admin.php?page=fancy_product_designer' ),
		            'pos'        => 40,
		            'permission' => 'dokan_view_overview_menu'
		        );

			}

			return $urls;

		}

		public function add_settings( $wc_settings ) {

			$wc_settings['dokan'] = array(

				array(
					'title' => __( 'Admin Access', 'radykal' ),
					'description' 		=> __( 'Vendors get access to the Fancy Product Designer admin (Products, Product Builder) in order to create own products.', 'radykal' ),
					'id' 		=> 'fpd_wc_dokan_vendor_admin_access',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'FPD Products From User', 'radykal' ),
					'description' 		=> __( 'The FPD products created by selected user can be used by all vendors for their own products.', 'radykal' ),
					'placeholder' => __( 'Select From Users', 'radykal' ),
					'id' 		=> 'fpd_wc_dokan_user_global_products',
					'default'	=> 'none',
					'type' 		=> 'select',
					'css'		=> 'width: 200px',
					'options'   => self::get_users_options()
				),


			);

			return $wc_settings;

		}

		public function add_settings_block( $settings_blocks ) {

			$settings_blocks['woocommerce']['dokan'] = __('Dokan', 'radykal');

			return $settings_blocks;

		}

		public function add_block_options( $settings_blocks ) {

			$options = FPD_Settings_WooCommerce::get_options();
			FPD_Settings::$radykal_settings->add_block_options( 'dokan', $options['dokan']);

		}

		public function get_products_sql_attrs( $attrs ) {

			$where = isset( $attrs['where'] ) ? $attrs['where'] : null;

			if( self::user_is_vendor() ) {

				$user_ids = array(get_current_user_id());

				//add fpd products from user
				$fpd_products_user_id = fpd_get_option( 'fpd_wc_dokan_user_global_products' );

				//skip if no use is set or on product builder
				if( $fpd_products_user_id !== 'none' && !(isset( $_GET['page'] ) && $_GET['page'] === 'fpd_product_builder') )
					array_push( $user_ids, $fpd_products_user_id );

				$user_ids = join( ",", $user_ids );

				$where = empty($where) ? "user_id IN ($user_ids)" : $where." AND user_id IN ($user_ids)";

			}

			//manage products filter
			if( isset($_POST['fpd_filter_users_select']) && $_POST['fpd_filter_users_select'] != "-1" ) {
				$where = "user_id=".$_POST['fpd_filter_users_select'];
			}

			$attrs['where'] = $where;

			return $attrs;

		}

		public function get_categories_sql_attrs( $attrs ) {

			$where = isset( $attrs['where'] ) ? $attrs['where'] : null;

			//only return products created by the current logged-in user
			if( self::user_is_vendor() ) {
				$where = empty($where) ? 'user_id='.get_current_user_id() : $where.' AND user_id='.get_current_user_id();
			}

			$attrs['where'] = $where;

			return $attrs;

		}

		private static function get_users_options() {

			$users_options = array(
				'none' => __( 'None', 'radykal' )
			);
			$users = get_users( array('fields' => array('ID', 'user_nicename')) );

			foreach( $users as $user ) {
				$users_options[$user->ID] = $user->user_nicename;
			}

			return $users_options;

		}

		public static function user_is_vendor() {

			$user = wp_get_current_user();
			return in_array( 'seller', (array) $user->roles );

		}

		public function add_body_classes( $classes ) {

			if( self::user_is_vendor() )
				$classes .= ' dokan-vendor';

			return $classes;

		}

	}
}

new FPD_WC_Dokan();

?>