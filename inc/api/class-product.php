<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Product')) {

	class FPD_Product {

		public $id;

		public function __construct( $id ) {

			$this->id = $id;

		}

		public static function create( $title, $options = '', $thumbnail = '' ) {

			if( empty($title) )
				return false;

			global $wpdb, $charset_collate;

			//create products table if necessary
			if( !fpd_table_exists(FPD_PRODUCTS_TABLE) ) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				//create products table
				$products_sql_string = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				              title TEXT COLLATE utf8_general_ci NOT NULL,
				              options TEXT COLLATE utf8_general_ci NULL,
				              thumbnail TEXT COLLATE utf8_general_ci NULL,
				              user_id BIGINT(20) UNSIGNED NULL,
							  PRIMARY KEY (ID)";

				$sql = "CREATE TABLE ".FPD_PRODUCTS_TABLE." ($products_sql_string) $charset_collate;";
				dbDelta($sql);

			}

			self::columns_exist();

			$inserted = $wpdb->insert(
				FPD_PRODUCTS_TABLE,
				array(
					'title' 	=> $title,
					'options' 	=> $options,
					'thumbnail' => $thumbnail,
					'user_id' 	=> get_current_user_id()
				),
				array( '%s', '%s', '%s', '%d' )
			);

			return $inserted ? $wpdb->insert_id : false;

		}

		public static function exists( $id ) {

			if( fpd_table_exists(FPD_PRODUCTS_TABLE) ) {

				global $wpdb;
				$count = $wpdb->get_var("SELECT COUNT(*) FROM ".FPD_PRODUCTS_TABLE." WHERE ID=$id");
				return $count === "1";

			}
			else {
				return false;
			}

		}

		public static function get_products( $attrs=array() ) {

			global $wpdb;

			$defaults = array(
				'cols' 		=> '*',
				'where' 	=> '',
				'order_by' 	=> '',
				'limit' 	=> null,
				'offset' 	=> null
			);

			$attrs = apply_filters( 'fpd_get_products_sql_attrs', $attrs );

			extract( array_merge( $defaults, $attrs ) );

			$products = array();
			if( fpd_table_exists(FPD_PRODUCTS_TABLE) ) {

				self::columns_exist();

				$where = empty($where) ? '' : 'WHERE '.$where;
				$order_by = empty($order_by) ? '' : 'ORDER BY '.$order_by;
				$limit = empty($limit) ? '' : 'LIMIT '.$limit;
				$offset = empty($offset) ? '' : 'OFFSET '.$offset;

 				$products = $wpdb->get_results("SELECT $cols FROM ".FPD_PRODUCTS_TABLE." $where $order_by $limit $offset");

			}

			return $products;

		}

		public function add_view( $title, $elements = '', $thumbnail = '', $order = NULL, $options = NULL ) {

			global $wpdb;

			FPD_View::create();

			//check if an order value is set
			if($order === NULL) {
				//count views of a fancy product
				$count = $wpdb->get_var("SELECT MAX(view_order) FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$this->id."");
				//count is the order value
				$order = intval($count);
				$order++;
			}

			FPD_View::columns_exist();

			$elements = is_array($elements) ? json_encode($elements) : $elements;
			$options = is_object($options) ? json_encode($options) : $options;

			$inserted = $wpdb->insert(
				FPD_VIEWS_TABLE,
				array(
					'product_id' => $this->id,
					'title' => $title,
					'elements' => $elements ? $elements : '',
					'thumbnail' => $thumbnail ? $thumbnail : '',
					'view_order' => $order,
					'options' => $options ? $options : ''
				),
				array( '%d', '%s', '%s', '%s', '%d', '%s')
			);

			return $inserted ? $wpdb->insert_id : false;

		}

		public function update( $title=null, $options=null, $thumbnail=null, $user_id=null ) {

			global $wpdb;

			$columns = array();
			$colum_formats = array();

			if( !empty($title) ) {
				$columns['title'] = $title;
				array_push($colum_formats, '%s');
			}

			if( !is_null( $options ) ) {
				$columns['options'] = empty($options) ? '' : json_encode($options);
				array_push($colum_formats, '%s');
			}

			if( !is_null( $thumbnail ) ) {
				$columns['thumbnail'] = $thumbnail;
				array_push($colum_formats, '%s');
			}

			if( !is_null( $user_id ) ) {
				$columns['user_id'] = $user_id;
				array_push($colum_formats, '%d');
			}

			if( !empty($columns) ) {

				self::columns_exist();

				$wpdb->update(
				 	FPD_PRODUCTS_TABLE,
				 	$columns, //what
				 	array('ID' => $this->id), //where
				 	$colum_formats, //format what
				 	array('%d') //format where
				);

			}

			return $columns;

		}

		public function duplicate( $new_product_id ) {

			$source_options = $this->get_options();

			$new_fp = new FPD_Product( $new_product_id );
			$new_fp->update(null, $this->get_options(), $this->get_thumbnail() );

			try {

				foreach( $this->get_views() as $view ) {

					$view_id = $new_fp->add_view(
						$view->title,
						$view->elements,
						$view->thumbnail,
						$view->view_order,
						$view->options
					);

				}

				return $new_product_id;

			}
			catch(Exception $e) {
				return false;
			}

		}

		public function delete() {

			global $wpdb;

			try {

				$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_PRODUCTS_TABLE." WHERE ID=%d", $this->id) );
				$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_VIEWS_TABLE." WHERE product_id=%d", $this->id) );

				return 1;
			}
			catch(Exception $e) {
				return 0;
			}

		}

		public function get_data() {

			global $wpdb;

			return $wpdb->get_results("SELECT * FROM ".FPD_PRODUCTS_TABLE." WHERE ID=".$this->id);

		}

		public function get_thumbnail() {

			global $wpdb;

			self::columns_exist();

			return $wpdb->get_var("SELECT thumbnail FROM ".FPD_PRODUCTS_TABLE." WHERE ID=".$this->id);

		}

		public function get_title() {

			global $wpdb;

			return $wpdb->get_var("SELECT title FROM ".FPD_PRODUCTS_TABLE." WHERE ID=".$this->id);

		}

		public function get_options() {

			global $wpdb;

			$options = $wpdb->get_var("SELECT options FROM ".FPD_PRODUCTS_TABLE." WHERE ID=".$this->id);

			if( empty($options) )
				return array();

			json_decode($options);
			if( json_last_error() !== JSON_ERROR_NONE ) { //V3.4.2 or lower, options are stored as HTML string
				$options = fpd_convert_obj_string_to_array($options);
			}
			else {
				$options = json_decode($options, true);
			}

			return $options;

		}

		public function get_category_ids() {

			global $wpdb;

			$category_ids = array();

			if( fpd_table_exists(FPD_CATEGORY_PRODUCTS_REL_TABLE) ) {

				$categories = $wpdb->get_results("SELECT category_id FROM ".FPD_CATEGORY_PRODUCTS_REL_TABLE." WHERE product_id=".$this->id);

				foreach($categories as $category) {
					array_push($category_ids, $category->category_id);
				}

			}

			return $category_ids;

		}

		public function get_user_id() {

			global $wpdb;

			return $wpdb->get_var("SELECT user_id FROM ".FPD_PRODUCTS_TABLE." WHERE ID=".$this->id);

		}

		public function get_username() {

			$user_id = $this->get_user_id();
			$user_info = get_userdata( intval($user_id) );

			return $user_info->user_nicename;

		}

		public function get_views( $serialize_elements = false ) {

			global $wpdb;

			$views = array();

			if( fpd_table_exists(FPD_VIEWS_TABLE) ) {

				$views = $wpdb->get_results("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$this->id." ORDER BY view_order ASC");
				//updates the image sources to the current domain and protocol
				foreach($views as $view_key => $view) {

					//update thumbnail source
					$view->thumbnail = fpd_reset_image_source($view->thumbnail);

					//V2 - views are serialized
					$elements = is_serialized( $view->elements ) ? @unserialize($view->elements) : json_decode($view->elements, true);

					if( is_array($elements) ) {

						foreach( $elements as $key => $element ) {

							if( $element['type'] == 'image' && !empty($element['source']) ) {
								$updated_image = fpd_reset_image_source($element['source']);
								$element['source'] = $updated_image;
							}
							$elements[$key] = $element;

						}

						$view->elements = $serialize_elements ? serialize($elements) : $elements;

					}

				}

			}

			return $views;

		}

		public function to_JSON( $encode=true ) {

			$product_array = array();

			$product_options = $this->get_options();
			$views = $this->get_views();

			$view_count = 0;
			foreach($views as $view) {

				$fancy_view = new FPD_View( $view->ID );
				$fv_options = $fancy_view->get_options();

				//setup view options for json
				$view_options = array_merge( (array) $product_options, (array) $fv_options );

				$view_json = array();
				if( $view_count == 0 ) { //only in first view

					$view_json['productTitle'] =  $this->get_title();
					if( $this->get_thumbnail() )
						$view_json['productThumbnail'] = $this->get_thumbnail();

					if( !empty($view_options['layouts_product_id']) && $this->id !=  $view_options['layouts_product_id']) {

						$fpd_product_layouts = new FPD_Product( $view_options['layouts_product_id'] );
						$view_options['layouts'] = $fpd_product_layouts->to_JSON(false);

					}

				}

				$view_json['title'] = $view->title;
				$view_json['thumbnail'] = $view->thumbnail;
				$view_json['options'] = FPD_View::setup_options( $view_options );

				if( isset( $fv_options['mask'] ) && fpd_not_empty( $fv_options['mask'] ) )
					$view_json['mask'] = $fv_options['mask'];

				//add elements to view json
				$view_json['elements'] = array();
				$elements = $view->elements;
				if( is_array($elements) ) {

					foreach($view->elements as $key => $element) {

						if($view->elements[$key]['type'] == 'text')
							$view->elements[$key]['source'] = $this->parse_text_source($view->elements[$key]['source']);

						$view->elements[$key]['parameters'] = FPD_Parameters::to_json($element['parameters'], $element['type'], false);

					}

					$view_json['elements'] = $view->elements;

				}


				if( isset( $fv_options['mask'] ) && fpd_not_empty( $fv_options['mask'] ) )
						$view_json['mask'] = $fv_options['mask'];


				//add view json array to product
				$product_array[] = $view_json;

				$view_count++;

			}

			return $encode ? json_encode($product_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $product_array;

		}

		//source: https://stackoverflow.com/questions/395379/problem-when-retrieving-text-in-json-format-containing-line-breaks-with-jquery
		private function parse_text_source($text) {

		    $text = str_replace("\r\n", "\n", $text);
		    $text = str_replace("\r", "\n", $text);
		    $text = str_replace("\n", "\\n", $text);

		    return $text;
		}

		private static function columns_exist() {

			global $wpdb;

			$thumbnail_col_exists = $wpdb->get_var( "SHOW COLUMNS FROM ".FPD_PRODUCTS_TABLE." LIKE 'thumbnail'" );
			if( empty($thumbnail_col_exists) ) {
				$wpdb->query( "ALTER TABLE ".FPD_PRODUCTS_TABLE." ADD COLUMN thumbnail TEXT COLLATE utf8_general_ci NULL" );
			}

			$user_id_col_exists = $wpdb->get_var( "SHOW COLUMNS FROM ".FPD_PRODUCTS_TABLE." LIKE 'user_id'" );
			if( empty($user_id_col_exists) ) {
				$wpdb->query( "ALTER TABLE ".FPD_PRODUCTS_TABLE." ADD COLUMN user_id BIGINT(20) UNSIGNED" );
			}

		}

	}

}

?>