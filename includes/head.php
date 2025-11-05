<?php
	
	/**
	 * @var null|string $page_title
	 * @var null|string $page_description
	 */
	
	// Variable Defaults
	$page_title       ??= '';
	$page_description ??= '';
	
	// STORE REFORMED ARRAY DATA
	$reformed_array = array();
	
	// SET VOICE SCHEMA
	$voice = Database::Action("SELECT `question`, `answer` FROM `voice_search` WHERE `page_url` = :page_url", array(
		'page_url' => !empty($_SERVER['REQUEST_URI']) ? filter_input(INPUT_SERVER, 'REQUEST_URI') : '/'
	))->fetch(PDO::FETCH_ASSOC);
	
	if(!empty($voice)) {
		$voice_schema = array_map(function($schema) {
			return json_decode($schema);
		}, $voice);
		foreach($voice_schema['question'] as $key => $value) {
			$reformed_array[$key] = array('question' => $voice_schema['question'][$key], 'answer' => $voice_schema['answer'][$key]);
		}
		
		// Check Voice Schema
		if(!empty($voice_schema)) {
			$schema = json_encode(array(
				'@context'   => 'http://schema.org',
				'@type'      => 'WebPage',
				'url'        => curPageURL(),
				'speakable'  => array(
					'@type' => 'SpeakableSpecification',
					'xpath' => array(
						'/html/head/title',
						'/html/head/meta[@name="description"]/@content'
					)
				),
				'mainEntity' => array_map(function($schema) {
					return array(
						'@type'          => 'Question',
						'name'           => $schema['question'],
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => $schema['answer']
						)
					);
				}, $reformed_array)
			), JSON_PRETTY_PRINT);
		}
	}

