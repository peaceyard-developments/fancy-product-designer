<?php

if( !class_exists('FPD_Resource_Pricing_Rules') ) {

	class FPD_Resource_Pricing_Rules {

		public static function get_pricing_rules(  ) {

			if( !class_exists('Fancy_Product_Designer_Pricing') )
				return new WP_Error(
					'fpd-pricing-missing',
					__('The Fancy Product Designer Pricing add-on is not installed in your WordPress site.', 'radykal')
				);


			return json_decode(fpd_strip_multi_slahes(get_option( 'fpd_pr_groups' )));

		}

		public static function update_pricing_rules( $pricing_rules = array() ) {

			update_option( 'fpd_pr_groups', is_array( $pricing_rules ) ? json_encode($pricing_rules, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $pricing_rules );

			return array(
				'message' => __('Pricing Groups Updated.', 'radykal')
			);

		}

	}

}

?>