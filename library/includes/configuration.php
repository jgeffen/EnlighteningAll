<?php
	// TODO: Use Config Class to Load Configuration Files
	// TODO: Dynamically generate FTP user
	// TODO: Build error report email from domain name
	
	// Set Root
	define('ROOT', dirname(__DIR__, 2));
	
	// Set Configuration
	if(!$config = json_decode(file_get_contents(sprintf("%s/library/settings/configuration.json", ROOT)), TRUE)) exit("Unable to open configuration.json.");
	
	// Error Reporting
	error_reporting(-1);
	ini_set('display_errors', 'Off');
	
	// Session Handling
	if(!headers_sent()) {
		if(session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}
	
	// Set Timezone
	date_default_timezone_set($config['system']['timezone']);
	
	// Set Locate
	setlocale(LC_MONETARY, $config['system']['locale']);
	
	// Widget Codes
	define('UA_CODE', $config['constants']['google_analytics_code'] ?? '');
	define('UW_CODE', $config['constants']['userway'] ?? '');
	
	// Site Constants
	define('SITE_NAME', $config['constants']['site_name'] ?? '');
	define('SITE_COMPANY', $config['constants']['site_company'] ?? '');
	define('SITE_COMPANY_DBA', $config['constants']['site_company_dba'] ?? '');
	define('SITE_PHONE', $config['constants']['site_phone'] ?? '');
	define('SITE_FAX', $config['constants']['site_fax'] ?? '');
	define('SITE_ADDRESS', $config['constants']['site_address'] ?? '');
	define('SITE_CITY', $config['constants']['site_city'] ?? '');
	define('SITE_STATE', $config['constants']['site_state'] ?? '');
	define('SITE_ZIP', $config['constants']['site_zip'] ?? '');
	define('SITE_EMAIL', $config['constants']['site_email'] ?? '');
	define('SITE_URL', $config['constants']['site_url'] ?? '');
	
	// SMTP Constants
	const SMTP_AUTH = FALSE;
	const SMTP_HOST = 'serverresponse.net';
	const SMTP_USER = 'serverresponse.net';
	const SMTP_PASS = 'youAintGetMyPass';
	const SMTP_PORT = '465';
	
	// Dev Constants
	const DEV_NAME = 'Fencl Web Design';
	const DEV_LINK = 'https://www.fenclwebdesign.com';
	define('DEV_EMAIL', $config['constants']['dev_email']);
	define('DEV_FROM', $config['constants']['dev_from']);
	define('DEV_SUBJ', $config['constants']['dev_subj']);
	define('FTP_USR', $config['constants']['ftp_user']);
	
	// Basic Authentication
	const ADMIN_AUTH_ENABLED = TRUE;
	const ADMIN_AUTH_USER    = 'enlig';
	const ADMIN_AUTH_PASS    = '321enjmAl$$';
	const ADMIN_AUTH_USER_2  = 'deryk';
	const ADMIN_AUTH_PASS_2  = '321enjmAl$$';
	
	const ADMIN_AUTH_USER_3 = 'bret';
	const ADMIN_AUTH_PASS_3 = '321enjmAl$$';
	
	$settings = array(
		'social_link' => array(
			'facebook'  => 'https://www.facebook.com/bretfencl',
			'twitter'   => 'https://x.com/bretfencl',
			'instagram' => 'https://www.instagram.com/bretfencl/',
			'linkedin'  => 'https://www.linkedin.com/bretfencl/',
			'youtube'   => 'https://www.youtube.com/@creatinglightinthedark/'
		)
	);
	
	
	
	
	
	
	
	
	