?>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title><?php echo $page_title; ?></title>
	<meta name="description" content="<?php echo htmlentities($page_description, ENT_QUOTES); ?>">
	
	<?php if(!empty($no_index)) : ?>
		<meta name="robots" content="noindex">
	<?php endif; ?>
	
	<!-- Schema.org Markup for Google+ -->
	<meta itemprop="name" content="<?php echo htmlentities($page_title, ENT_QUOTES); ?>">
	<meta itemprop="description" content="<?php echo htmlentities($page_description, ENT_QUOTES); ?>">
	<meta itemprop="image" content="<?php echo curSiteURL() . (!empty($top_image) ? $top_image : '/images/site-image.jpg'); ?>">
	
	<?php if(isset($item) && $item instanceof Items\Interfaces\PageType) : ?>
		<!-- CANONICAL LINK -->
		<link rel="canonical" href="<?php echo curSiteUrl($item->getLink()); ?>"/>
	<?php endif; ?>
	
	<?php if(!empty($schema)) : ?>
		<!-- VOICE SCHEMA MARKUP -->
		<script type="application/ld+json">
			<?php echo $schema; ?>
		
		
		</script>
	<?php endif; ?>
	
	<!-- Facebook Open Graph data -->
	<meta property="og:title" content="<?php echo htmlentities($page_title, ENT_QUOTES); ?>">
	<meta property="og:description" content="<?php echo htmlentities($page_description, ENT_QUOTES); ?>">
	<meta property="og:phone_number" content="1-844-563-6969"/>
	<meta property="og:street-address" content="2145 E Irlo Bronson Memorial Hwy"/>
	<meta property="og:locality" content="Kissimmee"/>
	<meta property="og:region" content="Florida"/>
	<meta property="og:postal-code" content="34744"/>
	<meta property="og:type" content="<?php echo !empty($homepage) ? 'website' : 'article'; ?>">
	<meta property="og:url" content="<?php echo curPageURL(); ?>">
	<?php if(!empty($top_image)) : ?>
		<meta property="og:image" content="<?php echo curSiteURL() . $top_image; ?>">
		<meta property="og:image:secure_url" content="<?php echo curSiteURL() . $top_image; ?>">
		<meta property="og:image:width" content="<?php echo getImageDimension(ltrim($top_image, '/'), 'width'); ?>">
		<meta property="og:image:height" content="<?php echo getImageDimension(ltrim($top_image, '/'), 'height'); ?>">
		<link rel="image_src" href="<?php echo curSiteURL() . $top_image; ?>">
	<?php else: ?>
		<meta property="og:image" content="<?php echo curSiteURL() . '/images/site-image.jpg'; ?>">
		<meta property="og:image:secure_url" content="<?php echo curSiteURL() . '/images/site-image.jpg'; ?>">
		<meta property="og:image:width" content="1200">
		<meta property="og:image:height" content="600">
		<link rel="image_src" href="<?php echo curSiteURL() . '/images/site-image.jpg'; ?>">
	<?php endif; ?>
	<meta property="og:site_name" content="<?php echo SITE_COMPANY; ?>">
	
	<!-- Twitter Card data -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="<?php echo SITE_COMPANY; ?>">
	<meta name="twitter:title" content="<?php echo htmlentities($page_title, ENT_QUOTES); ?>">
	<meta name="twitter:description" content="<?php echo htmlentities($page_description, ENT_QUOTES); ?>">
	<meta name="twitter:image:src" content="<?php echo curSiteURL() . (!empty($top_image) ? $top_image : '/images/site-image.jpg'); ?>">
	
	<?php /* SERVER PUSH PRELOADS */ ?>
	<?php /* include('preloader.php'); */ ?>
	
	<link rel="shortcut icon preload" href="/images/iconified/favicon.ico" type="image/x-icon"/>
	<link rel="apple-touch-icon" href="/images/iconified/apple-touch-icon.png"/>
	<link rel="apple-touch-icon" sizes="57x57" href="/images/iconified/apple-touch-icon-57x57.png"/>
	<link rel="apple-touch-icon" sizes="72x72" href="/images/iconified/apple-touch-icon-72x72.png"/>
	<link rel="apple-touch-icon" sizes="76x76" href="/images/iconified/apple-touch-icon-76x76.png"/>
	<link rel="apple-touch-icon" sizes="114x114" href="/images/iconified/apple-touch-icon-114x114.png"/>
	<link rel="apple-touch-icon" sizes="120x120" href="/images/iconified/apple-touch-icon-120x120.png"/>
	<link rel="apple-touch-icon" sizes="144x144" href="/images/iconified/apple-touch-icon-144x144.png"/>
	<link rel="apple-touch-icon" sizes="152x152" href="/images/iconified/apple-touch-icon-152x152.png"/>
	
	<?php /* MAIN COMBINED STYLESHEET */ ?>
	<link href="/css/styles-main.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap">
	<style>
		#terms_privacy_signature_modal,
		#affiliate_terms_conditions_signature_modal,
		#terms_privacy_signature_span,
		#affiliate_terms_conditions_signature_span {
			font-family: 'Pacifico', cursive;

		}
	</style>
	
	<script>
		var siteEmailUser   = '<?php echo explode("@", SITE_EMAIL)[0]; ?>';
		var siteEmailDomain = '<?php echo explode("@", SITE_EMAIL)[1]; ?>';
	</script>
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
    <!-- Google tag (gtag.js) -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-L59357B0P1"></script>
    <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());



        gtag('config', 'G-L59357B0P1');

    </script>
    <style>
        /* 🔧 Force show Get Pass button on mobile without editing main.css */
        @media (max-width: 767.98px) {
            .btn-toolbar {
                flex-wrap: wrap !important;
            }

            .btn-toolbar .btn {
                display: inline-flex !important;
                justify-content: center !important;
                align-items: center !important;
                flex: 1 1 100% !important;
                margin-bottom: 0.75rem !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            /* In case something upstream hides it */
            .btn-toolbar .btn.btn-warning {
                display: flex !important;
            }

            /* Sometimes .card-footer or .trim have overflow:hidden */
            .card-footer, .trim, .inset {
                overflow: visible !important;
            }
        }
    </style>

</head>