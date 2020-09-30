<?php

if( !class_exists('FPD_Resource_Views') ) {

	class FPD_Resource_Views {

		public static function get_product_view( $view_id ) {

			$fpd_view = new FPD_View( $view_id );
			$response = $fpd_view->get_data();

			return $response;

		}

		public static function update_product_view( $view_id, $args = array() ) {

			global $wpdb;

			$columns = array();

			if( isset( $args['title'] ) ) {
				$columns['title'] = $args['title'];
			}

			if( isset( $args['thumbnail'] ) ) {
				$columns['thumbnail'] = $args['thumbnail'];
			}

			if( isset( $args['options'] ) ) {

				$new_options = $args['options'];

				//convert boolean values to string (yes or no)
				foreach($new_options as $key => $new_option) {
					if( is_bool($new_option) ) {
						$new_options[$key] = $new_option ? 'yes' : 'no';
					}
				}

				$columns['options'] = $new_options;
			}

			if( isset( $args['elements'] ) ) {
				$columns['elements'] = json_encode( $args['elements'], JSON_UNESCAPED_SLASHES );
			}

			$fancy_view = new FPD_View( $view_id );
			$response = $fancy_view->update($columns);

			if( $response === false ) {

				return new WP_Error(
					'view-update-fail',
					__('The view could not be updated. Please try again!', 'radykal')
				);

			}
			else {

				return array(
					'message' => __('View Updated.', 'radykal')
				);

			}

		}

		public static function delete_product_view( $view_id ) {

			$fancy_view = new FPD_View( $view_id );

			if( $fancy_view->delete() )
				return array(
					'message' => __('View Deleted.', 'radykal')
				);
			else
				return new WP_Error(
					'view-delete-fail',
					__('View could not be deleted. Please try again!', 'radykal')
				);

		}

	}

}

?>