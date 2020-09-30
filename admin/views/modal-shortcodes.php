<div class="ui modal" id="fpd-modal-shortcodes">

	<div class="ui clearing basic segment header">
		<div class="ui left floated basic segment">
			<h3 class="ui header">
				<?php _e('Shortcodes', 'radykal') ?>
			</h3>
		</div>
	</div>

	<div class="scrolling content">

		<form class="ui form">

			<h4 class="ui dividing header">
				<?php _e('Product Designer', 'radykal') ?>
				<div class="sub header"><?php _e('Place the product designer anywhere you want with these two shortcodes. NOT NECESARRY FOR WooCommerce PRODUCTS!', 'radykal'); ?></div>
			</h4>

			<div class="two fields">
				<div class="field">
					<label><?php _e('Shortcode', 'radykal') ?></label>
					<textarea readonly="readyonly" id="fpd-sc-pd">[fpd] [fpd_form]</textarea>
				</div>
				<div class="field">
					<label><?php _e('Price Format (%d is the placeholder for the price)', 'radykal'); ?></label>
					<input type="text" placeholder="<?php _e('e.g. $%d', 'radykal'); ?>" id="fpd-sc-pd-price" />
				</div>
			</div>

			<h4 class="ui dividing header">
				<?php _e('Action Buttons', 'radykal'); ?>
				<div class="sub header"><?php _e('Place an action button anywhere in your page.', 'radykal'); ?></div>
			</h4>

			<div class="two fields">
				<div class="field">
					<label><?php _e('Shortcode', 'radykal') ?></label>
					<textarea readonly="readyonly" id="fpd-sc-action"></textarea>
				</div>
				<div class="two fields" id="fpd-action-attr">

					<div class="field">
						<label><?php _e('Type', 'radykal'); ?></label>
						<select class="ui fluid dropdown" id="fpd-sc-action-type">
							<option value=""><?php _e('Select Type', 'radykal'); ?></option>
						</select>
					</div>
					<div class="field">
						<label><?php _e('Layout', 'radykal'); ?></label>
						<select class="ui fluid dropdown" id="fpd-sc-action-layout">
							<option value=""><?php _e('Select Layout', 'radykal'); ?></option>
							<option value="icon-tooltip"><?php _e('Icon Tooltip', 'radykal'); ?></option>
							<option value="icon-text"><?php _e('Icon Text', 'radykal'); ?></option>
							<option value="text"><?php _e('Text', 'radykal'); ?></option>
						</select>
					</div>

				</div>
			</div>

			<h4 class="ui dividing header">
				<?php _e('Modules', 'radykal'); ?>
				<div class="sub header"><?php _e('Place a module anywhere in your page.', 'radykal'); ?></div>
			</h4>

			<div class="two fields">
				<div class="field">
					<label><?php _e('Shortcode', 'radykal') ?></label>
					<textarea readonly="readyonly" id="fpd-sc-module"></textarea>
				</div>
				<div class="field">
					<label><?php _e('Module', 'radykal'); ?></label>
					<select id="fpd-sc-module-type" style="margin-bottom: 10px;">
						<option disabled selected value><?php _e('Select Module', 'radykal'); ?></option>
						<option value="products"><?php _e('Products', 'radykal'); ?></option>
						<option value="images"><?php _e('Images', 'radykal'); ?></option>
						<option value="designs"><?php _e('Designs', 'radykal'); ?></option>
						<option value="text"><?php _e('Text', 'radykal'); ?></option>
						<option value="manage-layers"><?php _e('Manage Layers', 'radykal'); ?></option>
						<option value="text-layers"><?php _e('Text Layers', 'radykal'); ?></option>
						<option value="layouts"><?php _e('Layouts', 'radykal'); ?></option>
						<?php do_action( 'fpd_shortcode_module_options' ); ?>
					</select>
					<br /><br />
					<label><?php _e('Wrapper CSS Style', 'radykal'); ?></label>
					<input type="text" placeholder="<?php _e('e.g. height: 500px; width: 300px;', 'radykal'); ?>" id="fpd-sc-module-css" class="widefat" />
				</div>
			</div>

			<h4 class="ui dividing header">
				<?php _e('Saved Products', 'radykal'); ?>
				<div class="sub header"><?php _e('Displays the saved products in a grid. Only works when the option "Account Product Storage" in Settings > General tab > Actions section is enabled.', 'radykal'); ?></div>
			</h4>

			<div class="field">
				<label><?php _e('Shortcode', 'radykal') ?></label>
				<input type="text" readonly="readyonly" value="[fpd_saved_products]" />
			</div>

		</form>

	</div>

</div>
