<?php

if( !class_exists('FPD_Resource_Templates') ) {

	class FPD_Resource_Templates {

		public static function create_product_template( $args ) {

			$fancy_product = new FPD_Product( $args['product_id'] );

			$views = $fancy_product->get_views();
			foreach($views as $view) {
				unset($view->ID);
				unset($view->product_id);
				unset($view->view_order);
				unset($view->options);
			}
			$views = json_encode($views);

		    //create new template
			$template_id = FPD_Template::create( $args['title'], $views );

			if( $template_id ) {

				return array(
					'message' => __('Product Template Added.', 'radykal'),
					'ID' => $template_id
				);

			}
			else {

				return new WP_Error(
					'template-create-fail',
					__('Product Template could not be created. Please try again!', 'radykal')
				);

			}

		}

		public static function get_product_templates( $args ) {

			$templates_type = isset ( $args['type'] ) ? $args['type'] : 'user';

			$response = FPD_Template::get_templates( $templates_type );

			return $response;

		}

		public static function delete_product_template( $template_id ) {

			$fpd_template = new FPD_Template( $template_id );

			if( $fpd_template->delete() )
				return array(
					'message' => __('Product Template Deleted.', 'radykal')
				);
			else
				return new WP_Error(
					'template-delete-fail',
					__('Product Template could not be deleted. Please try again!', 'radykal')
				);

		}

	}

}

?>