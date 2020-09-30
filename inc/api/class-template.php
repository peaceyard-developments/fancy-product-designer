<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Template')) {

	class FPD_Template {

		public $id;

		const FREE_PATH = '/assets/objects-library/products/';
		const PREMIUM_PATH = '/uploads/fpd_product_templates/';

		public function __construct( $id ) {

			$this->id = $id;

		}

		public static function create( $title, $views ) {

			global $wpdb, $charset_collate;

			//create templates table if necessary
			if( !fpd_table_exists(FPD_TEMPLATES_TABLE) ) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				//create table
				$views_sql = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				              title TEXT COLLATE utf8_general_ci NOT NULL,
				              views LONGTEXT COLLATE utf8_general_ci NOT NULL,
							  PRIMARY KEY (ID)";

				$sql = "CREATE TABLE ".FPD_TEMPLATES_TABLE." ($views_sql) $charset_collate;";

				dbDelta($sql);
			}

			$inserted = $wpdb->insert(
				FPD_TEMPLATES_TABLE,
				array(
					'title' => $title,
					'views' => $views
				),
				array( '%s', '%s' )
			);

			return $inserted ? $wpdb->insert_id : false;

		}


		public static function get_templates( $type='user' ) {

			global $wpdb;

			$templates = array();

			if( fpd_table_exists(FPD_TEMPLATES_TABLE) && $type == 'user' ) {

				$templates = $wpdb->get_results("SELECT * FROM ".FPD_TEMPLATES_TABLE." ORDER BY ID DESC");

			}
			else if( $type == 'library' ) {

				$free_templates_dir = FPD_PLUGIN_DIR . self::FREE_PATH;
				$premium_templates_dir = WP_CONTENT_DIR . self::PREMIUM_PATH;

				$templates_json = fpd_admin_get_file_content( FPD_PLUGIN_DIR . '/assets/json/product_templates.json' );
				$templates_json = json_decode($templates_json);

				foreach($templates_json as $catKey => $templatesCat) {

					foreach($templatesCat->templates as $templateKey => $template) {

						if( isset($template->free) ) {

							$template->installed = true;
							$template->file_path = $free_templates_dir.$template->file;
							$template->file_url = plugins_url( self::FREE_PATH.$template->file, FPD_PLUGIN_ROOT_PHP );

						}
						else {

							$template->installed = file_exists($premium_templates_dir.$template->file);
							$template->file_path = $premium_templates_dir.$template->file;
							$template->file_url = content_url( self::PREMIUM_PATH.$template->file );

						}

						$preview_images = is_array($template->images) ? $template->images : array($template->images);
						array_walk($preview_images, function(&$value, $key) { $value = plugins_url($value, FPD_PLUGIN_ADMIN_DIR); } );
						$template->images = $preview_images;

					}

				}

				$templates = $templates_json;

			}

			return $templates;

		}

		public function get_views() {

			global $wpdb;
			$views = $wpdb->get_row('SELECT views FROM '.FPD_TEMPLATES_TABLE.' WHERE ID='.$this->id);

			return $views->views;

		}

		public function delete() {

			global $wpdb;

			try {
				$wpdb->query( $wpdb->prepare('DELETE FROM '.FPD_TEMPLATES_TABLE.' WHERE ID=%d', $this->id) );
				return 1;
			}
			catch(Exception $e) {
				return 0;
			}

		}

	}

}

?>