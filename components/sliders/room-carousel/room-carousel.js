document.addEventListener('DOMContentLoaded', function() {
	$('.room-carousel-wrap').each(function() {
		// Variable Defaults
		var carousel = $(this);
		var next     = carousel.find('.swiper-button-next');
		var prev     = carousel.find('.swiper-button-prev');
		var slides   = carousel.find('.swiper-slide');
		var links    = carousel.find('.swiper-slide a');

		// Set Thumbs Carousel
		var thumbs = new Swiper($(this).find('.gallery-thumbs')[0], {
			spaceBetween: 6,
			slidesPerView: 3,
			loop: true,
			speed: 900,
			threshold: 5,
			longSwipesRatio: .15,
			grabCursor: true,
			breakpoints: {
				450: {
					spaceBetween: 10
				},
				715: {
					slidesPerView: 4,
					spaceBetween: 10
				}
			},
			watchSlidesProgress: true,
			watchSlidesVisibility: true
		});

		// Set Images Carousel
		var images = new Swiper($(this).find('.gallery-top')[0], {
			speed: 900,
			longSwipesRatio: .15,
			threshold: 5,
			grabCursor: true,
			watchSlidesProgress: true,
			watchSlidesVisibility: true,
			navigation: {
				nextEl: next[0],
				prevEl: prev[0]
			},
			thumbs: {
				swiper: thumbs
			},
			on: {
				transitionEnd: function() {
					container_attr(carousel.find('.gallery-top .swiper-slide'), 'hide');
					links_attr(carousel.find('.gallery-top .swiper-slide a'), 'hide');
					container_attr(carousel.find('.swiper-slide-visible'), 'show');
					links_attr(carousel.find('.swiper-slide-visible a'), 'show');
				}
			}
		});

		container_attr(slides, 'hide');
		links_attr(links, 'hide');
		container_attr(carousel.find('.swiper-slide-visible'), 'show');
		links_attr(carousel.find('.swiper-slide-visible a'), 'show');
	});
});
