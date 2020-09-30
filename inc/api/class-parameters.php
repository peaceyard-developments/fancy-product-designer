<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Parameters')) {

	class FPD_Parameters {

		const OLD_PROPS = array(
			'x' => 'left',
			'y' => 'top',
			'textSize' => 'fontSize'
		);

		//deprecated (method not used anymore in last versions of fpd plugins, remove with V3.9)
		public static function convert_parameters_to_string( $parameters, $type = '' ) {

			return FPD_Parameters::to_json($parameters, $type);

		}

		public static function get_general_props() {

			return array(
				'left' => 'floatval',
				'top' => 'floatval',
				'originX' => 'strval',
				'originY' => 'strval',
				'z' => 'intval',
				'fill' => function($value) { return $value == "0" ? false : $value; },
				'colors' => function($value) { return is_array($value) ? implode(", ", $value) : ($value == "0" ? '' : $value); },
				'colorLinkGroup' => function($value) { return $value == "0" ? false : strval($value); },
				'draggable' => 'boolval',
				'rotatable' => 'boolval',
				'resizable' => 'boolval',
				'removable' => 'boolval',
				'flipX' => 'boolval',
				'flipY' => 'boolval',
				'zChangeable' => 'boolval',
				'scaleX' => 'floatval',
				'scaleY' => 'floatval',
				'lockUniScaling' => 'boolval',
				'uniScalingUnlockable' => 'boolval',
				'angle' => 'intval',
				'price' => 'floatval',
				'autoCenter' => 'boolval',
				'replace' => function($value) { return $value == "0" ? '' : $value; },
				'replaceInAllViews' => 'boolval',
				'autoSelect' => 'boolval',
				'topped' => 'boolval',
				'boundingBoxMode' => 'strval',
				'opacity' => 'floatval',
				'sku' => 'strval',
				'excludeFromExport' => 'boolval',
				'locked' => 'boolval',
				'showInColorSelection' => 'boolval',
				'boundingBox' => function($value) { return is_array($value) ? $value : strval($value); },
				//old (needs to be present)
				'x' => 'floatval',
				'y' => 'floatval',
			);

		}

		public static function get_image_props() {

			return array(
				'minW' => 'floatval',
				'minH' => 'floatval',
				'maxW' => 'floatval',
				'maxH' => 'floatval',
				'resizeToW' => 'strval',
				'resizeToH' => 'strval',
				'maxSize' => 'floatval',
				'minDPI' => 'intval',
				'filter' => function($value) { return $value == "0" ? false : $value; },
				'scaleMode' => 'strval',
				'minScaleLimit' => function($value) { return round(floatval($value), 4); },
				'advancedEditing' => 'boolval',
				'svgFill' => function($value) { return is_array($value) ? $value : ''; },
				'pattern' => 'strval',
				//upload zone
				'uploadZone' => 'boolval',
				'designCategories' => function($value) { return is_array($value) ? $value : array(); },
				'adds_uploads' => 'boolval',
				'adds_texts' => 'boolval',
				'adds_designs' => 'boolval',
				'uploadZoneMovable' => 'boolval',
				'uploadZoneRemovable' => 'boolval',
				//old (needs to be present)
				'scale' => 'floatval',
			);

		}

		public static function get_text_props() {

			return array(
				'fontFamily' => 'strval',
				'fontSize' => 'intval',
				'editable' => 'boolval',
				'lineHeight' => 'floatval',
				'textDecoration' => 'strval',
				'maxLength' => 'intval',
				'fontWeight' => 'strval',
				'fontStyle' => 'strval',
				'textAlign' => 'strval',
				'curvable' => 'boolval',
				'curved' => 'boolval',
				'curveSpacing' => 'intval',
				'curveRadius' => 'intval',
				'curveReverse' => 'boolval',
				'stroke' => 'strval',
				'strokeWidth' => 'intval',
				'maxLines' => 'intval',
				'letterSpacing' => 'floatval',
				'chargeAfterEditing' => 'boolval',
				'minFontSize' => 'intval',
				'maxFontSize' => 'intval',
				'textTransform' => 'strval',
				'widthFontSize' => 'intval',
				'textBox' => 'boolval',
				'width' => 'floatval', //textbox
				'shadowColor' => 'strval',
				'shadowBlur' => 'intval',
				'shadowOffsetX' => 'intval',
				'shadowOffsetY' => 'intval',
				'textNumberPlaceholder' => 'strval',
				'numberPlaceholderMin' => 'intval',
				'numberPlaceholderMax' => 'intval',
				'textLinkGroup' => 'strval',
				'pattern' => 'strval',
				'strokeColors' => function($value) { return empty($value) ? array() : explode(',',  preg_replace('/\s+/', '', $value)); },
				//old (needs to be present)
				'textSize' => 'intval',
			);

		}

		public static function parse_property( $prop_key, $value, $type = 'image' ) {

			$properties = self::get_general_props();

			if( $type === 'text' )
				$properties = array_merge( $properties, self::get_text_props() );
			else
				$properties = array_merge( $properties, self::get_image_props() );

			if( isset($properties[$prop_key]) )
				return call_user_func($properties[$prop_key], $value);
			else
				return null;

		}

		public static function to_json( $parameters, $type = 'image', $encode = true ) {

			if( empty($parameters) ) { return '{}'; }

			$json_data = array();

			foreach($parameters as $key => $value) {

				if( fpd_not_empty($value) ) {

					if( $key == 'designCategories[]' ) { //old
						$json_data['designCategories'] = self::parse_property('designCategories', $value, $type);
					}
					else if( $type == 'text' && $key == 'width' ) {
						if(isset($parameters['textBox']) && $parameters['textBox'])
							$json_data['width'] = self::parse_property('width', $value, $type);
					}
					else if( $type == 'text' && $key == 'textNumberPlaceholder' ) {

						if($value === 'text')
							$json_data['textPlaceholder'] = true;
						else if($value === 'number') {

							if( isset($parameters['numberPlaceholderMin']) && isset($parameters['numberPlaceholderMax']) ) {
								$json_data['numberPlaceholder'] = array(
									$parameters['numberPlaceholderMin'], $parameters['numberPlaceholderMax']
								);
							}
							else
								$json_data['numberPlaceholder'] = true;

						}

					}
					else { //general

						$key = array_key_exists( $key, self::OLD_PROPS ) ? self::OLD_PROPS[$key] : $key;
						$parsed_val = self::parse_property($key, $value, $type);
						if( !is_null($parsed_val) ) {
							$json_data[$key] = $parsed_val;
						}


					}

				}
			}

			//if colors are empty, remove from props, so element might get colors from global options
			if( isset($json_data['colors']) && $json_data['colors'] === '' )
				unset($json_data['colors']);

			if( isset($parameters['colorPicker']) && fpd_convert_string_value_to_int($parameters['colorPicker']) ) {
				$json_data['colors'] = true;
			}

			//upload zones media sources
			if( isset($parameters['uploadZone'])  ) {

				$json_data['customAdds'] = array();

				if( isset($parameters['adds_uploads']) )
					$json_data['customAdds']['uploads'] = (bool)$parameters['adds_uploads'];

				if( isset($parameters['adds_texts']) )
					$json_data['customAdds']['texts'] = (bool)$parameters['adds_texts'];

				if( isset($parameters['adds_designs']) )
					$json_data['customAdds']['designs'] = (bool)$parameters['adds_designs'];

			}

			//bounding box (old)
			if( empty($parameters['bounding_box_control']) ) {

				//use custom bounding box
				if(isset($parameters['bounding_box_x']) &&
				   isset($parameters['bounding_box_y']) &&
				   isset($parameters['bounding_box_width']) &&
				   isset($parameters['bounding_box_height'])
				   ) {

					if( fpd_not_empty($parameters['bounding_box_x']) &&
						fpd_not_empty($parameters['bounding_box_y']) &&
						fpd_not_empty($parameters['bounding_box_width']) &&
						fpd_not_empty($parameters['bounding_box_height'])
						) {

						$json_data['boundingBox'] = array(
							'x' => floatval($parameters['bounding_box_x']),
							'y' => floatval($parameters['bounding_box_y']),
							'width' => floatval($parameters['bounding_box_width']),
							'height' => floatval($parameters['bounding_box_height']),
						);

					}
				}

			}
			else if ( isset($parameters['bounding_box_by_other']) && fpd_not_empty(trim($parameters['bounding_box_by_other'])) ) {
				$json_data['boundingBox'] = $parameters['bounding_box_by_other'];
			}

			return $encode ?  json_encode($json_data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : $json_data;

		}

	}

}


?>