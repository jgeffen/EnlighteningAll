/*!
 * jQuery Additional Inputs
 *
 * Copyright 2021 Daerik Khan
 * Released under the MIT license
 */
(function($) {
	/**
	 * @param {Object} 		[pluginSettings]
	 * @param {Function}	[pluginSettings.callback]		(Optional) Callback function - Params: this, action
	 * @param {String} 		[pluginSettings.addIcon]		(Optional) String representing snippet for add icon.
	 * @param {String} 		[pluginSettings.deleteIcon]		(Optional) String representing snippet for delete icon.
	 * @param {Array} 		[pluginSettings.buttonClasses]	(Optional) Array of strings representing the button classes.
	 * @param {Integer} 	[pluginSettings.maxInput]		(Optional) Maximum number of additional inputs.
	 */
	$.fn.additionalInputs = function(pluginSettings) {
		// Reset Settings
		pluginSettings = $.extend({}, {
			callback: null,
			addIcon: '<i class="fas fa-plus"></i>',
			deleteIcon: '<i class="fas fa-minus"></i>',
			buttonClasses: ['btn', 'btn-outline-secondary'],
			maxInput: 5
		}, (pluginSettings instanceof Object) ? pluginSettings : {});

		// Iterate Over Selectors
		$(this).each(function() {
			// Variable Defaults
			var original  = $(this);
			var clone     = original.clone(false);
			var name      = original.attr('name');
			var parent    = original.parent();
			var label_for = original.attr('id');
			var label     = $('label[for="' + label_for + '"]');
			var template  = $('<div/>', {
				html: $('<button/>', {
					type: 'button',
					html: $(pluginSettings.addIcon)
				}).addClass(['ai-button-add'].concat(pluginSettings.buttonClasses))
			}).addClass('ai-wrapper input-group');
			var clones    = function() {
				return parent.find('.ai-clone');
			};

			// Disable Original
			$(this).removeAttr('id').prop('disabled', true).addClass('ai-original').hide();

			// Add ADA to Label
			label.attr('id', label_for).removeAttr('for');

			// Add ADA to Clone
			clone.attr('aria-labelby', label_for);

			// Append Clone
			template.clone(false).prepend(clone.clone(false).removeAttr('id').attr('name', name + '[]').addClass('ai-clone')).appendTo(parent);

			// Delegate Click Event to Add Input
			parent.on('click', '.ai-button-add', function() {
				// Update Buttons
				$(this).replaceWith($('<button/>', {
					type: 'button',
					html: $(pluginSettings.deleteIcon)
				}).addClass(['ai-button-delete'].concat(pluginSettings.buttonClasses)));

				// Check Max Input
				if(pluginSettings.maxInput <= 1 || clones().length < pluginSettings.maxInput - 1) {
					// Append Clone
					template.clone(false).prepend(clone.clone(false).removeAttr('id').attr('name', name + '[]').addClass('ai-clone')).appendTo(parent);

					// Handle Callback
					if(pluginSettings.callback instanceof Function) pluginSettings.callback(this, 'added');
				} else {
					// Append Clone
					template.clone(false).prepend(clone.clone(false).removeAttr('id').attr('name', name + '[]').addClass('ai-clone')).appendTo(parent).find('.ai-button-add').replaceWith($('<button/>', {
						type: 'button',
						html: $(pluginSettings.deleteIcon)
					}).addClass(['ai-button-delete'].concat(pluginSettings.buttonClasses)));

					// Handle Callback
					if(pluginSettings.callback instanceof Function) pluginSettings.callback(this, 'max_reached');
				}
			});

			// Delegate Click Event to Delete Input
			parent.on('click', '.ai-button-delete', function() {
				// Remove Input
				$(this).parents('.ai-wrapper').remove();

				// Handle Callback
				if(pluginSettings.callback instanceof Function) pluginSettings.callback(this, 'deleted');

				// Check Max Input
				if(pluginSettings.maxInput <= 1 || clones().length === pluginSettings.maxInput - 1) {
					clones().last().next().replaceWith($('<button/>', {
						type: 'button',
						html: $(pluginSettings.addIcon)
					}).addClass(['ai-button-add'].concat(pluginSettings.buttonClasses)));
				}
			});

			// Handle Callback
			if(pluginSettings.callback instanceof Function) pluginSettings.callback(this, 'loaded');
		});
	};
})(jQuery);