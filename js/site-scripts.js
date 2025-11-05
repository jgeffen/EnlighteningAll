// TODO: Refactor site-scripts.js

/*||==========================================================||**
 **|| <-------------- Document Ready Section ----------------> ||**
 **||==========================================================||*/

$(function() {
	/*================================================*/
	/*   ==== Responsive Youtube Video Resizing ====  */
	/*================================================*/
	$('.fitvid').fitVids();

	//////// !!!!!!!!!!!! This is out Oringal Email Script from QD to be merged !!!!!!!!!!!!!!
	/*||--------------------------------------------------------||**
	 **||	<- Insert href attribute with mailto:email link ->	||**
	 **||--------------------------------------------------------||*/
	$('.email-link').each(function() {
		var user = $(this).data('email-user') || siteEmailUser;
		var site = $(this).data('email-domain') || siteEmailDomain;
		var mail = user + '@' + site;
		$(this).attr('href', 'mailto:' + mail.toLowerCase());
		if(!$(this).hasClass('email-title')) {
			$(this).append(mail);
		}
	});
	/* 
	 Example for site email:
	 <a class="email-link"></a>

	 Example for custom email:
	 <a class="email-link" data-email-user="user" data-email-domain="domain.com"></a>

	 Example for email title:
	 <a class="email-link email-title">Email Us Today!</a>
	 */

	//////// !!!!!!!!!!!! This is the Encryptor Script from this project to be merged !!!!!!!!!!!!!!
	/* Handle Text to Links */
	$('span[data-link]').each(function() {
		var type = $(this).data('link') || null;
		var text = $(this).text();
		var link = null;
		switch(type) {
			case 'email':
				var user   = $(this).data('user') || 'info';
				var domain = $(this).data('domain') || 'google.com';
				link       = $('<a/>', { href: 'mailto:' + user + '@' + domain, class: 'text-truncate d-inline-block mw-100 align-bottom' }).text(text || user + '@' + domain);
				break;
			case 'phone':
				var phone = $(this).data('phone') || '555-555-5555';
				link      = $('<a/>', { href: 'tel:' + phone.replace(/\D/g, ''), class: 'nobr' }).text(text || phone);
				break;
			case 'website':
				var website = $(this).data('website') || 'https://www.google.com/';
				link        = $('<a/>', { href: website, target: '_blank', rel: 'nofollow', class: 'text-truncate d-inline-block mw-100 align-bottom' }).text(text || website);
				break;
			default:
				return false;
		}
		$(this).replaceWith(link);
	});

	/*||--------------------------------------------------------||**
	 **||				<- Parallax Initialization ->			||**
	 **||--------------------------------------------------------||*/
	//Run the parallax initialization if it's not mobile, IE or Edge
	if(!/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Trident.*rv[ :]*11\.|Edge|Opera Mini/i.test(navigator.userAgent)) {
		var myParaxify = paraxify('.parallax');

		//Trigger scroll on load to get paralax started on load
		setTimeout(function() {
			$(window).scrollTop($(window).scrollTop() + 1);
			$(window).scrollTop($(window).scrollTop() - 1);
		}, 200);
	}

	/*||------------------------------------------------------------||**
	 **||				<- Events Carousel Script ->				||**
	 **||------------------------------------------------------------||*/
	$('.events-carousel-wrap').each(function() {
		var carousel = $(this).find('.events-carousel');
		var next     = $(this).find('.swiper-button-next');
		var prev     = $(this).find('.swiper-button-prev');
		var that     = $(this);

		new Swiper(carousel[0], {
			slidesPerView: 1,
			centerInsufficientSlides: true,
			spaceBetween: 100,
			loop: false,
			speed: 900,
			centeredSliders: true,
			simulateTouch: false,
			longSwipesRatio: .15,
			grabCursor: true,
			autoplay: {
				delay: 2000,
				disableOnInteration: false
			},
			navigation: {
				nextEl: next[0],
				prevEl: prev[0]
			},
			breakpoints: {
				715: {
					slidesPerView: 2
				},
				975: {
					slidesPerView: 3
				},
				1250: {
					slidesPerView: 4
				},
				1550: {
					slidesPerView: 5
				}
			},
			watchSlidesProgress: true,
			watchSlidesVisibility: true,
			on: {
				slideChangeTransitionStart: function() {
					that.find('.swiper-slide').attr('aria-hidden', 'true');
					that.find('a').attr('tabindex', '-1').attr('aria-hidden', 'true');
					that.find('.swiper-slide-visible').attr('aria-hidden', 'false');
					that.find('.swiper-slide-visible a').attr('tabindex', '0').attr('aria-hidden', 'false');
				}
			}
		});
	});

	$('.events-carousel .swiper-slide').attr('aria-hidden', 'true');
});

/*||==========================================================||**
 **|| <---------------- Window Load Section -----------------> ||**
 **||==========================================================||*/

$(window).on('load', function() {

	/*||--------------------------------------------------------||**
	 **||					<- Equal Heights ->					||**
	 **||--------------------------------------------------------||*/
	var targetClasses = {};

	$('[class^="equal-"], [class*=" equal-"]').each(function() {
		var thisClass               = $(this).attr('class').split(' ').filter(function(className) {
			return !className.indexOf('equal-');
		});
		targetClasses[thisClass[0]] = true;
	});

	$.each(targetClasses, function(className) {
		$('.' + className).matchHeight();
	});

	//Trigger the Match Height Update on load
	setTimeout(function() {
		$.fn.matchHeight._update();
	}, 500);

	/*||--------------------------------------------------------||**
	 **||				<- Responsive Tables->					||**
	 **||--------------------------------------------------------||*/
	$('.resp-table [data-tabletitle], .resp-table-lg [data-tabletitle]').each(function() {
		var titleId      = $(this).data('tabletitle');
		var titleContent = $('#' + titleId).html();
		$(this).prepend('<span>' + titleContent + ':&nbsp;</span>');
	});

	/*||--------------------------------------------------------||**
	 **||		<- Margin Fixes for Trim Containers ->			||**
	 **||--------------------------------------------------------||*/
	$('p + .row, ul + .row, ol + .row').find('.trim, .title-bar-trim-combo').closest('.row').css('margin-top', '30px');
	$('.row > [class^="col"] > .trim, .row > [class^="col"] > .title-bar-trim-combo').closest('.row').find('+ .trim, + .title-bar-trim-combo').css('margin-top', '0');
});

// Self Analytics
$(function() {
	// Bind Click Event to Analytics
	$('[data-analytics-action]').on('click', function() {
		// Variable Defaults
		var item   = $(this);
		var banner = item.find('img').first();
		var data   = item.data();

		// Switch Action
		switch(data.analyticsAction) {
			case 'click':
				// Handle Ajax Request
				$.ajax('/ajax/analytics', {
					dataType: 'html',
					async: true,
					method: 'post',
					beforeSend: null,
					complete: null,
					data: {
						url: item.prop('href'),
						banner: banner.attr('src'),
						referer: location.href
					}
				});
				break;
			default:
				// Handle Error
				console.error('Unknown Action:', data.analyticsAction);
		}
	});
});

















