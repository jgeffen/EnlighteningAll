<?php
	/*
	Copyright (c) 2021, 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Search Engine Optimization
	$page_title       = sprintf("%s's Messages", $member->getFirstName());
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<style>
	#contact-list, #messages-container {
		will-change: min-height;
	}
	
	.contact-list__inner, .messages-container__inner {
		transform: translate(0, 0);
		transform: translate3d(0, 0, 0);
		will-change: position, transform;
	}
</style>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div id="contact-list" class="col-xs-12 col-md-4 col-lg-3 px-0 mx-2">
				<div class="contact-list__inner">
					<div class="card p-0">
						<ul class="list-group list-group-flush h-100">
							<li class="list-group-item d-flex justify-content-center align-content-center flex-wrap mb-0">
								<p class="mb-0">Your contact list is empty.</p>
							</li>
						</ul>
					</div>
				</div>
			</div>
			
			<div id="messages-container" class="col px-0 mx-2">
				<div class="messages-container__inner">
					<div class="card p-0">
						<div class="card-body">
							<div class="d-flex justify-content-center align-content-center flex-wrap h-100">
								<p class="h3 text-muted text-center">
									<i class="fas fa-mail-bulk d-none d-lg-inline-block"></i> You Currently Have <u><?php echo $member->messages(NULL, TRUE)->count(); ?></u> Unread Message(s)
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Stick Message Pane
		window.stickyMessagePane = new StickySidebar('#messages-container', {
			topSpacing: $('#nav-wrapper').outerHeight(true) + 40,
			bottomSpacing: 40,
			containerSelector: '#content-area > .main-content > .container',
			innerWrapperSelector: '.messages-container__inner',
			resizeSensor: true
		});
		
		// Stick Contact List
		window.stickyContactList = new StickySidebar('#contact-list', {
			topSpacing: $('#nav-wrapper').outerHeight(true) + 40,
			bottomSpacing: 40,
			containerSelector: '#content-area > .main-content > .container',
			innerWrapperSelector: '.contact-list__inner',
			resizeSensor: true
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
