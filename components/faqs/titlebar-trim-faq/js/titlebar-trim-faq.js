document.addEventListener('DOMContentLoaded', function() {
	// Delegate Click Functionality
	$(this.body).on('click', '.titlebar-trim-faq [data-component-action]', function(event) {
		// Prevent Default
		event.preventDefault();

		// Variable Defaults
		var payload  = $(this).data('component') || $(this).parents('[data-component]').data('component');
		var action   = $(this).data('component-action');
		var endpoint = $(this).closest('[data-endpoint]').data('endpoint');

		// Switch Action
		switch(action) {
			case 'modal':
				// Render Modal
				$.ajax(endpoint, {
					data: payload,
					dataType: 'html',
					method: 'post',
					async: false,
					beforeSend: showLoader,
					complete: hideLoader,
					success: function(modal) {
						// Init Modal
						$(modal).on('hidden.bs.modal', function() {
							$(this).remove();
						}).modal();
					}
				});
				break;
			default:
				console.error('Unknown Component Action', action);
		}
	});
});