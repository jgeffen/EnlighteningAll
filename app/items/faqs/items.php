<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Fetch/Set Items
	$items = Items\Faq::FetchAll(Database::Action("SELECT * FROM `faqs` ORDER BY `position` DESC"));
	
	// Search Engine Optimization
	$page_title       = "Enlightening All Frequently Asked Questions";
	$page_description = "Here are some answers to common questions Enlightening All.";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-2">Frequently Asked Questions</h1>
				
				<p>Got questions? We’ve got answers.</p>
				
				<p>
					At ENLIGHTENING ALL™, we know every journey is a little different — whether you’re here to teach, create, book a class, or explore our AI tools. That’s why we’ve put together this FAQ page to cover the things we get asked the most.
				</p>
				
				<p>
						You’ll find quick info on everything from yoga and studio bookings to web design services and our growing lineup of AI-powered products. If you don’t see your question answered here, feel free to reach out — we’re always happy to help.
					
				</p>
				<p>
					Let’s make things clear, simple, and inspiring — just how we like it.
				</p>
				
				<?php
					Render::Component('faqs/titlebar-trim-faq/titlebar-trim-faq', array(
						'items'    => $items,
						'icon'     => '<i class="fal fa-question-circle"></i>',
						'cols'     => 1,
						'message'  => 'Sorry, no FAQs to show at this time. Please check back soon!',
						'modal'    => TRUE,
						'nl2br'    => FALSE,
						'truncate' => FALSE,
						'endpoint' => '/ajax/components/faqs/titlebar-trim-faq/titlebar-trim-faq'
					));
				?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>

