<?php
// Set Include Path
set_include_path(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'));

// Required Files
require('library/vendor/autoload.php');

use ScssPhp\ScssPhp\Exception\SassException;

// Variable Defaults
$debug    = FALSE;
$filename = sprintf("/js/%s.min.js", pathinfo(__FILE__, PATHINFO_FILENAME));

try {
	// Minify JavaScript
	Render::Minify($filename, array(
		// Polyfill for Older Browser Support
		'/js/polyfill.js',

		// Installed via Yarn
		'/library/packages/jquery/dist/jquery.min.js',
		'/library/packages/popper.js/dist/umd/popper.min.js',
		'/library/packages/bootstrap/dist/js/bootstrap.min.js',
		'/library/packages/@fancyapps/fancybox/dist/jquery.fancybox.min.js',
		'/library/packages/css-element-queries/src/ResizeSensor.js',
		'/library/packages/jquery-mask-plugin/dist/jquery.mask.min.js',
		'/library/packages/jquery-match-height/dist/jquery.matchHeight-min.js',
		'/library/packages/js-cookie/dist/js.cookie.js',
		'/library/packages/layzr.js/dist/layzr.js',
		'/library/packages/sticky-sidebar/dist/sticky-sidebar.min.js',
		'/library/packages/swiper/swiper-bundle.min.js',
		'/library/packages/select2/dist/js/select2.full.min.js',
		'/library/packages/multiselect/js/jquery.multi-select.js',
		'/library/packages/@zxing/library/umd/index.js',

		// Configuration Files
		'/js/jquery.ajax.config.js',

		// Installed Manually
		'/js/jquery.fitvids.js',
		'/js/youtube.js',
		'/js/photobox/jquery.photobox.js',
		'/js/paraxify.min.js',

		// Main Scripts
		'/js/common.js',
		'/js/site-scripts.js',

		// Member Scripts
		'/library/members/js/members-scripts.js',

		// Component Scripts
		'/components/sliders/info-slider/js/info-slider.js',
		'/components/sliders/logo-carousel/js/logo-carousel.js',
		'/components/sliders/gallery-carousel/gallery-carousel.js',
		'/components/sliders/room-carousel/room-carousel.js',
		'/components/sliders/testimonials-slider/js/testimonials-slider.js',
		'/components/one-page-articles/titlebar-trim-article/js/titlebar-trim-article.js',
		'/components/faqs/titlebar-trim-faq/js/titlebar-trim-faq.js',

		// Structure Scripts
		'/js/nav-desktop.js',
		'/js/nav-mobile.js',
	), filemtime(__FILE__), array(), $debug);

	// Serve JavaScript
	Render::Asset($filename);
} catch (Exception | SassException $exception) {
	error_log($exception->getMessage());
}
