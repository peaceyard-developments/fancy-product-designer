jQuery(document).ready(function($) {

	var $modalShortcodes = $('#fpd-modal-shortcodes');

	$modalShortcodes.find('select').dropdown();

	$modalShortcodes.modal({
		centered: false,
		blurring: false,
		autofocus: false
	});

	$('#fpd-open-shortcode-builder').click(function(evt) {

		$modalShortcodes.modal('show')

	});

	//all
	$modalShortcodes.find('textarea, input[readonly]').focus(function() {
		$(this).select();
	});

	//fpd_form, fpd
	$modalShortcodes.find('#fpd-sc-pd-price').keyup(function() {
		$modalShortcodes.find('#fpd-sc-pd').val('[fpd] [fpd_form price_format="'+this.value+'"]');
	});

	//action
	var $scActionType = $modalShortcodes.find('#fpd-sc-action-type'),
		scActionTypeVal = scActionLayoutVal = '';

	FPDActions.availableActions.forEach(function(type) {

		if(type !== 'zoom') {
			$scActionType.append('<option value="'+type+'">'+toTitleCase(type.replace('-', ' '))+'</option>');
		}

	});
	$modalShortcodes.find('#fpd-action-attr select').change(function() {

		if(this.id === 'fpd-sc-action-type') {
			scActionTypeVal = this.value;
		}
		else {
			scActionLayoutVal = this.value;
		}

		setScAction();

	});

	function setScAction() {

		var scVal = '[fpd_action';
		if(scActionTypeVal !== '') {
			scVal += ' type="'+scActionTypeVal+'"';
		}
		if(scActionLayoutVal !== '') {
			scVal += ' layout="'+scActionLayoutVal+'"';
		}
		scVal += ']';

		$modalShortcodes.find('#fpd-sc-action').val(scVal);

	}

	//module
	var $scModuleType = $modalShortcodes.find('#fpd-sc-module-type'),
		scModuleTypeVal = scModuleStyleVal = '';

	$modalShortcodes.on('change keyup', '#fpd-sc-module-type, #fpd-sc-module-css', function() {

		if(this.id === 'fpd-sc-module-type') {
			scModuleTypeVal = this.value;
		}
		else {
			scModuleStyleVal = this.value;
		}

		setScModule();

	});

	function setScModule() {

		var scVal = '[fpd_module';
		if(scModuleTypeVal !== '') {
			scVal += ' type="'+scModuleTypeVal+'"';
		}
		if(scModuleStyleVal !== '') {
			scVal += ' css="'+scModuleStyleVal+'"';
		}
		scVal += ']';

		$modalShortcodes.find('#fpd-sc-module').val(scVal);

	}

	function toTitleCase(str) {
	    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
	}


	$('#fpd-reset-image-sources').click(function(evt) {

		evt.preventDefault();

		var $this = jQuery(this),
			$updatedInfo = jQuery('#fpd-updated-infos').addClass('fpd-hidden');
			oldDomain = jQuery('#fpd-old-domain').val();

		var regexURL = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
		if(!regexURL.test(oldDomain)) {

			alertify.error(fpd_status_tools_opts.label_no_valid_url, 'error');
			return;
		}

		$this.addClass('loading');
		$.ajax({
			url: fpd_admin_opts.adminAjaxUrl,
			data: {
				action: 'fpd_reset_image_sources',
				_ajax_nonce: fpd_admin_opts.ajaxNonce,
				old_domain: oldDomain
			},
			type: 'post',
			dataType: 'json',
			success: function(data) {

				if(data && data.new_domain) {
					$updatedInfo.removeClass('fpd-hidden');
					$updatedInfo.find('#fpd-updated-views').html(data.updated_views);
					$updatedInfo.find('#fpd-updated-templates').html(data.updated_templates);
					$updatedInfo.find('#fpd-updated-sc-orders').html(data.updated_sc_orders);
					$updatedInfo.find('#fpd-updated-wc-orders').html(data.updated_wc_orders);
				}

				$this.removeClass('loading');

			}
		});


	})

});