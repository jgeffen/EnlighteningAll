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
			'/css/email-stylesheet.scss'
		), filemtime(__FILE__), array(
			'/css/_variables.scss',
			'/css/common.scss'
		), $debug);
		
		// Serve JavaScript
		Render::Asset($filename);
	} catch(Exception|SassException $exception) {
		error_log($exception->getMessage());
	}