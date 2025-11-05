document.addEventListener('DOMContentLoaded', function() {
	var infoSlider = new Swiper('.info-slider', {
		slidesPerView: 1,
		spaceBetween: 0,
		loop: false,
		speed: 900,
		effect: 'fade',
		fadeEffect: {
			crossFade: true
		},
		simulateTouch: false,
		autoHeight: true,
		autoplay: {
			delay: 7000,
			disableOnInteration: false
		},
		pagination: {
			el: '.swiper-pagination',
			type: 'bullets',
			clickable: true
		},
		on: {
			slideChangeTransitionStart: function() {
				$('.info-slider .swiper-slide').attr('aria-hidden', 'true');
				$('.info-slider a').attr('tabindex', '-1').attr('aria-hidden', 'true');
			},
			slideChangeTransitionEnd: function() {
				setTimeout(function() {
					$('.info-slider .swiper-slide-active').attr('aria-hidden', 'false');
					$('.info-slider .swiper-slide-active a').attr('tabindex', '0').attr('aria-hidden', 'false');
				}, 2000);
			}
		}
	});

	$('.info-slider .swiper-slide').attr('aria-hidden', 'true');
});

//Apply the slides-ready class to start the animation after window load
$(window).on('load', function() {
	setTimeout(function() {
		$('.info-slider').addClass('slides-ready');
	}, 200);
});