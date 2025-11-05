document.addEventListener('DOMContentLoaded', function() {
	// Variable Defaults
	var mainCSS = $('link[href^="/css/styles-main.min.css"]');

	// Init Scripts
	$.when(
		// Load Styles
		$('<link>').attr({ type: 'text/css', rel: 'stylesheet', href: '/library/packages/fullcalendar/main.min.css' }).insertBefore(mainCSS),
		$('<link>').attr({ type: 'text/css', rel: 'stylesheet', href: '/components/calendars/fullcalendar/css/fullcalendar.css' }).insertBefore(mainCSS),

		// Load Scripts
		$.ajax('/library/packages/fullcalendar/main.min.js', { async: false, dataType: 'script' }),
		$.Deferred(function(deferred) {
			$(deferred.resolve);
		})
	).then(function() {
		// Init Event Calendar
		(function(eventObject) {
			(new FullCalendar.Calendar(eventObject.calendar[0], {
				initialView: eventObject.getView(),
				themeSystem: 'bootstrap',
				events: {
					url: eventObject.calendar.data('endpoint'),
					method: 'post',
					extraParams: { category: eventObject.calendar.data('category') }
				},
				showNonCurrentDates: true,
				aspectRatio: 1.8,
				contentHeight: null,
				windowResizeDelay: 100,
				windowResize: function(view) {
					// Set View
					if(eventObject.getView() !== view.type) {
						this.changeView(eventObject.getView());
					}

					// Set Height/Aspect Ratio
					switch(true) {
						case (eventObject.getWindowWidth() <= 991):
							eventObject.calendar.find('.fc-view-harness').height($('.fc-list-table').outerHeight(true));
							break;
						default:
					}
				},
				eventClick: function(info) {
					// Prevent Default
					info.jsEvent.preventDefault();

					// Display Using Modal
					$(info.event._def.extendedProps.modal).on('hidden.bs.modal', function() {
						$(this).remove();
					}).modal();
				},
				loading: function() {
					// Fix for Mobile Devices
					eventObject.calendar.find('.fc-view-harness').height($('.fc-list-table').outerHeight(true));
				}
			})).render();
		})({
			calendar: $('#event-calendar'),
			getWindowWidth: function() {
				return window.innerWidth;
			},
			getView: function() {
				return (this.getWindowWidth() <= 991) ? 'listMonth' : 'dayGridMonth';
			}
		});
	});
});