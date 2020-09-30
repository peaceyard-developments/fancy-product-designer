<?php

	//DESKTOP
	$source_type = isset( $custom_fields["fpd_source_type"] ) ? $custom_fields["fpd_source_type"][0] : "category";
	$current_ind_settings = isset( $custom_fields["fpd_product_settings"] ) ? $custom_fields["fpd_product_settings"][0] : "";

	$selected_categories = isset( $custom_fields["fpd_product_categories"] ) ? $custom_fields["fpd_product_categories"][0] : "";
	if( is_serialized($selected_categories) )
		$selected_categories = unserialize($selected_categories); //V2.0, saved as array in db
	else
		$selected_categories = empty($selected_categories)? array() : explode(',', $selected_categories); //V3.0 saved as string in db

	$selected_products = isset( $custom_fields["fpd_products"] ) ? $custom_fields["fpd_products"][0] : "";
	if( is_serialized($selected_products) )
		$selected_products = unserialize($selected_products); //V2.0, saved as array in db
	else
		$selected_products = empty($selected_products)? array() : explode(',', $selected_products); //V3.0 saved as string in db

	//MOBILE
	$source_type_mobile = isset( $custom_fields["fpd_source_type_mobile"] ) ? $custom_fields["fpd_source_type_mobile"][0] : "category";

	$selected_categories_mobile = isset( $custom_fields["fpd_product_categories_mobile"] ) ? $custom_fields["fpd_product_categories_mobile"][0] : "";

	$selected_categories_mobile = empty($selected_categories_mobile)? array() : explode(',', $selected_categories_mobile);

	$selected_products_mobile = isset( $custom_fields["fpd_products_mobile"] ) ? $custom_fields["fpd_products_mobile"][0] : "";
	$selected_products_mobile = empty($selected_products_mobile)? array() : explode(',', $selected_products_mobile);

?>
<div class="ui pointing secondary menu radykal-tabs">
	<a class="active item" data-tab-target="desktop"><?php _e('Desktop', 'radykal'); ?></a>
	<a class="item" data-tab-target="mobile"><?php _e('Mobile', 'radykal'); ?></a>
</div>

<div class="radykal-tab" data-tab-target="desktop">

	<div>
		<label><strong><?php _e( 'Source Type', 'radykal' ); ?></strong></label>
		<span style="padding-right: 20px;">
			<input type="radio" name="fpd_source_type" value="category" <?php checked($source_type, 'category') ?> />
			<?php _e( 'Category', 'radykal' ); ?>
		</span>
		<span>
			<input type="radio" name="fpd_source_type" value="product" <?php checked($source_type, 'product') ?> />
			<?php _e( 'Product', 'radykal' ); ?>
		</span>
	</div>
	<div>
		<div class="fpd-categories">
			<label><strong><?php _e( 'Product Categories', 'radykal' ); ?></strong></label>
			<select data-placeholder="<?php _e( 'Add categories to selection.', 'radykal' ); ?>" class="radykal-select-sortable" style="width: 100%;" data-selected="<?php echo implode(',', $selected_categories); ?>" name="fpd_product_categories">
			<?php

				$categories = FPD_Category::get_categories( array(
					'order_by' => 'title ASC'
				) );

				foreach($categories as $category) {
					$cat_title = '#'.$category->ID . ' - ' . $category->title;
					echo '<option value="'.$category->ID.'" data-title="'.$cat_title.'">'.$cat_title.'</option>';
				}

			?>
			</select>
			<p class="description"><?php _e( 'Sort items by drag & drop.', 'radykal' ); ?></p>
		</div>
		<div class="fpd-products">
			<label><strong><?php _e( 'Products', 'radykal' ); ?></strong></label>
			<select data-placeholder="<?php _e( 'Add products to selection.', 'radykal' ); ?>" class="radykal-select-sortable" style="width: 100%;" name="fpd_products" data-selected="<?php echo implode(',', $selected_products); ?>">
				<?php

					$products = FPD_Product::get_products( array(
						'order_by' 	=> "ID ASC",
					) );

					foreach($products as $fpd_product) {
						$product_title = '#'.$fpd_product->ID . ' - ' . $fpd_product->title;
						echo '<option value="'.$fpd_product->ID.'" data-title="'.$product_title.'">'.$product_title.'</option>';
					}

				?>
			</select>
			<p class="description"><?php _e( 'Sort items by drag & drop.', 'radykal' ); ?></p>
		</div>
	</div>

