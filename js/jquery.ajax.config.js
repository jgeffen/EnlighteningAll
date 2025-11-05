document.addEventListener('DOMContentLoaded', function() {
	// Handle Ajax Errors
	$(document).ajaxError(function(event, xhr, settings, thrownError) {
		if(xhr.status > 0) {
			switch(xhr.status) {
				case 503:
					console.error(xhr.status + ': ' + xhr.statusText + ' (' + settings.url + ')');
					break;
				default:
					displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + settings.url + ')', 'alert');
			}
		}
	});

	// Configure Ajax
	$.ajaxSetup({
		dataFilter: function(data, type) {
			// Automatically Determine Type
			if(type == null) {
				try {
					data = JSON.parse(typeof data !== 'string' ? JSON.stringify(data) : data);
					if(typeof data === 'object' && data !== null) return data;
				} catch(exception) {
					// Nothing to do here
				}
			}

			return data;
		}
	});
});