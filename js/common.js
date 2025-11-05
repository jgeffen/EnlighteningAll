/*================================================*/
/*  ============= Global Functions ============== */
/*================================================*/

//Utility to console log current element in focus, uncomment to use:
/*
 document.addEventListener('focusin', function() {
 console.log('focused: ', document.activeElement)
 }, true);
 */

/**||--------------------------------------------------------||**
 **||					<- ADA Functions ->					 ||**
 **||--------------------------------------------------------||**/
// Listen for the click and/or space bar/enter keypress event and return true or false
function a11yClick(event) {
	if(event.type === 'click') {
		return true;
	} else if(event.type === 'keypress') {
		var code = event.charCode || event.keyCode;
		if((code === 32) || (code === 13)) {
			return true;
		}
	} else {
		return false;
	}
}

//functions
/*//////////==== add hidden or shown attributes to link(s) ====//////////*/
function links_attr(el, state) {
	var $aria_hidden = (state === 'show' ? 'false' : 'true');
	var $tabindex    = (state === 'show' ? '0' : '-1');
	el.attr({
		'aria-hidden': $aria_hidden,
		'tabindex': $tabindex
	});
}

/*//////////==== add hidden or shown attributes to link(s) ====//////////*/
function container_attr(el, state) {
	var $aria_hidden = (state === 'show' ? 'false' : 'true');
	var $tabindex    = (state === 'show' ? '' : '-1');
	el.attr({
		'aria-hidden': $aria_hidden,
		'tabindex': $tabindex
	});
}

/*//////////==== add tab index to link(s) ====//////////*/
function tab_index(el, state) {
	var $tabindex = (state === 'show' ? '0' : '-1');
	el.attr({
		'tabindex': $tabindex
	});
}

/**||--------------------------------------------------------||**
 **||				<- Lazy Load Images ->					 ||**
 **||--------------------------------------------------------||**/
if(typeof Layzr === 'function') {
	// Init Layzr
	var layzrInstance = Layzr({
		normal: 'data-src',
		retina: 'data-retina',
		srcset: 'data-srcset',
		threshold: 50
	});

	// Add Callbacks
	layzrInstance.on('src:before', function(image) {
		// Check for Empty Source
		image.dataset.src = image.dataset.src || image.src;
	});

	// Start When DOM is Ready
	document.addEventListener('DOMContentLoaded', function() {
		layzrInstance.update().check().handlers(true);
	});
} else {
	$(window).on('load', function() {
		// Reducing the amount of images loaded by decreasing measured distance from bottom of screen
		var initialLoad = 400;

		//6.929931640625ms to acquire initial images
		setTimeout(function() {
			swapLazyImages($('.lazy'), initialLoad);
		}, 1000);

		//ON SCROLL LOAD IMAGES
		window.addEventListener('scroll', function() {
			swapLazyImages($('.lazy'), initialLoad);
		}, { passive: true });

		function swapLazyImages(lazyImages, initialLoad) {
			for(var i = 0; i < lazyImages.length; i++) {
				if(lazyImages[i].getBoundingClientRect().top - window.innerHeight < initialLoad) {
					lazyImages[i].src = lazyImages[i].dataset.src;
					lazyImages[i].classList.remove('lazy');
				}
			}
		}
	});
}

/**
 * Destroys Modal
 */
function destroyModal() {
	$(this).remove();
}

/*
 * Displays Message in Modal
 *
 * @param {string}	 text
 * @param {string}	 severity "alert" | "success"
 * @param {Function} [callbackFunction]
 */
function displayMessage(text, severity, callbackFunction) {
	// Check Open Modal
	if($('.modal.in, .modal.show').length) {
		alert(text);
	} else {
		// Reset Severity
		severity = (function() {
			switch(severity) {
				case 'alert':
				case 'error':
				case 'info':
				case 'success':
				case 'warning':
					return severity;
				default:
					return 'default';
			}
		})();

		// Handle Ajax
		$.ajax('/modals/notifications/' + severity, {
			data: { text: text },
			dataType: 'html',
			method: 'post',
			async: true,
			success: function(html) {
				$(html).on('shown.bs.modal', function() {
					if(typeof callbackFunction === 'function') {
						callbackFunction($(this));
					}
				}).on('hidden.bs.modal', destroyModal).modal();
			}
		});
	}
}

