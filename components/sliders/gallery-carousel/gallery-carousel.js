document.addEventListener('DOMContentLoaded', function() {
	$('.gallery-carousel-wrap').each(function() {
		var next      = $(this).find('.swiper-button-next');
		var prev      = $(this).find('.swiper-button-prev');
		var carousel  = $(this);
		var allSlides = carousel.find('.swiper-slide');
		var allLinks  = carousel.find('.swiper-slide a');

		var galleryThumbs = new Swiper($(this).find('.gallery-thumbs')[0], {
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

		var galleryTop = new Swiper($(this).find('.gallery-top')[0], {
			speed: 900,
			longSwipesRatio: .15,
			threshold: 5,
			loop: true,
			grabCursor: true,
			watchSlidesProgress: true,
			watchSlidesVisibility: true,
			navigation: {
				nextEl: next[0],
				prevEl: prev[0]
			},
			thumbs: {
				swiper: galleryThumbs
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

		container_attr(allSlides, 'hide');
		links_attr(allLinks, 'hide');
		container_attr(carousel.find('.swiper-slide-visible'), 'show');
		links_attr(carousel.find('.swiper-slide-visible a'), 'show');
	});
});