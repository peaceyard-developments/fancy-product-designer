jQuery(document).ready(function($) {

	if($().wpColorPicker) {
		$('.radykal-color-picker').wpColorPicker({
			change: function(evt, ui) {

				var $input = $(this);
				setTimeout(function() {

					if($input.wpColorPicker('color') !== $input.data('tempcolor')) {
						$input.change().data('tempcolor', $input.wpColorPicker('color'));
					}

				}, 10);

			}
		});
	}

	if($().dropdown) {
		$('.semantic-select:not([data-allow-clear])').dropdown();
		$('.semantic-select[data-allow-clear]').dropdown({clearable: true});
	}

	$('.radykal-tabs > .item').click(function() {

		var $this = $(this).addClass('active');

		$this.siblings().removeClass('active');
		$this.parent().nextAll('.radykal-tab').addClass('fpd-hidden')
		.filter('[data-tab-target="'+$this.data('tab-target')+'"]').removeClass('fpd-hidden');

	});

	//SelectSortable
	if($().radykalSelectSortable) {

		$('.radykal-select-sortable').radykalSelectSortable({
			select2Options: {
				width: 'style'
			}
		});

	}

	//Upload
	var radykalUploader = null;
	$('.radykal-add-image').click(function(evt) {

		evt.preventDefault();

		var $this = $(this);

		radykalUploader = wp.media({
            multiple: false
        });

		radykalUploader.$input = $this.nextAll('input:first');
		radykalUploader.on('select', function() {
			radykalUploader.$input.val(radykalUploader.state().get('selection').toJSON()[0].url);
			radykalUploader = null;
        });

        radykalUploader.open();

	});

	$('.radykal-remove-image').click(function(evt) {

		evt.preventDefault();
		$(this).prevAll('input:first').val('');

	});

	//Relations
	$('input[data-relation]').change(function() {

		var $this = $(this),
			relationObj = fpdSerializedStringToObject($this.data('relation'));

		for (var key in relationObj) {

			if (relationObj.hasOwnProperty(key)) {

				var value = Boolean(parseInt(relationObj[key]));

				if($this.is(':checkbox')) {
					value = $this.is(':checked') ? value : !value;
				}

				//toggle target elements
				var $target = $('[name="'+key+'"], #'+key);
				$target.parents('tr:first').toggleClass('fpd-hidden', !value);

				if(value && $target.filter('[data-relation]').length) {
					$target.filter(':checked').change();
				}

			}

		}


	}).filter(':checked, :selected, :checkbox').change();

});


function fpdResetForm($form) {

	$form.find('[type="text"], [type="number"], textarea, select').val('');
	$form.find('[type="checkbox"], option').removeAttr('checked').removeAttr('selected');

};

function fpdSerializedStringToObject(str) {

	var obj = new Object();

	var fields = str.split('&');
	for(var i=0; i < fields.length; ++i) {
		var field = fields[i].split('=');
		if(field[1] !== undefined) {
			obj[field[0]] = field[1];
		}

	}

	return obj;
};

function fpdFillForm($form, obj) {

	if(typeof obj === 'string') {

		try {
			//object string
			obj = JSON.parse(obj);
		}
		catch(e) {
			//if parameter string (serialized with $.serialize), create object
			obj = fpdSerializedStringToObject(obj);
		}

	}

	if($form) {

		fpdResetForm($form);

		for(var prop in obj) {
			if(obj.hasOwnProperty(prop)) {

				var value = obj[prop],
					$formElement = $form.find('[name="'+prop+'"]');

				if($formElement.is('[type="radio"]') || $formElement.is('[type="checkbox"]')) {
					$formElement.filter('[value="'+value+'"]').prop('checked', true);
				}
				else if($formElement.is('select')) {

					//multi values
					if(typeof value === 'object') {
						for(var i=0; i < value.length; ++i) {
							$formElement.children('[value="'+value[i]+'"]').prop('selected', true);
						}
					}
					//single value
					else {
						$formElement.children('[value="'+value+'"]').prop('selected', true);
					}

				}
				else {
					$formElement.val(value);
				}

			}
		}

	}

};

function fpdSerializeObject(fields) {

    var o = {},
    	a = fields.serializeArray();

    jQuery.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
			if(this.value) {
				o[this.name].push(this.value || '');
			}

        } else {
        	if(this.value) {
	        	o[this.name] = this.value || '';
        	}
        }
    });

    return o;
};