/**||------------------------------------------------------------||**
 **||		<- Focus Message Modal over Exiting Modal ->		 ||**
 **||------------------------------------------------------------||**/
function focusDisplayMessage() {
	if($('.modal-backdrop').length >= 2) {
		$('.modal-backdrop:last').css('z-index', 9901);
		$('.modal:last').css('z-index', 9902);
	}
}

/**
 * Show Loading Animation
 *
 * @returns {void}
 */
function showLoader() {
	$('.loading-anim').removeClass('loaded');
	$('.preloader').removeClass('loaded');
}

/**
 * Hide Loading Animation
 *
 * @returns {void}
 */
function hideLoader() {
	$('.loading-anim').addClass('loaded');
	$('.preloader').addClass('loaded');
}

/**||==========================================================||**
 **|| <----------------    Filter by Data   -----------------> ||**
 **||==========================================================||**/
$.fn.filterByData = function(prop, val) {
	return this.filter(function() {
		return $(this).data(prop) === val;
	});
};

/**||------------------------------------------------------------------------||**
 **||		<- Extend jQuery to Serialize Object from Forms ->				 ||**
 **||------------------------------------------------------------------------||**/
$.fn.serializeObject = function() {
	var self          = this;
	var json          = {};
	var push_counters = {};
	var patterns      = {
		'validate': /^[a-zA-Z][a-zA-Z0-9_\-]*(?:\[(?:\d*|[a-zA-Z0-9_\-]+)])*$/,
		'key': /[a-zA-Z0-9_\-]+|(?=\[])/g,
		'push': /^$/,
		'fixed': /^\d+$/,
		'named': /^[a-zA-Z0-9_\-]+$/
	};

	this.build = function(base, key, value) {
		base[key] = value;
		return base;
	};

	this.push_counter = function(key) {
		if(push_counters[key] === undefined) {
			push_counters[key] = 0;
		}
		return push_counters[key]++;
	};

	$.each($(this).serializeArray(), function() {
		// Skip invalid keys
		if(!patterns.validate.test(this.name)) {
			return;
		}

		var k;
		var keys        = this.name.match(patterns.key);
		var merge       = this.value;
		var reverse_key = this.name;

		while((k = keys.pop()) !== undefined) {
			// Adjust reverse_key
			reverse_key = reverse_key.replace(new RegExp('\\[' + k + '\\]$'), '');

			if(k.match(patterns.push)) {
				merge = self.build([], self.push_counter(reverse_key), merge);
			} else if(k.match(patterns.named)) {
				merge = self.build({}, k, merge);
			} else if(k.match(patterns.fixed)) {
				merge = self.build([], k, merge);
			}
		}

		json = $.extend(true, json, merge);
	});

	return json;
};

/**||==========================================================||**
 **|| <-------------- Document Ready Section ----------------> ||**
 **||==========================================================||**/
