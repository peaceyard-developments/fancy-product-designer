<?php

	require_once(FPD_PLUGIN_DIR.'/inc/settings/ips/class-general-settings.php');
	require_once(FPD_PLUGIN_DIR.'/inc/settings/ips/class-image-settings.php');
	require_once(FPD_PLUGIN_DIR.'/inc/settings/ips/class-text-settings.php');
	require_once(FPD_PLUGIN_DIR.'/inc/settings/ips/class-wc-settings.php');

	$tabs = array(
		'general' => __('General', 'radykal'),
		'image-options' => __('Image Properties', 'radykal'),
		'custom-text-options' => __('Custom Text Properties', 'radykal'),
	);

	if( get_post_type($post) === 'product')
		$tabs['woocommerce-options'] = __('WooCommerce', 'radykal');

	$tabs = apply_filters( 'fpd_ips_tabs', $tabs );

	$tabs_options = array(
		'general' => FPD_IPS_General::get_options(),
		'image-options' => FPD_IPS_Image::get_options(),
		'custom-text-options' => FPD_IPS_Text::get_options(),
		'woocommerce-options' => FPD_IPS_WC::get_options(),
	);

	$tabs_options = apply_filters( 'fpd_ips_tabs_options', $tabs_options );

?>

<div class="ui dimmer modals page">
	<div class="ui modal active" id="fpd-modal-ips">

		<div class="ui clearing basic segment header">
			<div class="ui left floated basic segment">
				<h3 class="ui header">
					<?php _e('Individual Product Settings', 'radykal'); ?>
					<div class="sub header"><?php printf(
			__('Here you can set individual product designer settings. That allows to use different settings from the <a href="%s">main settings</a>.', 'radykal'),
			esc_url_raw( admin_url('admin.php?page=fpd_settings') )
		) ?></div>
				</h3>
			</div>
		</div>

		<div class="scrolling content">

			<div class="ui pointing secondary menu radykal-tabs">

				<?php foreach($tabs as $tab_key => $tab) : ?>

					<a class="item <?php echo $tab_key == 'general' ? 'active' : '';?>" data-tab-target="<?php echo $tab_key; ?>">
						<?php echo $tab; ?>
					</a>

				<?php endforeach; ?>

			</div>
			<?php foreach($tabs_options as $tabs_options_key => $tabs_option) : ?>
			<div class="radykal-tab <?php echo $tabs_options_key == 'general' ? '' : 'fpd-hidden';?>" data-tab-target="<?php echo $tabs_options_key; ?>">
				<table class="ui very basic table">
					<tbody>
						<?php foreach($tabs_option as $option) {

							if($option['type'] == 'select') {
								$select_options = array(
									'' => __( 'Use Option From Main Settings', 'radykal' )
								);
								$option['options'] = array_merge($select_options, $option['options']);
							}

							radykal_output_option_item($option);
						}
						?>
					</tbody>
				</table>
			</div>
			<?php endforeach; ?>

		</div><!-- main content -->

		<div class="actions">

			<span class="ui primary small button" id="fpd-save-admin-modal"><?php _e('Set', 'radykal'); ?></span>

		</div>

	</div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		var $modal = $('#fpd-modal-ips');

		$modal.find('.dropdown').select2({allowClear: false, width: '100%'});

		//bounding box switcher
		$('[name="designs_parameter_bounding_box_control"], [name="custom_texts_parameter_bounding_box_control"]').change(function() {

			var $this = $(this),
				$tbody = $this.parents('tbody');

			$tbody.find('.custom-bb, .target-bb').parents('tr').hide();
			if(this.value != '') {

				if(this.value == '0') { //custom
					$tbody.find('.custom-bb').parents('tr').show();
				}
				else {
					$tbody.find('.target-bb').parents('tr').show();
				}

			}
			else {
				$tbody.find('.custom-bb, .target-bb').find('input').val(''); //empty values
			}


		});

		$('#fpd-change-settings').click(function(evt) {

			evt.preventDefault();

			$modal.parent().addClass('active').css('display', 'flex !important');

			fpdFillForm($modal, $('[name="fpd_product_settings"]').val());

			$modal.find('select').change();
			$modal.find('[name="background_color"], [name="background_type"]:checked').change();

		})
		//.click();

		$modal.on('click', '#fpd-save-admin-modal', function(evt) {

			var $formFields = $modal.find('input[type="number"],input[type="text"],input[type="hidden"],input[type="radio"]:checked,select,input[type="checkbox"]:checked').filter(':not(.no-serialization)'),
				serializedObj = fpdSerializeObject($formFields);
				serializedStr = JSON.stringify(serializedObj);

			$('[name="fpd_product_settings"]').val(serializedStr);

			$modal.parent().removeClass('active').css('display', 'none !important');

		});

		$modal.parent().click(function(evt) {

			if($(evt.target).hasClass('modals')) {
				$modal.parent().removeClass('active').css('display', 'none !important');
			}

		})

	});

</script>