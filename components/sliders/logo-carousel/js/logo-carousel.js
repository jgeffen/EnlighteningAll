document.addEventListener('DOMContentLoaded', function() {
	$('.logo-carousel-wrap').each(function() {
		var carousel = $(this).find('.logo-carousel');
		var next     = $(this).find('.swiper-button-next');
		var prev     = $(this).find('.swiper-button-prev');
		var that     = $(this);

		new Swiper(carousel[0], {
			slidesPerView: 3,
			centerInsufficientSlides: true,
			spaceBetween: 20,
			loop: true,
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
					slidesPerView: 4
				},
				975: {
					slidesPerView: 5
				},
				1250: {
					slidesPerView: 8
				},
				1650: {
					slidesPerView: 9
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

	$('.logo-carousel .swiper-slide').attr('aria-hidden', 'true');
});
