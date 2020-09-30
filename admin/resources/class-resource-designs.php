<?php

if( !class_exists('FPD_Resource_Designs') ) {

	class FPD_Resource_Designs {

		public static function get_design_categories() {

			$categories = FPD_Designs::get_categories();

			return $categories;

		}

		public static function create_design_category( $title ) {

			$result = FPD_Designs::create_design_category( $title );

			if( isset( $result['error'] ) )
				return new WP_Error(
					'design-category-create-fail',
					$result['error']
				);
			else
				return $result;

		}

		public static function get_single_category( $category_id ) {

			$category_data = get_term( $category_id, 'fpd_design_category', 'ARRAY_A' );

			$category_data_res = array(
				'ID' 	=> $category_data['term_id'],
				'name' 	=> $category_data['name']
			);

			if( is_wp_error( $category_data ) )
				return new WP_Error(
					$category_data->get_error_code(),
					$category_data->get_error_message()
				);

			$category_parameters = get_option( 'fpd_category_parameters_'.$category_data['slug'] );

			if( is_string($category_parameters) )
				parse_str( $category_parameters, $category_parameters );

			$category_data_res['parameters'] = $category_parameters;

			return array(
				'category_data' => $category_data_res,
				'designs' => FPD_Designs::get_category_designs( $category_id )
			);

		}

		public static function update_design_category( $category_id, $args ) {

			$result = FPD_Designs::update_design_category( $category_id, $args );

			if( isset( $result['error'] ) )
				return new WP_Error(
					'design-category-update-fail',
					$result['error']
				);
			else
				return $result;

		}

		public static function delete_design_category( $category_id ) {

			$result = FPD_Designs::delete_design_category( $category_id );

			if( isset( $result['error'] ) )
				return new WP_Error(
					'design-category-delete-fail',
					$result['error']
				);
			else
				return $result;

		}

	}

}

?>