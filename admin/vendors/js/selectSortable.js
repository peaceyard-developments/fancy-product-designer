;(function ( $, window, document, undefined ) {

	var pluginName = 'radykalSelectSortable',
		defaults = {
            select2Options: {}
        };

	var RadykalSelectSortable = function(element, options) {

		var _this = this;

		this.element = element;
		this.$element = $(element);
		this.$selectOptions = this.$element.children('option');
		this.options = $.extend( {}, defaults, options) ;
		this._defaults = defaults;
		this.selected = this.$element.data('selected') || '';

		var _init = function () {

			_this.$hiddenInput = $('<input type="hidden" name="'+_this.element.name+'" value="'+_this.selected+'" />');
			_this.$list = $('<ul class="radykal-select-sortable-list"></ul>');

			_this.$element.before(_this.$hiddenInput).removeAttr('name');
			_this.$element.after(_this.$list);

			var initialSelected = [];
			//number=single item, length > 0 = multiple items
			if(typeof _this.selected === 'number' || _this.selected.length > 0) {
				if(isNaN(_this.selected)) {
					initialSelected = _this.selected.split(',');
				}
				else {
					initialSelected = [_this.selected];
				}
			}

			initialSelected.forEach(function(option_val) {

				var title = _this.$selectOptions.filter('[value="'+option_val+'"]').remove().data('title');
				if(title) {
					_appendItemToList(option_val, title);
				}

			});

			//update hidden input to get correct list
			_updateHiddenInput();

			//INIT SELECT2
	        _this.$element.val('').select2(_this.options.select2Options)
			//select item from select2 dropdown
			.on('change', function(evt) {

				var $this = $(this),
					title = $this.children(':selected').data('title');

				_appendItemToList(this.value, title);
				//remove item from select dropdown
				$this.children(':selected').remove();
				$this.val('');

				_updateHiddenInput();

			});

			_this.$list.sortable({
				items: "> li",
				containment: 'parent',
				//placeholder: 'radykal-sortable-placeholder',
				update: function() {
					_updateHiddenInput();
				}
			})
			//remove select item
			.on('click', 'li > span', function(evt) {

				var $item = $(this).parent('li'),
					value = $item.data('value'),
					title = $item.data('title');

				_appendItemToDropdown(value, title);

				$item.remove();
				_updateHiddenInput();

			});

	    };

	    var _updateHiddenInput = function () {

			var values = _this.$list.children('li').map(function() {return $(this).data('value');}).get();
			_this.$hiddenInput.val(values.toString());

		};

		//append item to select list
		var _appendItemToList = function(value, title) {

			if(value && title) {
				_this.$list.append('<li data-value="'+value+'" data-title="'+title+'">'+title+'<span>&times;</span></li>');
			}

		};

		var _appendItemToDropdown = function(value, title) {

			_this.$element.append('<option value="'+value+'" data-title="'+title+'">'+title+'</option>');

			//sort dropdown options by value
			var $options = _this.$element.children('option');
			$options.sort(function(a,b) {
				a = a.value;
				b = b.value;

				return a-b;
			});

			_this.$element.html($options);

			_this.$element.val('');

		};

		_init();

	};

	$.fn['radykalSelectSortable'] = function ( options ) {

        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
                new RadykalSelectSortable( this, options ));
            }
        });

    }

})( jQuery, window, document );
