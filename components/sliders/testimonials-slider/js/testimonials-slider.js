document.addEventListener('DOMContentLoaded', function() {
	$('.testimonials-slider').each(function() {
		var slider = $(this);
		var next   = $(this).find('.swiper-button-next');
		var prev   = $(this).find('.swiper-button-prev');
		var page   = $(this).find('.swiper-pagination');
		var that   = $(this);

		new Swiper(slider[0], {
			slidesPerView: 1,
			spaceBetween: 0,
			loop: true,
			speed: 900,
			effect: 'slide',
			simulateTouch: false,
			autoHeight: false,
			autoplay: {
				delay: 7000,
				disableOnInteration: false
			},
			navigation: {
				nextEl: next[0],
				prevEl: prev[0]
			},
			pagination: {
				el: page,
				type: 'fraction',
				clickable: true
			},
			breakpoints: {
				715: {
					pagination: {
						type: 'bullets'
					}
				}
			},
			on: {
				slideChangeTransitionStart: function() {
					that.find('.swiper-slide').attr('aria-hidden', 'true');
					that.find('a').attr('tabindex', '-1').attr('aria-hidden', 'true');
				},
				slideChangeTransitionEnd: function() {
					setTimeout(function() {
						that.find('.swiper-slide-active').attr('aria-hidden', 'false');
						that.find('.swiper-slide-active a').attr('tabindex', '0').attr('aria-hidden', 'false');
					}, 2000);
				}
			}
		});
	});

	$('.info-slider .swiper-slide').attr('aria-hidden', 'true');
});