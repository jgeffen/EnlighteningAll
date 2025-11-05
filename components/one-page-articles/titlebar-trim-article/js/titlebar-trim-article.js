document.addEventListener('DOMContentLoaded', function() {
	// Delegate Click Functionality
	$(this.body).on('click', '.titlebar-trim-article [data-component-action]', function(event) {
		// Prevent Default
		event.preventDefault();

		// Variable Defaults
		var payload = $(this).data('component') || $(this).parents('[data-component]').data('component');
		var action  = $(this).data('component-action');

		// Switch Action
		switch(action) {
			case 'modal':
				// Render Modal
				$.ajax('/components/one-page-articles/titlebar-trim-article/modals/titlebar-trim-article.php', {
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
					},
					error: function(xhr) {
						displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
					}
				});
				break;
			default:
				console.error('Unknown Component Action', action);
		}
	});
});