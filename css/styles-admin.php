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
			'/app/admin/css/imports.css',
			
			/* INSTALLED THROUGH YARN */
			'/library/packages/bootstrap/dist/css/bootstrap.min.css',
			'/library/packages/@fancyapps/fancybox/dist/jquery.fancybox.min.css',
			'/library/packages/@fortawesome/fontawesome-pro/css/all.min.css',
			'/library/packages/cropperjs/dist/cropper.min.css',
			'/library/packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
			'/library/packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css',
			'/library/packages/datatables.net-rowreorder-bs4/css/rowReorder.bootstrap4.min.css',
			'/library/packages/datatables.net-searchpanes-bs4/css/searchPanes.bootstrap4.min.css',
			'/library/packages/datatables.net-select-bs4/css/select.bootstrap4.min.css',
			'/library/packages/dropzone/dist/min/basic.min.css',
			'/library/packages/dropzone/dist/min/dropzone.min.css',
			'/library/packages/flatpickr/dist/flatpickr.min.css',
			'/library/packages/flatpickr/dist/plugins/confirmDate/confirmDate.css',
			'/library/packages/multiselect/css/multi-select.css',
			
			/* INSTALLED MANUALLY */
			'/js/quickdeploy/cropper/jquery.cropper.css',
			'/js/quickdeploy/uploader/jquery.uploader.css',
			
			/* CUSTOM PLUGINS */
			'/css/plugins/bootstrap.scss',
			'/css/plugins/tinymce.scss',
			
			/* COMMON STYLESHEET */
			// '/css/common.scss',
			
			/* MEMBERS STYLESHEET */
			'/library/members/css/members-stylesheet.scss',
			
			/* MAIN STYLESHEET */
			'/app/admin/css/admin-stylesheet.scss'
		), filemtime(__FILE__), array(
			'/app/admin/css/_nav.scss',
			'/app/admin/css/_variables.scss',
			'/css/common.scss'
		), $debug);
		
		// Serve JavaScript
		Render::Asset($filename);
	} catch(Exception|SassException $exception) {
		error_log($exception->getMessage());
	}