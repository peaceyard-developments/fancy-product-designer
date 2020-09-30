<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Designs') ) {

	class FPD_Designs {

		public $category_ids;
		public $default_image_options;

		private $designs_json = array();

		public function __construct( $category_ids, $default_image_options ) {

			$this->category_ids = $category_ids;
			$this->default_image_options = $default_image_options;

		}

		private function category_loop( $categories, $next_category=array() ) {

			foreach( $categories as $category ) {

				$category_obj = array(
					'title' => $category->name,
					'thumbnail' => get_option('fpd_category_thumbnail_url_'.$category->term_id, '')
				);

				if( isset($category->children) && sizeof($category->children) ) {

					$category_obj['category'] = array();
					$category_obj['category'] = $this->category_loop( $category->children, $category_obj['category'] );

				}
				else {

					$category_obj['designs'] = array();


					//general parameters
					$general_parameters_array = $this->default_image_options;
					$final_parameters = array();

					//get attachments from design category
					$args = array(
						 'posts_per_page' => -1,
						 'post_type' => 'attachment',
						 'orderby' => 'meta_value_num',
                         'meta_key' => "{$category->slug}_order",
						 'order' => 'ASC',
						 'fpd_design_category' => $category->slug
					);
					$designs = get_posts( $args );

					//category parameters
					$category_parameters_array = array();
					$category_parameters = get_option( 'fpd_category_parameters_'.$category->slug );
					parse_str($category_parameters, $category_parameters_array);

					//category parameters disabled
					if( !isset($category_parameters_array['enabled']) || !$category_parameters_array['enabled'] ) {
						$category_parameters_array = array();
					}

					if( is_array($designs) ) {

						foreach( $designs as $design ) {

							//merge general parameters with category parameters
							$final_parameters = array_merge( $general_parameters_array, $category_parameters_array );

							//single element parameters
							$single_design_parameters_array = array();
							$single_design_parameters = get_post_meta($design->ID, 'fpd_parameters', true);
							parse_str($single_design_parameters, $single_design_parameters_array);

							//replace with sinlge parameters if enabled
							if( isset($single_design_parameters_array['enabled']) && $single_design_parameters_array['enabled'] ) {
								$final_parameters = $single_design_parameters_array;
							}

							//get design thumbnail
							$design_thumbnail = get_post_meta($design->ID, 'fpd_thumbnail', true); //custom thumbnail
							if( empty($design_thumbnail) ) {
								$design_thumbnail = wp_get_attachment_image_src( $design->ID, 'medium' );
								$design_thumbnail = $design_thumbnail[0] ? $design_thumbnail[0] : $design->guid;
							}

							$origin_image = wp_get_attachment_image_src( $design->ID, 'full' );
							$origin_image = $origin_image[0] ? $origin_image[0] : $design->guid;

							if( isset($origin_image) ) {

								$category_obj['designs'][] = array(
									'source' => $origin_image,
									'title' => $design->post_title,
									'thumbnail' => $design_thumbnail,
									'parameters' => FPD_Parameters::to_json($final_parameters, 'image', false)
								);

							}

						}

					}

				}

				array_push($next_category, $category_obj);

			}

			return $next_category;

		}

		public function get_json() {


			$categories = get_terms('fpd_design_category', array(
				'hide_empty' => false,
				'include'	=> $this->category_ids
			));

			if( isset($this->category_ids) && !empty($this->category_ids) ) {

				//single id is returned as string, cast to array
				$this->category_ids = is_string($this->category_ids) ? str_split($this->category_ids, strlen($this->category_ids)) : $this->category_ids;

				foreach($this->category_ids as $category_id) {

					//get children ids
					$term_children_ids = get_term_children( $category_id, 'fpd_design_category' );

					//get term children
					if( !empty($term_children_ids) ) {
						$term_children = get_terms('fpd_design_category', array(
							'hide_empty' => false,
							'include'	=> $term_children_ids
						));

						//merge into categories
						$categories = array_merge($categories, $term_children);
					}

				}

			}

			$category_hierarchy = array();
			fpd_sort_terms_hierarchicaly($categories, $category_hierarchy);

			$designs_json = $this->category_loop( $category_hierarchy );

			if( !is_array($designs_json) )
				$designs_json = array();

			return json_encode($designs_json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

		}


		public static function get_categories() {

			$categories = get_terms( 'fpd_design_category', array(
			 	'hide_empty' => false,
			));

			if( !empty($categories) ) {

				$sorted_cats = array();
				$categories_data = array();
				fpd_sort_terms_hierarchicaly($categories, $sorted_cats);

				self::get_categories_data($sorted_cats, $categories_data);

				return $categories_data;

			}
			else {
				return array();
			}

		}

		public static function get_categories_data( $categories, Array &$categories_data ) {

			foreach( $categories as $cat ) {

				$category_children = isset($cat->children) && sizeof($cat->children) ? $cat->children : null;

				$categories_data[$cat->slug] = array(
					'ID' 		=> $cat->term_id,
					'name'      => $cat->name,
					'thumbnail' => get_option( 'fpd_category_thumbnail_url_'.$cat->term_id, '' ),
					'children' 	=> array()
				);

				if( !is_null($category_children) )
					self::get_categories_data( $category_children, $categories_data[$cat->slug]['children'] );

			}

		}

		public static function get_category_designs( $category_id ) {

			$category_designs_data = array();

			$args = array(
				'posts_per_page' => -1,
				'post_type' => 'attachment',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'tax_query' => array(
					array(
						'taxonomy' 			=> 'fpd_design_category',
						'include_children' 	=> false,
						'field' 			=> 'term_id',
						'terms' 			=> $category_id
					)
				)
			);

			$category_designs = get_posts( $args );

			foreach( $category_designs as $category_design ) {

				$origin_image = wp_get_attachment_image_src( $category_design->ID, 'full' );
				$origin_image = $origin_image[0] ? $origin_image[0] : $category_design->guid;

				$parameters = get_post_meta($category_design->ID, 'fpd_parameters', true);

				if( is_string($parameters) )
					parse_str( $parameters, $parameters );

				$design_props = array(
					'ID' => $category_design->ID,
					'title' => $category_design->post_title,
					'image' => $origin_image,
					'thumbnail' => get_post_meta($category_design->ID, 'fpd_thumbnail', true),
					'parameters' => (object) $parameters
				);

				array_push( $category_designs_data, $design_props );

			}

			return $category_designs_data;

		}

		public static function save_category_designs( $category_slug, $designs ) {

			//get all attachments and remove the from category
			$old_designs = get_posts( array(
				'posts_per_page' => -1,
				'post_type' => 'attachment',
				'fpd_design_category' => $category_slug
			) );

			foreach( $old_designs as $old_design ) {
				wp_remove_object_terms($old_design->ID, $category_slug, 'fpd_design_category');
			}

			if( is_array($designs) ) {

				$order = 0;
			 	//loop through all submitted images
			 	foreach( $designs as $design ) {

				 	$design_defaults = array(
						'parameters' => '',
						'thumbnail' => ''
					);

					$design = array_replace_recursive($design_defaults, $design);

				 	//update menu order
			 		$attachment = array(
						'ID'           => $design['ID'],
						'menu_order' => $order
					);

					wp_update_post( $attachment );

					//set relation between image and design category
			 		wp_set_object_terms( $design['ID'], $category_slug, 'fpd_design_category', true );

			 		update_post_meta( $design['ID'], $category_slug.'_order', $order );
			 		//set parameters for design
			 		update_post_meta( $design['ID'], 'fpd_parameters', is_array($design['parameters']) ? http_build_query($design['parameters']) : $design['parameters']);
			 		update_post_meta( $design['ID'], 'fpd_thumbnail', $design['thumbnail']);

			 		$order++;

			 	}

			}

			return 1;

		}

		public static function create_design_category( $title ) {

			$result = wp_insert_term( $title, 'fpd_design_category' );

			if( is_wp_error($result) ) {

				return array(
					'error' => $result->get_error_message()
				);

			}
			else {

				$term = get_term( intval($result['term_id']), 'fpd_design_category' );

				return array(
					'ID' => $result['term_id'],
					'slug' => $term->slug,
					'message' => __('Category successfully created!', 'radykal'),
				);

			}

		}

		public static function update_design_category( $category_id, $fields = array()) {

			if( isset($fields['thumbnail']) ) {

				if( empty($fields['thumbnail']) ) {
					$result = delete_option( 'fpd_category_thumbnail_url_'.$category_id );
				}
				else {
					$result = update_option( 'fpd_category_thumbnail_url_'.$category_id, $fields['thumbnail'] );
				}

			}

			if( isset($fields['title']) ) {

				$result = wp_update_term( $category_id, 'fpd_design_category', array(
					'name' => $fields['title']
				));

			}

			if( isset($fields['parent_id']) ) {

				$result = wp_update_term( $category_id, 'fpd_design_category', array(
					'parent' => $fields['parent_id']
				));

			}

			if( isset($fields['options']) ) {

				$category_term = get_term( $category_id, 'fpd_design_category' );

				if( is_array($fields['options']) )
					$result = update_option( 'fpd_category_parameters_'.$category_term->slug, http_build_query( $fields['options'] ) );
				else
					$result = update_option( 'fpd_category_parameters_'.$category_term->slug, $fields['options'] );

			}

			if( isset($fields['designs']) ) {

				$category = get_term($category_id, 'fpd_design_category');

				if( is_null($category) ) {

					return new WP_Error(
						'category-design-save-fail',
						__('Category does not exist!', 'radykal')
					);

				}

				$category_slug = $category->slug;

				$result = FPD_Designs::save_category_designs( $category_slug, $fields['designs']);

			}

			if( is_wp_error($result) ) {

				return array(
					'error' => is_wp_error($result) ? $result->get_error_message() : __('Something went wrong. Please try again!', 'radykal')
				);

			}
			else {

				return array(
					'message' => __('Category successfully updated!', 'radykal'),
					'object' => $result
				);

			}

		}

		public static function delete_design_category( $category_id ) {

			$result = wp_delete_term( $category_id, 'fpd_design_category' );
			delete_option( 'fpd_category_thumbnail_url_'.$category_id );

			if( is_wp_error($result) ) {

				return array(
					'error' => $result->get_error_message()
				);

			}
			else {

				return array(
					'message' => __('Category successfully deleted!', 'radykal'),
				);

			}

		}

	}

}