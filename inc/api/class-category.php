<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Category')) {

	class FPD_Category {

		public $id;

		public function __construct( $id ) {

			$this->id = $id;

		}

		public static function create( $title ) {

			if( empty($title) ) {
				return false;
			}

			global $wpdb, $charset_collate;

			//create views table if necessary
			if( !fpd_table_exists(FPD_CATEGORIES_TABLE) ) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				//create table
				$sql_string = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				              title TEXT COLLATE utf8_general_ci NOT NULL,
				              user_id BIGINT(20) UNSIGNED NULL,
							  PRIMARY KEY (ID)";

				$sql = "CREATE TABLE ".FPD_CATEGORIES_TABLE." ($sql_string) $charset_collate;";

				dbDelta($sql);
			}

			self::columns_exist();

			$inserted = $wpdb->insert(
				FPD_CATEGORIES_TABLE,
				array(
					'title' => $title,
					'user_id' 	=> get_current_user_id()
				),
				array( '%s', '%d' )
			);

			return $inserted ? $wpdb->insert_id : false;

		}

		public static function get_categories( $attrs=array() ) {

			global $wpdb;

			$defaults = array(
				'cols' 		=> '*',
				'where' 	=> '',
				'order_by' 	=> '',
				'limit' 	=> null,
				'offset' 	=> null
			);

			$attrs = apply_filters( 'fpd_get_categories_sql_attrs', $attrs );

			extract( array_merge( $defaults, $attrs ) );

			$cats = array();
			if( fpd_table_exists(FPD_CATEGORIES_TABLE) ) {

				$where = empty($where) ? '' : 'WHERE '.$where;
				$order_by = empty($order_by) ? '' : 'ORDER BY '.$order_by;
				$limit = empty($limit) ? '' : 'LIMIT '.$limit;
				$offset = empty($offset) ? '' : 'OFFSET '.$offset;

 				$cats = $wpdb->get_results("SELECT $cols FROM ".FPD_CATEGORIES_TABLE." $where $order_by $limit $offset");

			}

			return $cats;

		}

		public function add_product( $product_id ) {

			global $wpdb, $charset_collate;

			//create products table if necessary
			if( !fpd_table_exists(FPD_CATEGORY_PRODUCTS_REL_TABLE) ) {

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				//create many-to-many relationship category/products table
				$category_products_rel_sql_string = "category_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
							  product_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
							  PRIMARY KEY (category_id, product_id),
							  CONSTRAINT ".$wpdb->prefix."_fpd_category_fk FOREIGN KEY (category_id) REFERENCES ".FPD_CATEGORIES_TABLE." (ID) ON DELETE CASCADE,
							  CONSTRAINT ".$wpdb->prefix."_fpd_product_fk FOREIGN KEY (product_id) REFERENCES ".FPD_PRODUCTS_TABLE." (ID) ON DELETE CASCADE";

				$sql = "CREATE TABLE ".FPD_CATEGORY_PRODUCTS_REL_TABLE." ($category_products_rel_sql_string) $charset_collate;";
				dbDelta($sql);

			}

			$inserted = $wpdb->insert(
				FPD_CATEGORY_PRODUCTS_REL_TABLE,
				array(
					'category_id' => $this->id,
					'product_id' => $product_id
				),
				array( '%d', '%d' )
			);

			return $inserted ? $wpdb->insert_id : false;
		}

		public function get_data() {

			global $wpdb;

			return $wpdb->get_row("SELECT * FROM ".FPD_CATEGORIES_TABLE." WHERE ID=".$this->id);

		}

		public function get_products() {

			global $wpdb;

			$product_ids = $wpdb->get_col("SELECT product_id FROM ".FPD_CATEGORY_PRODUCTS_REL_TABLE." WHERE category_id=".$this->id."");
			$product_ids = implode(', ', $product_ids);
			return $wpdb->get_results("SELECT * FROM ".FPD_PRODUCTS_TABLE." WHERE ID IN(".$product_ids.")");

		}

		public function update( $title ) {

			global $wpdb;

			$columns = array();
			$colum_formats = array();

			if( !empty($title) ) {

				$columns['title'] = $title;
				array_push($colum_formats, '%s');
			}

			if( !empty($columns) ) {

				$wpdb->update(
				 	FPD_CATEGORIES_TABLE,
				 	$columns, //what
				 	array('ID' => $this->id), //where
				 	$colum_formats, //format what
				 	array('%d') //format where
				);

			}

			return $columns;

		}

		public function delete() {

			global $wpdb;

			try {
				$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_CATEGORIES_TABLE." WHERE ID=%d", $this->id) );
				return 1;
			}
			catch(Exception $e) {
				return 0;
			}

		}

		private static function columns_exist() {

			global $wpdb;

			$user_id_col_exists = $wpdb->get_var( "SHOW COLUMNS FROM ".FPD_CATEGORIES_TABLE." LIKE 'user_id'" );
			if( empty($user_id_col_exists) ) {
				$wpdb->query( "ALTER TABLE ".FPD_CATEGORIES_TABLE." ADD COLUMN user_id BIGINT(20) UNSIGNED" );
			}

		}

	}

}

?>