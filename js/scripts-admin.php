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
			'/library/packages/datatables.net/js/jquery.dataTables.min.js',
			'/library/packages/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
			'/library/packages/datatables.net-responsive/js/dataTables.responsive.min.js',
			'/library/packages/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
			'/library/packages/datatables.net-rowreorder/js/dataTables.rowReorder.min.js',
			'/library/packages/datatables.net-rowreorder-bs4/js/rowReorder.bootstrap4.min.js',
			'/library/packages/datatables.net-searchpanes/js/dataTables.searchPanes.min.js',
			'/library/packages/datatables.net-searchpanes-bs4/js/searchPanes.bootstrap4.min.js',
			'/library/packages/datatables.net-select/js/dataTables.select.min.js',
			'/library/packages/datatables.net-select-bs4/js/select.bootstrap4.min.js',
			'/library/packages/@fancyapps/fancybox/dist/jquery.fancybox.min.js',
			'/library/packages/accounting/accounting.min.js',
			'/library/packages/chart.js/dist/chart.min.js',
			'/library/packages/cropperjs/dist/cropper.min.js',
			'/library/packages/dropzone/dist/min/dropzone.min.js',
			'/library/packages/flatpickr/dist/flatpickr.min.js',
			'/library/packages/flatpickr/dist/plugins/confirmDate/confirmDate.js',
			'/library/packages/jquery-match-height/dist/jquery.matchHeight-min.js',
			'/library/packages/jquery-mask-plugin/dist/jquery.mask.min.js',
			'/library/packages/layzr.js/dist/layzr.js',
			'/library/packages/multiselect/js/jquery.multi-select.js',
			'/library/packages/js-cookie/dist/js.cookie.js',
			'/library/packages/sortablejs/Sortable.min.js',
			'/library/packages/@zxing/library/umd/index.js',
			
			// Installed Manually
			'/js/quickdeploy/jquery.dependent.fields.min.js',
			'/js/quickdeploy/cropper/jquery.cropper.min.js',
			'/js/quickdeploy/uploader/jquery.uploader.min.js',
			
			// Configuration Files
			'/app/admin/js/datatables.net.config.js',
			'/app/admin/js/tinymce.config.js',
			'/js/jquery.ajax.config.js',
			
			// Main Scripts
			'/js/common.js',
			'/app/admin/js/admin-scripts.js',
			
			// Structure Scripts
			'/app/admin/js/nav.js',
		), filemtime(__FILE__), array(), $debug);
		
		// Serve JavaScript
		Render::Asset($filename);
	} catch(Exception|SassException $exception) {
		error_log($exception->getMessage());
	}