$(function() {
	/**||--------------------------------------------------------||**
	 **||			<- Bind Data for Select Boxes ->		   	 ||**
	 **||--------------------------------------------------------||**/
	$('body').on('change', '.select-wrap', function() {
		// Set Option
		var option = $(this).find('option:selected').text();

		// Update HTML
		$(this).find('.select-box').html(option);
	}).find('select[data-value]').each(function() {
		// Check Value
		if($(this).find('option[value="' + this.dataset.value + '"]').length) {
			// Set Value
			$(this).val(this.dataset.value);
		}
	}).end().find('select[data-values]').each(function() {
		// Set Values
		$(this).val(JSON.parse(this.dataset.values));
	}).end().find('.select-wrap').trigger('change');

	/**||--------------------------------------------------------||**
	 **||					<- Checkbox Script ->				 ||**
	 **||--------------------------------------------------------||**/
	$('.check-btn').each(function() {
		$(this).on('click', function() {
			$(this).toggleClass('checked-btn');
		});

		if($(this).find('input').is(':checked')) {
			$(this).addClass('checked-btn');
		}
	});

	/**||------------------------------------------------------------||**
	 **||		<- Display Cookie Message in Alert Modal ->			 ||**
	 **||------------------------------------------------------------||**/
	(function(message) {
		// Check Message
		if(message.hasOwnProperty('message')) {
			displayMessage(message.text, message.severity, function() {
				Cookies.remove('message');
			});
		}
	})(JSON.parse(Cookies.get('message') || '[]'));

	/**||----------------------------------------||**
	 **||		<- jQuery Mask Plugin ->		 ||**
	 **||----------------------------------------||**/
	$.fn.mask && $(':input[data-format]').each(function() {
		// Variable Default
		var type = $(this).data('format');

		// Switch Type
		switch(type) {
			case 'date':
				$(this).mask('00/00/0000');
				break;
			case 'mm/yy':
				$(this).mask('00/00', { placeholder: 'mm/yy' });
				break;
			case 'mm/yyyy':
				$(this).mask('00/0000', { placeholder: 'mm/yyyy' });
				break;
			case 'fax':
			case 'phone':
				$(this).mask('(000) 000-0000');
				break;
			case 'postal':
			case 'zip':
				$(this).mask('00000');
				break;
			case 'number':
				$(this).mask('000,000,000,000,000.00', { reverse: true });
				break;
			default:
				console.error('Unknown Mask Type:', type);
		}
	});

	/**||----------------------------------------||**
	 **||			<- MultiSelect ->			 ||**
	 **||----------------------------------------||**/
	$.fn.multiSelect && $('select[multiple]:not(.custom)').multiSelect({
		selectableHeader: '<div class="text-center"><b>Inactive</b></div>',
		selectionHeader: '<div class="text-center"><b>Active</b></div>',
		selectableFooter: '<button class="btn btn-block btn-primary btn-select-all" type="button">Select All</button>',
		selectionFooter: '<button class="btn btn-block btn-primary btn-deselect-all" type="button">Deselect All</button>',
		afterInit: function(multiSelect) {
			/* Variable Defaults */
			var element = this.$element;
			var wrapper = $('<div class="col-xl-6"/>');

			// 	'$container': 'div.ms-container',
			// 	'$element': 'select',
			// 	'$selectableContainer': 'div.ms-selectable',
			// 	'$selectionContainer': 'div.ms-selection',
			// 	'$selectableUl': 'div.ms-selectable ul.ms-list',
			// 	'$selectionUl': 'div.ms-selection ul.ms-list'

			// Custom Classes
			this.$container.addClass('row w-auto');
			this.$selectableContainer.addClass('w-100 px-xl-3 mb-4 mb-xl-0').wrap(wrapper);
			this.$selectionContainer.addClass('w-100 px-xl-3 mt-4 mt-xl-0').wrap(wrapper);

			/* Select/Deselect All */
			multiSelect.on('click', '.btn-select-all, .btn-deselect-all', function() {
				element.multiSelect($(this).hasClass('btn-select-all') ? 'select_all' : 'deselect_all');
			});
		}
	});

	/**||---------------------------------------------------||**
	 **||				<- Init Settings ->		     		||**
	 **||---------------------------------------------------||**/
	$.ajax('/ajax/settings', {
		method: 'post',
		dataType: 'json',
		cache: false,
		async: false,
		success: function(response) {
			/**
			 *
			 * @param {Object} response
			 * @param {Object} response.settings
			 * @param {Object} response.settings.maxFilesize
			 * @param {Number} response.settings.maxFilesize.B
			 * @param {Number} response.settings.maxFilesize.KB
			 * @param {Number} response.settings.maxFilesize.MB
			 * @param {Number} response.settings.maxFilesize.GB
			 *
			 */

			// Settings
			window.settings = response.settings;
		}
	});

	/*||--------------------------------------------------------||**
	 **||				<- Waypoint Script ->					||**
	 **||--------------------------------------------------------||*/
	// Add the .waypoint class to target elements, then assign css styles for an .active class.   
	// Add a data tag(example: data-thresh="50") to move the position of where the waypoint is triggered:
	// The Data Thresh Value is what percentage of the object you would like to be visible before it is activated, keep in mind css transforms could skew the measurements.
	// If you don't add a Data Tag it will use the default value of 80
	$(window).on('load scroll', function() {
		$('.waypoint').each(function() {
			var thresh     = typeof $(this).data('thresh') !== 'undefined' ? $(this).data('thresh') : 80,
				threshPerc = $(this).outerHeight() * (parseInt(thresh) / 100),
				winHeight  = $(window).height(),
				wayPoint   = $(this).offset().top - winHeight + threshPerc,
				scrollPos  = $(window).scrollTop();
			if(scrollPos > wayPoint) {
				$(this).addClass('active');
			} else if(scrollPos < wayPoint) {
				$(this).removeClass('active');
			}
		});
	});

	/*||--------------------------------------------------------||**
	 **||				<- YouTube Modal Script ->				||**
	 **||--------------------------------------------------------||*/
	/* Bind Click Event to Video Thumbnail */
	$('[data-youtube-id]').on('click', function(event) {
		/* Prevent Default */
		event.preventDefault();

		/* Variable Defaults */
		var youtubeId    = $(this).data('youtube-id');
		var youtubeTitle = $(this).prop('title');

		/* Populate/Trigger Modal */
		$('#youtube-modal').find('.modal-body').html(
			$('<div class="embed-responsive embed-responsive-16by9">').append(
				$('<iframe/>', {
					'allowfullscreen': true,
					'title': youtubeTitle,
					'class': 'embed-responsive-item',
					'src': '//www.youtube.com/embed/' + youtubeId + '?autoplay=1&rel=0'
				}).one('load', function() {
					// $('#youtube-modal').modal(); // Using data-* API instead to workaround reviews widget
					$(window).trigger('resize');
				})
			)
		).end().find('.modal-title').text(youtubeTitle).end();
	}).attr({
		'data-toggle': 'modal',
		'data-target': '#youtube-modal'
	});

	/* Remove Modal Data on Close */
	$('#youtube-modal').on('hide.bs.modal', function() {
		var that = $(this);
		setTimeout(function() {
			$(that).find('.modal-body, .modal-title').empty();
		}, 300);
	});

	// Set default thumb if video thumb is missing
	$('.video-wrap[data-youtube-id]').each(function() {
		// Variable Defaults
		var parent     = $(this);
		var thumb      = parent.find('img');
		var youtube_id = parent.data('youtube-id');

		// Fetch List of Thumbnails
		$.get('https://www.googleapis.com/youtube/v3/videos', {
			id: youtube_id,
			key: 'AIzaSyCmOeKqKRNBhKHtXvFirWjXSyAE9G5aNcw',
			part: 'snippet',
			fields: 'items/snippet/thumbnails'
		}, function(response) {
			if(response.hasOwnProperty('items') && response.items.length) {
				thumb.attr('src', Object.keys(response.items[0].snippet.thumbnails).map(function(key) {
					return response.items[0].snippet.thumbnails[key];
				}).reduce(function(a, b) {
					return a.width > b.width && a.width <= 600 ? a.url : b.url;
				}));
			}
		});
	});

	/* HTML EXAMPLE */
	/*
	 <a href="javascript:void(0)" class="video-wrap" title="Sample Video Title" rel="video" data-youtube-id="Bey4XXJAqS8">
	 <div class="img-wrap">
	 <img class="img-fluid" src="/images/layout/default-landscape.jpg" alt="Sample Video Title" />
	 </div>
	 </a>
	 */

	/*||--------------------------------------------------------||**
	 **||			<- Check for Render Error ->				||**
	 **||--------------------------------------------------------||*/
	(function() {
		// Variable Defaults
		var error   = $('<div/>', { id: 'render-error' }).prependTo('body');
		var message = error.css('content');

		// Check Message
		message.replace('normal', '').length && console.error('You have a render error: ' + message);

		// Remove Error
		error.remove();
	})();

	/**||-------------------------------------------||**
	 **||			<- Init Fancybox ->				||**
	 **||-------------------------------------------||**/
	(function() {
		// Fancybox Defaults
		$.extend(true, $.fancybox.defaults, {
			spinnerTpl: '<div class="d-flex align-items-center justify-content-center h-100"><i class="fa-solid fa-gear fa-spin fa-5x"></i></div>',
			buttons: [
				'zoom',
				'slideShow',
				'fullScreen',
				'thumbs',
				'close'
			],
			btnTpl: {
				zoom:
					'<button data-fancybox-zoom class="fancybox-button fancybox-button--zoom" title="{{ZOOM}}">' +
					'<i class="fa-solid fa-magnifying-glass-plus"></i>' +
					'</button>',
				close:
					'<button data-fancybox-close class="fancybox-button fancybox-button--close" title="{{CLOSE}}">' +
					'<i class="fa-solid fa-xmark-large"></i>' +
					'</button>',
				arrowLeft:
					'<button data-fancybox-prev class="fancybox-button fancybox-button--arrow_left" title="{{PREV}}">' +
					'<div><i class="fa-light fa-chevron-left"></i></div>' +
					'</button>',
				arrowRight:
					'<button data-fancybox-next class="fancybox-button fancybox-button--arrow_right" title="{{NEXT}}">' +
					'<div><i class="fa-light fa-chevron-right"></i></div>' +
					'</button>',
				smallBtn:
					'<button type="button" data-fancybox-close class="fancybox-button fancybox-close-small" title="{{CLOSE}}">' +
					'<i class="fa-solid fa-xmark"></i>' +
					'</button>',
				thumbs:
					'<button data-fancybox-thumbs class="fancybox-button fancybox-button--thumbs" title="{{THUMBS}}">' +
					'<i class="fa-solid fa-gallery-thumbnails"></i>' +
					'</button>'
			},
			thumbs: {
				autoStart: true,
				axis: 'x'
			},
			lang: 'en',
			i18n: {
				en: {
					CLOSE: 'Close',
					NEXT: 'Next',
					PREV: 'Previous',
					ERROR: 'The requested content cannot be loaded.<br>Please try again later.',
					PLAY_START: 'Start Slideshow',
					PLAY_STOP: 'Pause Slideshow',
					FULL_SCREEN: 'Full Screen',
					THUMBS: 'Thumbnails',
					DOWNLOAD: 'Download',
					SHARE: 'Share',
					ZOOM: 'Zoom'
				}
			}
		});

		// Delegate Click Event to Parent
		$('.lightbox').on('click', 'a:not([data-youtube-id])', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var selected      = $(this);
			var selectedIndex = 0;

			// Show Fancybox
			$.fancybox.open($(event.delegateTarget).find('a').map(function(index, element) {
				var $trigger = $(element);
				var $thumb   = $trigger.find('img');
				var videoId  = $trigger.data('youtube-id');

				if($trigger.is(selected)) selectedIndex = index;

				// Return Object: Check if it's a YouTube video or an image
				return {
					src: videoId ? 'https://www.youtube.com/embed/' + videoId + '?autoplay=1' : $trigger.attr('href'),
					type: videoId ? 'iframe' : 'image',
					opts: {
						thumb: $thumb.data('src') || $thumb.attr('src'),
						caption: $thumb.attr('alt'),
						iframe: {
							css: {
								maxWidth: '800px',
								width: '100%'
							}
						}
					}
				};
			}).toArray(), {}, selectedIndex);
		});
	})();

	/**||-----------------------------------------------------------||**
	 **||			<- Extend jQuery Deferred Hook ->				||**
	 **||-----------------------------------------------------------||**/
	$.Deferred.exceptionHook = function(error, stack) {
		if(error && /^(Eval|Internal|Range|Reference|Syntax|Type|URI)Error$/.test(error.name)) {
			window.console.error(
				'jQuery.Deferred exception: ' + error.message,
				error.stack,
				stack
			);
		}
	};
});

/*||==========================================================||**
 **|| <---------------- Window Load Section -----------------> ||**
 **||==========================================================||*/

$(window).on('load', function() {
	/*||--------------------------------------------------------||**
	 **||				<- End Loading Animation ->				||**
	 **||--------------------------------------------------------||*/
	window.hideLoader();

	/*||--------------------------------------------------------||**
	 **||			<- Animate Scroll to Anchor ->				||**
	 **||--------------------------------------------------------||*/
	$('a[href*="#"]:not([href="#"])').on('click', function() {
		if(location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
			var target = $(this.hash);
			target     = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
			if(target.length) {
				$('html,body').animate({
					scrollTop: target.offset().top - 100
				}, 1000);
				return false;
			}
		}
	});
});
