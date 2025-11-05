!function($) {
	'use strict';

	// Handle Request
	$.ajax({
		type: 'post',
		url: '/ajax/admin/tinymce/get-components',
		dataType: 'json',
		async: false,
		success: function(response) {
			// Switch Status
			switch(response.status) {
				case 'success':
					tinymce.PluginManager.add('components', function(editor, url) {
						$.each(response.components, function(index, element) {
							editor.ui.registry.addNestedMenuItem(index, {
								icon: element.icon,
								text: element.component,
								getSubmenuItems: function() {
									// Variable Defaults
									var menuItems = [];
									var count     = 0;

									$.each(element.items, function(index, element) {
										menuItems[count]          = element;
										menuItems[count].onAction = function() {
											editor.insertContent(element.value);
										};

										count++;
									});

									return menuItems;
								}
							});
						});
					});
					break;
				case 'error':
					displayMessage(response.message || Object.keys(response.errors).map(function(key) {
						return response.errors[key];
					}).join('<br>'), 'alert', null);
					break;
				default:
					displayMessage(response.message || 'Something went wrong.', 'alert');
			}
		}
	});
}(jQuery);