<?php

if( !class_exists('FPD_Resource_Products') ) {

	class FPD_Resource_Products {

		public static function get_products( $args = array() ) {

			$defaults = array(
				'include_views' => true,
				'page' => 1,
				'limit' => 20,
				'search' => null,
				'category_id' => null,
				'cols' => '*',
				'filter_by' => 'ID',
				'sort_by' => 'ASC',
				'user_id' => null
			);

			$args = wp_parse_args( $args, $defaults );

			$include_views = !is_null($args['include_views']) ? boolval(intval($args['include_views'])) : true;
			$page = $args['page'] ? absint( $args['page'] ) : 1;
			$limit = $args['limit'] ? intval( $args['limit'] ) : 20;

			$offset = null;
			if( $limit != -1 ) {
				$offset = ( $page - 1 ) * $limit;
				$total = sizeof( FPD_Product::get_products() );
				$num_of_pages = ceil( $total / $limit);
			}
			else
				$limit = '';

			$where = '';
			if( isset($args['search']) && $args['search'] ) {
				$where = "title LIKE '%".$args['search']."%'";
			}

			if( isset($args['category_id']) && $args['category_id'] ) {
				$where .= empty($where) ? '' : ' AND ';
				$where .= "ID IN (SELECT product_id FROM ".FPD_CATEGORY_PRODUCTS_REL_TABLE." WHERE category_id=".$args['category_id'].")";
			}

			if( isset($args['user_id']) && $args['user_id'] ) {
				$where .= empty($where) ? '' : ' AND ';
				$where .= "user_id=".$args['user_id'];
			}

			$cols = $args['cols'] ? $args['cols'] : '*';
			$filter_by = $args['filter_by'] ? $args['filter_by'] : 'ID';
			$sort_by = $args['sort_by'] ? $args['sort_by'] : 'ASC';

			$products = FPD_Product::get_products( array(
				'cols' 		=> $cols,
				'where' 	=> $where,
				'order_by' 	=> $filter_by . ' '. $sort_by,
				'limit' 	=> $limit,
				'offset' 	=> $offset
			) );

			foreach( $products as $product ) {

				$fpd_product = new FPD_Product($product->ID);

				if( isset($product->options) )
					$product->options = is_string($product->options) ? fpd_convert_obj_string_to_array($product->options) : $product->options;

				$product->categories = $fpd_product->get_category_ids();

				if( $cols == '*' ) {

					$user_info = get_userdata( intval($product->user_id) );
					if( $user_info )
						$product->username = $user_info->user_nicename;

				}

				if( $include_views ) {

					$product->views = array();

					foreach( $fpd_product->get_views() as $fancy_view ) {

						$product->views[] = array(
							'ID' 		=> $fancy_view->ID,
							'title'		=> $fancy_view->title,
							'thumbnail' => $fancy_view->thumbnail
						);

					}

				}

			}

			return json_decode( json_encode( $products ), true );

		}

		public static function create_product( $args = array() ) {

			$id = null;
			$title = null;
			$thumbnail = null;
			$options = array();
			$categories = array();
			$views = array();

			if( isset( $args['duplicate_product_id'] ) ) { //duplicate product from present

				$title = $args['title'];

				//create new product
				$id = FPD_Product::create( $title );

				if( $id ) {

					//duplicate source product into new product
					$source_product = new FPD_Product( $args['duplicate_product_id'] );
					$new_product_id = $source_product->duplicate($id);

					$new_product = new FPD_Product( $new_product_id );

					$thumbnail = $new_product->get_thumbnail();
					$options = $new_product->get_options();
					$categories = $new_product->get_category_ids();
					$views = $new_product->get_views();

				}

			}
			else if( isset( $args['template_id'] ) ) { //create product from My Templates

				$title = $args['title'];

				//create new product
				$id = FPD_Product::create( $title );

				if( $id ) {

					$fpd_template = new FPD_Template( $args['template_id'] );
					$views = json_decode($fpd_template->get_views(), true);

					$fpd_product = new FPD_Product( $id );

					foreach($views as $view) {

						$view_id = $fpd_product->add_view(
							$view['title'],
							$view['elements'],
							$view['thumbnail']
						);


					}

					$thumbnail = $fpd_product->get_thumbnail();
					$options = $fpd_product->get_options();
					$categories = $fpd_product->get_category_ids();
					$views = $fpd_product->get_views();

				}

			}
			else if( isset( $args['zip_path'] ) ) { //create product from zip archive

				$add_to_library = $args['add_to_media_lib'];
			    $upload_dir = wp_upload_dir();
				$upload_dir = $upload_dir['basedir'];
				$extract_to_dir = $upload_dir . '/fpd_imports/';

				if( $add_to_library && file_exists( ABSPATH.'/wp-admin/includes/file.php' ) ) {
					require_once( ABSPATH.'/wp-admin/includes/file.php' );
					require_once( ABSPATH.'/wp-admin/includes/media.php' );
					require_once( ABSPATH.'/wp-admin/includes/image.php' );
				}

				if( !class_exists('FPD_Admin_Import') )
		    		require_once( FPD_PLUGIN_ADMIN_DIR.'/class-admin-import.php' );

			    $fpd_import = new FPD_Admin_Import();
			    $product_id = $fpd_import->extract_zip(
			    	$args['zip_path'],
			    	$extract_to_dir,
			    	basename($args['zip_path']),
			    	$add_to_library
			    );

			    if( $product_id ) {

				    $fpd_product = new FPD_Product( $product_id );
				    $id = $product_id;
				    $title = $fpd_product->get_title();
				    $thumbnail = $fpd_product->get_thumbnail();
					$options = $fpd_product->get_options();
					$categories = $fpd_product->get_category_ids();
					$views = $fpd_product->get_views();

			    }

			}
			else { //create new product

				$title = $args['title'];

				$options = isset( $args['options'] ) ? $args['options'] : '';
				$thumbnail = isset( $args['thumbnail'] ) ? $args['thumbnail'] : '';

				$id = FPD_Product::create( $title, $options, $thumbnail );

				if( isset($args['views']) ) {

					foreach($args['views'] as $view) {

						$view['view_title'] = $view['title'];
						self::update_product( $id, $view );

					}


				}

			}

			if( $id ) {

				$fpd_product = new FPD_Product( $id );

				$response = array(
					'message'		=> __( 'Product Created.', 'radykal' ),
					'ID' 			=> $id,
					'title' 		=> $title,
					'thumbnail'	 	=> $thumbnail,
					'options' 		=> $options,
					'categories' 	=> $categories,
					'views' 		=> $views,
					'user_id'		=> $fpd_product->get_user_id(),
					'username'		=> $fpd_product->get_username(),
				);

				return $response;

			}
			else {

				return new WP_Error(
					'product-create-fail',
					__('The product could not be created. Please try again!', 'radykal')
				);

			}

		}

		public static function update_product( $product_id, $args = array() ) {

			global $wpdb;

			$response = array();

			//add product view
			if( isset( $args['view_title'] ) )  {

				//duplicate view
				if( isset( $args['duplicate_view_id'] )) {

					$source_view = new FPD_View( $args['duplicate_view_id'] );
					$view_data = $source_view->duplicate( $args['view_title'] );

					$response['ID'] = $view_data->ID;
					$response['title'] = $view_data->title;
					$response['thumbnail'] = $view_data->thumbnail;
					$response['view_order'] = $view_data->view_order;

					$response['message'] = __( 'View Duplicated.', 'radykal' );

				}
				//new view
				else {

					$fpd_product = new FPD_Product( $product_id );

					$view_id = $fpd_product->add_view(
						$args['view_title'],
						isset($args['elements']) ? $args['elements'] : '',
						$args['thumbnail']
					);

					$response['view_id'] = $view_id;
					$response['message'] = __( 'View Added.', 'radykal' );

				}

			}
			//update properties of view
			else if( isset($args['title']) || isset($args['options']) || isset($args['thumbnail']) ) {

				$fpd_product = new FPD_Product( $product_id );

				$response = $fpd_product->update(
					isset($args['title']) ? $args['title'] : null,
					isset($args['options']) ? $args['options'] : null,
					isset($args['thumbnail']) ? $args['thumbnail'] : null
				);

				$response['message'] = __( 'Product Updated.', 'radykal' );

			}
			 //view order in product
			else if( isset( $args['view_ids'] ) ) {

				$views_ids = $args['view_ids'];

			    for($i = 0; $i < sizeof($views_ids); $i++) {

					$updated_rows = $wpdb->update(
					 	FPD_VIEWS_TABLE,
						 	array('view_order' => $i), //what
						 	array('ID' => intval($views_ids[$i])), //where
						 	array('%d'), //format what
						 	array('%d') //format where
					);

			    }

			    $response['message'] = __( 'View Order Updated.', 'radykal' );

			}

			return $response;

		}

		public static function delete_product( $product_id ) {

			$fpd_product = new FPD_Product( $product_id );

			if( $fpd_product->delete() )
				return array( 'message' => __('Product Deleted.', 'radykal') );
			else
				return new WP_Error(
					'product-delete-fail',
					__('Product can not be deleted. Please try again!.', 'radykal')
				);

		}

		public static function get_all_product_categories() {

			return FPD_Category::get_categories( array(
				'order_by' => 'title ASC'
			) );

		}

		public static function create_product_category( $args ) {

			$id = FPD_Category::create( $args['title'] );

			return array(
				'message' => __('Product Category Added.', 'radykal'),
				'ID' => $id
			);

		}

		public static function update_product_category( $category_id, $args ) {

			$response = array();

			global $wpdb;

			if( isset( $args['title'] ) ) { //update category title

				$fancy_category = new FPD_Category( $category_id );

				$columns = $fancy_category->update(
					$args['title']
				);

				$response['message'] = __('Product Category Title Updated.', 'radykal');
				$response['type'] = 'title';
				$response['data'] = $columns;

			}
			else if( isset( $args['product_id'] ) ) { //update product assignment

				$product_id = $args['product_id'];
				$assign = $args['assign'];

				if( $assign ) { //assign product to category

					$fancy_category = new FPD_Category( $category_id );
					$inserted = $fancy_category->add_product( $product_id );

				}
				else { //unassign product

					$inserted = $wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_CATEGORY_PRODUCTS_REL_TABLE." WHERE category_id=%d AND product_id=%d", $category_id, $product_id) );

				}

				$response['message'] = $assign ? __('Product Category Assigned.', 'radykal') : __('Product Category Unassigned.', 'radykal');
				$response['type'] = 'assign_product';
				$response['data'] = $inserted;


			}

			return $response;

		}

		public static function delete_product_category( $category_id ) {

			$fancy_category = new FPD_Category( $category_id );

			if( $fancy_category->delete() )
				return array( 'message' => __('Product Category Deleted.', 'radykal') );
			else
				return new WP_Error(
					'product-category-delete-fail',
					__('Product Category can not be deleted. Please try again!.', 'radykal')
				);

		}

	}

}

?>