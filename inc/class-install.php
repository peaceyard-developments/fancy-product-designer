<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Install')) {

	class FPD_Install {

		const VERSION_NAME = 'fancyproductdesigner_version';
		const UPDATE_VERSIONS = array(
			'3.4.0' ,'3.4.3' ,'3.4.9' ,'3.5.1' ,'3.5.2', '3.5.4', '3.6.3', '3.7.2', '3.7.6', '3.7.9', '3.8.0', '3.8.4', '3.8.8', '3.9.0', '3.9.2', '3.9.3', '3.9.5', '4.0.0', '4.0.6', '4.1.0', '4.1.1', '4.1.4', '4.2.0', '4.3.0', '4.3.1'
		);
		const UPDATE_LANG_VERSION = '4.3.1';

		public function __construct() {

			register_activation_hook( FPD_PLUGIN_ROOT_PHP, array( &$this, 'activate_plugin' ) );
            //Uncomment this line to delete all database tables when deactivating the plugin
            //register_deactivation_hook( FPD_PLUGIN_ROOT_PHP, array( &$this,'deactive_plugin' ) );
            add_action( 'init', array( &$this,'check_version' ), 20 );
            add_action( 'wp_initialize_site', array( &$this, 'new_site'), 20, 2 );

		}

		public function check_version() {

			if( is_admin() && get_option(self::VERSION_NAME) != Fancy_Product_Designer::VERSION) {

				$this->upgrade();

			}
		}

		//install when a new network site is added
		public function new_site( $new_site, $args ) {

			if ( ! function_exists( 'is_plugin_active_for_network' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		    global $wpdb;

		    if ( is_plugin_active_for_network('fancy-product-designer/fancy-product-designer.php') ) {

		        $old_blog = $wpdb->blogid;
		        switch_to_blog($new_site->blog_id);
		        $this->activate_plugin();
		        switch_to_blog($old_blog);

		    }

		}

		public function activate_plugin() {

		   if(version_compare(PHP_VERSION, '5.6.0', '<')) {

			  deactivate_plugins(FPD_PLUGIN_ROOT_PHP); // Deactivate plugin
			  wp_die("Sorry, but you can't run this plugin, it requires PHP 5.6 or higher.");
			  return;

			}

			global $wpdb;

			if ( is_multisite() ) {

	    		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {

	                $current_blog = $wpdb->blogid;
	    			// Get all blog ids
	    			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	    			foreach ($blogids as $blog_id) {
	    				switch_to_blog($blog_id);
	    				$this->install();
	    			}

	    			switch_to_blog($current_blog);
	    			return;

	    		}

	    	}

			$this->install();

		}

		public function deactive_plugin($networkwide) {

			global $wpdb;

		    if (is_multisite()) {

		        if ($networkwide) {

		            $old_blog = $wpdb->blogid;
		            // Get all blog ids
		            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		            foreach ($blogids as $blog_id) {
		                switch_to_blog($blog_id);
		                $this->deinstall();
		            }

		            switch_to_blog($old_blog);
		            return;

		        }

		    }

		    $this->deinstall();

		}

		//all things that need to be installed on activation
		private function install() {

			//if version name option does not exist, its a new installation
			if( get_option(self::VERSION_NAME) === false ) {

				update_option(self::VERSION_NAME, Fancy_Product_Designer::VERSION);
				update_option('fpd_plugin_activated', true);

			}

		}

		private function deinstall() {

			global $wpdb;

			$wpdb->query("SET FOREIGN_KEY_CHECKS=0;");
			if( fpd_table_exists(FPD_CATEGORIES_TABLE) )
				$wpdb->query( "DROP TABLE ".FPD_CATEGORIES_TABLE."");
			if( fpd_table_exists(FPD_PRODUCTS_TABLE) )
				$wpdb->query( "DROP TABLE ".FPD_PRODUCTS_TABLE."");
			if( fpd_table_exists(FPD_CATEGORY_PRODUCTS_REL_TABLE) )
				$wpdb->query( "DROP TABLE ".FPD_CATEGORY_PRODUCTS_REL_TABLE."");
			if( fpd_table_exists(FPD_VIEWS_TABLE) )
				$wpdb->query( "DROP TABLE ".FPD_VIEWS_TABLE."");
			if( fpd_table_exists(FPD_TEMPLATES_TABLE) )
				$wpdb->query( "DROP TABLE ".FPD_TEMPLATES_TABLE."");
			$wpdb->query("SET FOREIGN_KEY_CHECKS=1;");

			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'fpd_%'");

		}

		public function upgrade() {

			$current_version = get_option(self::VERSION_NAME);

			foreach(self::UPDATE_VERSIONS as $update_version) {

				if( version_compare($current_version, $update_version, '<') ) {
					self::do_upgrade($update_version);
				}

			}

			update_option(self::VERSION_NAME, Fancy_Product_Designer::VERSION);
			update_option( 'fpd_update_notice', true );

		}

		public static function do_upgrade( $to_version ) {

			global $wpdb;

			if ( !class_exists('FPD_Settings_Labels') )
				require_once(FPD_PLUGIN_DIR.'/inc/settings/class-labels-settings.php');

			if( $to_version == self::UPDATE_LANG_VERSION )
				FPD_Settings_Labels::update_all_languages();


			if($to_version === '3.2.1') {

				if( file_exists(FPD_WP_CONTENT_DIR . '/uploads/fpd_patterns/') )
					rename(FPD_WP_CONTENT_DIR . '/uploads/fpd_patterns/', FPD_WP_CONTENT_DIR . '/uploads/fpd_patterns_text/');

			}
			else if($to_version === '3.2.2') {

				delete_option('fpd_lang_default');

				$all_cats = get_terms( 'fpd_design_category', array(
				 	'hide_empty' => false,
				 	'orderby' 	 => 'name',
				));

				foreach($all_cats as $cat) {

					$all_designs = get_posts(array(
						'posts_per_page' => -1,
						'post_type' => 'attachment',
						'tax_query' => array(
								array(
									'taxonomy' => 'fpd_design_category',
									'field'    => 'slug',
									'terms' => $cat->slug,
							),
						),
					));

					if(sizeof($all_designs) > 0) {

						foreach($all_designs as $design) {
							update_post_meta( $design->ID, $cat->slug.'_order', $design->menu_order );
						}

					}
				}

			}
			else if($to_version === '3.4.3') {

				update_option( 'fpd_instagram_redirect_uri', plugins_url( '/assets/templates/instagram_auth.php', FPD_PLUGIN_ROOT_PHP ) );

			}
			else if($to_version === '3.4.3') {

				update_option( 'order:_view_customized_product', get_option( 'order:_email_view_customized_product', 'View Customized Product' ) );

			}
			else if($to_version === '4.0.6') {

				update_option( 'fpd_customization_required', get_option('fpd_customization_required', 'no') == 'no' ? 'none' : 'any'  );

			}
			else if($to_version === '4.1.0') {

				if( fpd_table_exists(FPD_PRODUCTS_TABLE) ) {
					$wpdb->query( "UPDATE ".FPD_PRODUCTS_TABLE." SET options = REPLACE(options, 'stage_width', 'stageWidth')");
					$wpdb->query( "UPDATE ".FPD_PRODUCTS_TABLE." SET options = REPLACE(options, 'stage_height', 'stageHeight')");
				}

				if( fpd_table_exists(FPD_VIEWS_TABLE) ) {
					$wpdb->query( "UPDATE ".FPD_VIEWS_TABLE." SET options = REPLACE(options, 'stage_width', 'stageWidth')");
					$wpdb->query( "UPDATE ".FPD_VIEWS_TABLE." SET options = REPLACE(options, 'stage_height', 'stageHeight')");
				}

			}
			else if($to_version === '4.2.0') {
				update_option( 'fpd_react_enabled', 'yes' );
			}

		}
	}
}

new FPD_Install();

?>