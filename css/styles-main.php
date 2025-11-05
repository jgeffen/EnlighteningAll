<?php
	// Set Include Path
	set_include_path(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'));
	
	// Required Files
	require('library/vendor/autoload.php');
	use ScssPhp\ScssPhp\Exception\SassException;
	
	// Variable Defaults
	$debug    = FALSE;
	$filename = sprintf("/css/%s.min.css", pathinfo(__FILE__, PATHINFO_FILENAME));
	
	try {
		// Minify Styles
		Render::Minify($filename, array(
			/* FONT IMPORTS */
			'/css/imports.css',
			
			/* INSTALLED THROUGH YARN */
			'/library/packages/bootstrap/dist/css/bootstrap.min.css',
			'/library/packages/@fortawesome/fontawesome-pro/css/all.min.css',
			'/library/packages/swiper/swiper-bundle.min.css',
			'/library/packages/@fancyapps/fancybox/dist/jquery.fancybox.min.css',
			'/library/packages/select2/dist/css/select2.min.css',
			'/library/packages/multiselect/css/multi-select.css',
			
			/* INSTALLED MANUALLY */
			'/js/photobox/photobox.css',
			
			/* CUSTOM PLUGINS */
			'/css/plugins/bootstrap.scss',
			'/css/plugins/tinymce.scss',
			'/css/plugins/ktt10.scss',
			
			/* COMMON STYLESHEET */
			// '/css/common.scss',
			
			/* MAIN STYLESHEET */
			'/css/main-stylesheet.scss',
			
			/* MEMBERS STYLESHEET */
			'/library/members/css/members-stylesheet.scss',
			
			/* STYLE SHEETS FOR SITE COMPONENTS */
			'/components/alert-bar/alert-bar-standard/css/alert-bar-standard.scss',
			'/components/sliders/info-slider/css/info-slider.scss',
			'/components/sliders/logo-carousel/css/logo-carousel.scss',
			'/components/sliders/gallery-carousel/css/gallery-carousel.scss',
			'/components/sliders/room-carousel/css/room-carousel.scss',
			'/components/sliders/testimonials-slider/css/testimonials-slider.scss',
			'/components/info-buttons/icon-columns/css/icon-columns.scss',
			'/components/info-buttons/icon-photo-boxes/css/icon-photo-boxes.scss',
			'/components/breakouts/content-breakout/css/content-breakout.scss',
			'/components/recent-articles/tall-photo-articles/css/tall-photo-articles.scss',
			'/components/recent-articles/category-list/css/category-list.scss',
			'/components/recent-articles/multi-section-list/css/multi-section-list.scss',
			'/components/recent-articles/article-boxes-3up/css/article-boxes-3up.scss',
			'/components/categories/image-title-category/css/image-title-category.scss',
			'/components/articles/image-card-article/css/image-card-article.scss',
			'/components/articles/circle-articles/css/circle-articles.scss',
			'/components/articles/full-width-article/css/full-width-article.scss',
			'/components/one-page-articles/titlebar-trim-article/css/titlebar-trim-article.scss',
			'/components/faqs/titlebar-trim-faq/css/titlebar-trim-faq.scss',
			
			/* STYLE SHEETS FOR SITE STRUCTURES */
			'/library/structures/header/header-membership/css/header-membership.scss',
			'/css/nav-desktop.scss',
			'/css/nav-mobile.scss',
			'/library/structures/footer/navigation-footer-secrets/css/navigation-footer.scss'
		), filemtime(__FILE__), array(
			'/css/_variables.scss',
			'/css/common.scss'
		), $debug);
		
		// Serve JavaScript
		Render::Asset($filename);
	} catch(Exception|SassException $exception) {
		error_log($exception->getMessage());
	}