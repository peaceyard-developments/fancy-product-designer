<?php

//checks if a product has product designer enabled
function is_fancy_product( $post_id ) {

    return fpd_has_content( $post_id ) !== false;

}

function fpd_not_empty($value) {

	$value = gettype($value) === 'string' ? trim($value) : $value;
	return $value == '0' || !empty($value);

}

function fpd_convert_string_value_to_int($value) {

	if($value == 'yes') { return 1; }
	else if($value == 'no') { return 0; }
	else { return $value; }

}

function fpd_table_exists( $table_name ) {

	global $wpdb;
	return $wpdb->query( $wpdb->prepare("SHOW TABLES LIKE '%s'", $table_name) ) == 0 ? false : true;

}

function fpd_get_option( $key, $multiselect_to_str=true ) {
	return FPD_Settings::$radykal_settings->get_option( $key, $multiselect_to_str );
}

function fpd_convert_obj_string_to_array( $string ) {
	return json_decode( html_entity_decode( stripslashes( $string ) ), true );
}

function fpd_update_image_source( $string ) {

	if( !is_string($string) )
		return $string;

	$replace_i0_i1 = array('i0.wp.com/', 'i1.wp.com/');
	$string = str_replace($replace_i0_i1, '', $string); //remove i0/i1 sub-domains

	return $string;

}

function fpd_has_content( $post_id ) {

	$source_type = get_post_meta( $post_id, 'fpd_source_type', true );

	if( empty($source_type) || $source_type == 'category' ) {

		if( !fpd_table_exists(FPD_CATEGORIES_TABLE) )
			return false;

	}
	else {

		if( !fpd_table_exists(FPD_VIEWS_TABLE) )
			return false;

	}

	//get assigned categories
	$product_settings = new FPD_Product_Settings( $post_id );
	$ids = $product_settings->get_content_ids();

	//check if categories are not empty
	return empty($ids) ? false : $ids;

}

function fpd_sort_terms_hierarchicaly(Array &$cats, Array &$into, $parent_id = 0) {

    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parent_id) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }

    foreach ($into as $top_cat) {
        $top_cat->children = array();
        fpd_sort_terms_hierarchicaly($cats, $top_cat->children, $top_cat->term_id);
    }

}

function fpd_strip_multi_slahes( $str, $count=0 ) {

	if($count == 5)
		return $str;

	json_decode($str);

	if (json_last_error() !== JSON_ERROR_NONE)
		return fpd_strip_multi_slahes( stripslashes( $str ), ++$count);
	else
		return $str;

}

function fpd_wc_get_order_item_meta( $item_id, $order_id ) {

	$fpd_data = wc_get_order_item_meta( $item_id, 'fpd_data' );
	//V3.4.9: data stored in _fpd_data
	return empty($fpd_data) ? wc_get_order_item_meta( $item_id, '_fpd_data' ) : $fpd_data;

}

function fpd_get_files_from_uploads_by_type( $dir, $types ) {

	$urls = array();
	$path = FPD_WP_CONTENT_DIR . '/uploads/'.$dir.'/';

	if( file_exists($path) ) {

	  	$folder = opendir($path);

		while ($file = readdir ($folder)) {

			if( in_array(substr(strtolower($file), strrpos($file,".") + 1), $types) )
				$urls[] = content_url('/uploads/'.$dir.'/'.$file, FPD_PLUGIN_ROOT_PHP );

		}

		closedir($folder);
	}

	return $urls;

}

function fpd_is_first_view( $view_id ) {

	if( fpd_table_exists(FPD_VIEWS_TABLE) ) {

		global $wpdb;

		$fancy_view = new FPD_View($view_id);
		$product_id = $fancy_view->get_product_id();
		$first_product_view_id = $wpdb->get_var("SELECT ID FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$product_id." ORDER BY ID ASC");

		return $first_product_view_id == $view_id;

	}
	else
		return true;

}

function fpd_reset_image_source( $string ) {
	return preg_replace("/(http|https):\/\/(.*?)\/wp-content/i", content_url(), $string);
}

function fpd_get_contrast_color( $hexcolor ) {

    $r = hexdec(substr($hexcolor, 0, 2));
    $g = hexdec(substr($hexcolor, 2, 2));
    $b = hexdec(substr($hexcolor, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 128) ? 'black' : 'white';
}

function fpd_is_json( $string ) {

	json_decode(stripslashes($string));
	return (json_last_error() === JSON_ERROR_NONE);

}

function fpd_get_hex_name( $hex ) {

	$hex =  strtolower (str_replace('#', '', $hex ) );
	//get hex names, convert into array and make keys lowercase (hex values)
	$hex_names = json_decode( FPD_Settings_Colors::get_hex_names_object_string(), true );

	if( !is_array($hex_names) )
		return '';

	$hex_names = array_change_key_case( $hex_names, CASE_LOWER );

	return isset( $hex_names[$hex] ) ? $hex_names[$hex] : '';

}

function fpd_is_mobile() {

	if( !isset($_SERVER['HTTP_USER_AGENT']) )
		return false;

	$useragent = $_SERVER['HTTP_USER_AGENT'];

	return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4) );

}

function fpd_http_post_json($url, $data=null) {

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	if( $data )
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json')
	);

	$result = curl_exec($ch);
	curl_close($ch);

	return json_decode($result);

}

function fpd_string_to_array( $str ) {

	$str = preg_replace('/\s+/', '', $str); //remove all whitespaces

	if( !empty($str) ) {
		return explode(',', $str);
	}
	else
		return array();

}

function fpd_check_file_list( $files, $dir ) {

	if( empty($files) )
		return '';

	$files = str_replace('"', '', $files);
	$files_arr = explode(',', $files);
	$files = array();

	foreach($files_arr as $file) {

		if( file_exists($dir.basename($file)) )
			array_push($files, $file);

	}

	return $files;

}

?>