</div><!-- Tab: Desktop -->

<div class="radykal-tab fpd-hidden" data-tab-target="mobile">

	<div>
		<label><strong><?php _e( 'Source Type', 'radykal' ); ?></strong></label>
		<span style="padding-right: 20px;">
			<input type="radio" name="fpd_source_type_mobile" value="category" <?php checked($source_type_mobile, 'category') ?> />
			<?php _e( 'Category', 'radykal' ); ?>
		</span>
		<span>
			<input type="radio" name="fpd_source_type_mobile" value="product" <?php checked($source_type_mobile, 'product') ?> />
			<?php _e( 'Product', 'radykal' ); ?>
		</span>
	</div>
	<div>
		<div class="fpd-categories">
			<label><strong><?php _e( 'Product Categories', 'radykal' ); ?></strong></label>
			<select data-placeholder="<?php _e( 'Add categories to selection.', 'radykal' ); ?>" class="radykal-select-sortable" style="width: 100%;" data-selected="<?php echo implode(',', $selected_categories_mobile); ?>" name="fpd_product_categories_mobile">
			<?php

				$categories = FPD_Category::get_categories( array(
					'order_by' => 'title ASC'
				) );

				foreach($categories as $category) {
					$cat_title = '#'.$category->ID . ' - ' . $category->title;
					echo '<option value="'.$category->ID.'" data-title="'.$cat_title.'">'.$cat_title.'</option>';
				}

			?>
			</select>
			<p class="description"><?php _e( 'Sort items by drag & drop.', 'radykal' ); ?></p>
		</div>
		<div class="fpd-products">
			<label><strong><?php _e( 'Products', 'radykal' ); ?></strong></label>
			<select data-placeholder="<?php _e( 'Add products to selection.', 'radykal' ); ?>" class="radykal-select-sortable" style="width: 100%;" name="fpd_products_mobile" data-selected="<?php echo implode(',', $selected_products_mobile); ?>">
				<?php

					$products = FPD_Product::get_products( array(
						'order_by' 	=> "ID ASC",
					) );

					foreach($products as $fpd_product) {
						$product_title = '#'.$fpd_product->ID . ' - ' . $fpd_product->title;
						echo '<option value="'.$fpd_product->ID.'" data-title="'.$product_title.'">'.$product_title.'</option>';
					}

				?>
			</select>
			<p class="description"><?php _e( 'Sort items by drag & drop.', 'radykal' ); ?></p>
		</div>
	</div>

</div><!-- Tab: Mobile -->

<hr />

<div>
	<input type="hidden" name="fpd_product_settings" class="widefat" value="<?php echo $current_ind_settings; ?>" />
	<a href="#" id="fpd-change-settings"><?php _e( 'Individual Product Settings', 'radykal' ); ?></a>
</div>
<script type="text/javascript">

	jQuery(document).ready(function($) {

		//FANCY PRODUCT CHECKBOX
		$('#_fancy_product').change(function() {
			if($(this).is(':checked')) {
				$('.hide_if_fancy_product').show();
			}
			else {
				$('.hide_if_fancy_product').hide();
			}
		}).change();

		//source type
		$('[name="fpd_source_type"], [name="fpd_source_type_mobile"]').change(function() {

			var $tabContent = $(this).parents('.radykal-tab:first');

			if($tabContent.find('input[type="radio"]:checked').val() === 'category') {
				$tabContent.find('.fpd-categories').show();
				$tabContent.find('.fpd-products').hide();
			}
			else {
				$tabContent.find('.fpd-categories').hide();
				$tabContent.find('.fpd-products').show();
			}

		}).change();

	});

</script>