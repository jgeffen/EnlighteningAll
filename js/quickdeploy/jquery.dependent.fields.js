/*!
 * jQuery Dependent Fields
 * Based On: jQuery-Dependent-Fields (https://github.com/znbailey/jQuery-Dependent-Fields)
 *
 * Copyright 2021 Daerik Khan
 * Released under the MIT license
 */
(function($) {
	/**
	 * @param {Object} 	pluginSettings
	 * @param {Function}				[pluginSettings.callback]	(Optional) Callback function - Params: this, hide/show bool.
	 * @param {jQuery}					pluginSettings.selector		(Required) jQuery object of selectable values.
	 * @param {Array|String|Boolean}	pluginSettings.value		(Required) Value(s) to check against.
	 * @param {String} 					[pluginSettings.wrapper]	(Optional) jQuery selector of dependent parent.
	 */
	$.fn.dependsOn = function(pluginSettings) {
		// Reset Settings
		pluginSettings = $.extend({}, {
			callback: null,
			selector: null,
			value: null,
			wrapper: 'div.form-group'
		}, (pluginSettings instanceof Object) ? pluginSettings : {});

		// Variable Defaults
		var dependents = $(this);

		// Bind Change Event to Selectors
		pluginSettings.selector.on('change', function() {
			// Variable Defaults
			var showBool    = false;
			var selectorVal = '';

			// Iterate Over Selectors
			pluginSettings.selector.each(function() {
				// Break on Match
				if(showBool) return false;

				// Find Selector Type
				switch(true) {
					// Checkbox
					case $(this).is('input[type="checkbox"]'):
						// Variable Defaults
						showBool = $(this).is(':checked');
						break;

					// Select
					case $(this).is('select'):
						// Variable Defaults
						selectorVal = $(this).find('option:selected').val();

						// Set Show Bool
						if(selectorVal instanceof String) {
							if(!pluginSettings.value) {
								showBool = selectorVal && $.trim(selectorVal) !== '';
							} else {
								showBool = selectorVal === pluginSettings.value;
							}
						} else if(pluginSettings.value instanceof Array) {
							showBool = $.inArray(selectorVal, pluginSettings.value) !== -1;
						} else if(!pluginSettings.value) {
							showBool = selectorVal;
						}
						break;

					// Input
					case $(this).is('input[type="text"]'):
						// Variable Defaults
						selectorVal = $(this).val();

						// Set Show Bool
						if(selectorVal instanceof String) {
							if(!pluginSettings.value) {
								showBool = selectorVal && $.trim(selectorVal) !== '';
							} else {
								showBool = selectorVal === pluginSettings.value;
							}
						} else if(pluginSettings.value instanceof Array) {
							showBool = $.inArray(selectorVal, pluginSettings.value) !== -1;
						} else if(!pluginSettings.value) {
							showBool = selectorVal;
						}
						break;
				}
			});

			// Toggle Dependents
			dependents.each(function() {
				// Variable Defaults
				var dependent = pluginSettings.wrapper ? $(this).closest(pluginSettings.wrapper) : $(this);

				// Toggle Dependent
				dependent.toggle(showBool).find(':input').prop('disabled', !showBool);
			});

			// Handle Callback
			if(pluginSettings.callback instanceof Function) pluginSettings.callback(this, showBool);
		}).trigger('change');
	};
})(jQuery);