<?php

if( !class_exists('FPD_Resource_UI_Layouts') ) {

	class FPD_Resource_UI_Layouts {

		public static function get_ui_layouts() {

			$res_json = array();
			$res_json['layouts'] = array();

			$all_layouts = FPD_UI_Layout_Composer::get_layouts();

			foreach($all_layouts as $layout) {

				$name = json_decode(stripslashes($layout->option_value), true);
				$name = $name['name'];
				$id = str_replace('fpd_ui_layout_', '', $layout->option_name);
				$res_json['layouts'][$id] = $name;

			}

			$res_json['languages'] = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc&skip_missing=0' );

			return $res_json;

		}

		public static function get_ui_layout( $id ) {

			$ui_layout = FPD_UI_Layout_Composer::get_layout( $id );

			return $ui_layout;

		}

		public static function create_ui_layout( $ui_data ) {

			$ui_data = is_array($ui_data) ? $ui_data : json_decode($ui_data, true);
			$id = sanitize_key( $ui_data['name'] );

			$save_result = FPD_UI_Layout_Composer::save_layout( $id , $ui_data );

			if( isset($save_result['type']) ) {

				return array(
					'ID' => $id,
					'message' => __('UI Created.', 'radykal')
				);

			}
			else {

				return new WP_Error(
					'ui-create-fail',
					__('The UI could not be created. Please try again.', 'radykal')
				);

			}

		}

		public static function update_ui_layout( $id, $ui_data ) {

			$ui_data = is_array($ui_data) ? $ui_data : json_decode($ui_data, true);

			$save_result = FPD_UI_Layout_Composer::save_layout( $id , $ui_data );

			if( isset($save_result['type']) ) {

				return array(
					'message' => __('UI Updated.', 'radykal')
				);

			}
			else {

				return new WP_Error(
					'create-ui-fail',
					__('The UI could not be saved. Please try again.', 'radykal')
				);

			}

		}

		public static function delete_ui_layout( $id ) {

			if( delete_option( 'fpd_ui_layout_'.$id ) )
				return array( 'message' => __('UI Deleted.', 'radykal') );
			else
				return new WP_Error(
					'ui-delete-fail',
					__('UI can not be deleted. Please try again!.', 'radykal')
				);

		}
	}

}

?>