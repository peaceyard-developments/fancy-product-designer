<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('Radykal_Settings') ) {

	class Radykal_Settings {

		public $page_id = '';
		public $tabs = array();
		public $sections = array();
		public $settings = array();
		public $options = array();
		public $blocks = array();
		public $block_titles = array();
		public $block_descriptions = array();
		public $current_tab = '';

		public function __construct( $parameters ) {

			if( isset($parameters['page_id']) ) {
				$this->page_id = sanitize_key( $parameters['page_id'] );
			}

			/*
			* Structure: array(id=>title)
			*/
			if( isset($parameters['tabs']) ) {
				$this->tabs = $parameters['tabs'];
			}

			/*
			* Structure: array(id_of_tab=>title)
			*/
			if( isset($parameters['sections']) ) {
				$this->sections = $parameters['sections'];
			}

			$this->current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : key($this->tabs);

			foreach($this->tabs as $key => $value) {
				//first level: tabs
				$this->settings[$key] = array();
			}

		}

		/**
		 * Add blocks to a tab.
		 * Returns all available blocks.
		 */
		public function add_blocks( $blocks ) {

			$this->blocks = $blocks;

			foreach($this->blocks as $tab_block_key => $tab_block_value) {
				//second level: assign blocks to tabs
				$this->settings[$tab_block_key] = $tab_block_value;
				//store block titles
				foreach($this->blocks[$tab_block_key] as $block_key => $block_val) {
					$this->block_titles[$block_key] = $block_val;
				}
			}

			return $this->blocks;

		}

		/**
		 * Add description to blocks.
		 * Returns all available blocks.
		 */
		public function add_blocks_description( $descriptions = array() ) {

			$this->block_descriptions = $descriptions;
		}

		/**
		 * Add options to a block.
		 * Returns all available settings.
		 */
		public function add_block_options( $block_key, $options ) {

			foreach($this->settings as $tab_key => $tab_value) {

				if( isset($this->settings[$tab_key][$block_key]) ) {
					//add option to block
					$this->settings[$tab_key][$block_key] = $options;
				}
			}

			//create an array with keys=option.id
			$options_with_keys = array();
			foreach($options as $option) {
				$options_with_keys[$option['id']] = $option;
			}

			$this->options = array_merge($this->options, $options_with_keys);

			return $this->settings;

		}

		/**
		 * Get an option value. If no value is found in database, return default value
		 *
		 */
		public function get_option( $key, $multiselect_to_str=true ) {

			if( get_option($key) === false ) {
				return $this->get_default_option( $key );

			}
			else {

				$value = get_option( $key );
				//if option is type of number, it needs to return a value, otherwise its failed
				if( !$this->not_empty($value) && $this->get_option_type($key) == 'number') {

					return $this->get_default_option( $key );
				}
				//if is array, convert it into string
				else if( is_array($value) || $this->get_option_type($key) == 'multiselect') {

					if(empty($value) || $value == 'no')
						return array();
					else if($multiselect_to_str) {
						return !is_array($value) ? array() : '"' . implode('","', $value) . '"';
					}
					else {
						return $value;
					}

				}

				return $this->boolean_string_to_int( $value );

			}

		}

		/**
		 * Get the default value of an option
		 *
		 */
		public function get_default_option( $key ) {

			if( isset( $this->options[$key] ) && isset( $this->options[$key]['default'] ) ) {
				return $this->boolean_string_to_int($this->options[$key]['default']);
			}
			return false;

		}

		/**
		 * Get the type of an option
		 *
		 */
		public function get_option_type( $key ) {

			if( isset( $this->options[$key] ) ) {
				 return $this->boolean_string_to_int($this->options[$key]['type']);
			}
			return false;

		}

		/**
		 * Outputs an option item for a options table
		 *
		 */
		public static function output_item( $parameters ) {

			$id = isset($parameters['id']) ? $parameters['id'] : '';
			$title = isset($parameters['title']) ? $parameters['title'] : '';
			$type = isset($parameters['type']) ? $parameters['type'] : '';
			$description = isset($parameters['description']) ? $parameters['description'] : false;

			$default = isset($parameters['default']) ? $parameters['default'] : '';
			$css = isset($parameters['css']) ? $parameters['css'] : '';
			$class = isset($parameters['class']) ? $parameters['class'] : '';
			$value = get_option($id) !== false ? get_option($id) : '';
			$options = isset($parameters['options']) ? $parameters['options'] : array();
			$custom_attributes = isset($parameters['custom_attributes']) ? $parameters['custom_attributes'] : array();
			$relations = isset($parameters['relations']) ? $parameters['relations'] : array();
			$placeholder = isset($parameters['placeholder']) ? 'placeholder="'.esc_attr( $parameters['placeholder'] ).'"' : '';

			$input_html = '';
			$input_class = $class;
			$new_line_desc = '<br class="clear" />';
			$current_value = empty($value) && $value != '0' ? $default : $value;

			$custom_attributes_html = '';
			foreach( $custom_attributes as $ca_key => $ca_value ) {
				$custom_attributes_html .= $ca_key.'="'.esc_attr( $ca_value ).'"';
			}

			//text,number, checkbox
			if( $type == 'text' || $type == 'number' || $type == 'checkbox' || $type == 'colorpicker' || $type == 'upload' || $type == 'password' ) {

				$additional_attrs = '';
				$relation_attr = '';
				$current_value = stripslashes($current_value);

				if($placeholder == '') {
					$placeholder = 'placeholder="'.esc_attr( $default ).'"';
				}

				if( $type == 'colorpicker' ) {
					$type = 'text';
					$input_class .= ' radykal-color-picker';
				}

				$input_html = '';

				if($type == 'upload')
					$input_html .= '<div class="ui fluid action tiny input input-uploader"><span class="ui primary tiny button radykal-add-image">+</span>';
				else if( $type == 'text' || $type == 'number')
					$input_html .= '<div class="ui input">';

				$input_html .= '<input type="'.esc_attr( $type == 'upload' ? 'text' : $type ).'" id="'.esc_attr( $id ).'" name="'.esc_attr( $id ).'" '.$placeholder.' value="'.esc_attr($current_value).'" '.$additional_attrs.' style="'.$css.'" '.$custom_attributes_html.' class="'.esc_attr( $input_class ).'" '.$relation_attr.' />';

				if($type == 'upload')
					$input_html .= '<span class="ui negative tiny button radykal-remove-image">-</span></div>';
				else if( $type == 'text' || $type == 'number')
					$input_html .= '</div>';

				$input_html .= $new_line_desc;

			}
			//textarea
			else if($type == 'textarea') {

				$input_html = '<textarea id="'.esc_attr( $id ).'" name="'.esc_attr( $id ).'" class="'.esc_attr( $class ).'" style="'.esc_attr( $css ).'">'.esc_textarea( $current_value ).'</textarea>'.$new_line_desc;

			}
			//select
			else if($type == 'select' || $type == 'multiselect' || $type == 'select-sortable') {

				$multiple = $type == 'multiselect' ? 'multiple' : '';
				$brackets = $type == 'multiselect' ? '[]' : '';
				$class = $type == 'select-sortable' ? $class . ' radykal-select-sortable' : $class;
				$placeholder .= 'data-'.$placeholder;
				$allow_clear = isset( $parameters['allowclear'] ) ? 'data-allow-clear=1' : '';

				if($type == 'multiselect') {
					$class .= ' fluid';
				}

				//select-sortable
				$dataSelected = '';
				if($type == 'select-sortable') {
					$selected = is_array($current_value) ? implode(',', $current_value) : $current_value;
					$dataSelected = 'data-selected="'.$selected.'"';
				}

				$input_html = '<select id="'.esc_attr( $id ).'" name="'.esc_attr( $id.$brackets ).'" style="'.esc_attr( $css ).'" class="ui search dropdown '.esc_attr( $class ).'" '.$placeholder.' '.$multiple.' '.$dataSelected.' '.$allow_clear.'>';

				if( isset($parameters['placeholder']) )
					$input_html .= '<option value="">'.esc_attr( $parameters['placeholder'] ).'</option>';

				foreach($options as $option_key => $option_val) {

					//select-sortable
					$dataTitle = '';
					if($type == 'select-sortable') {
						$selected = '';
						$dataTitle = 'data-title='.$option_val.'';
					}
					//multiselect
					else if( is_array($current_value) ) {
						$selected = selected(in_array($option_key, $current_value), true, false);
					}
					//simple select
					else {
						$selected = selected($current_value, $option_key, false);
					}

					//check if files_dir is set and file exists
					$input_html .= '<option value="'.esc_attr( $option_key ).'" '.$selected.' '.$dataTitle.'>'.$option_val.'</option>';
				}
				$input_html .= '</select>'.$new_line_desc;

			}
			//radio
			else if( $type == 'radio' ) {

				$input_html .= '<div style="margin-bottom: 10px;">';
				foreach($options as $option_key => $option_val) {

					$relation_attr = '';
					if( isset($relations[$option_key]) )
						$relation_attr = is_array($relations[$option_key]) ? 'data-relation="'.http_build_query($relations[$option_key]).'"' : '';

					$input_html .= '<p><label><input type="radio" '.$relation_attr.' name="'.esc_attr( $id ).'" value="'.esc_attr( $option_key ).'" '.checked($current_value, $option_key, false).' />'.$option_val.'</label></p>';
				}
				$input_html .= '</div>';

			}
			else if( $type == 'button' ) {
				$input_html .= '<button id="'.esc_attr( $id ).'" name="'.esc_attr( $id ).'" style="'.$css.'" '.$custom_attributes_html.' class="'.esc_attr( $input_class ).'">'. $parameters['placeholder'] .'</button><br />';
			}

			$description_html = '';
			if( $description !== false ) {
				$description_html = '<label for="'.$id.'"><span class="description">'.wp_kses_post( $description ).'</span></label>';
			}

			?>
			<tr>
				<td <?php echo $type === 'section-title' ? 'colspan="2" class="radykal-section-title"' : 'class="top aligned six wide"'; ?>>
					<?php

						if($type === 'section-title')
							echo '<div class="ui small header">'. $title . '</div>';
						else
							echo $title;

					?>
				</td>
				<?php if( $type !== 'section-title' ): ?>
				<td class="radykal-option-type--<?php echo $type; ?>">
					<?php echo $input_html; ?>
					<?php echo $description_html;  ?>
				</td>
				<?php endif; ?>
			</tr>
			<?php

		}

		private function boolean_string_to_int($value) {

			if($value === 'yes') { return 1; }
			else if($value === 'no') { return 0; }
			else { return $value; }

		}

		private function not_empty($value) {

			return $value == '0' || !empty($value);

		}
	}
}

function radykal_output_option_item( $options ) {

	Radykal_Settings::output_item( $options );
}

?>