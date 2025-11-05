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
	
	// Imports
	use Items\Enums\Sizes;
	
	// Search Engine Optimization
	$page_title       = "";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="sr-only">Check-In</h1>
				<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 justify-content-center">
					<div class="col">
						<div class="card">
							<img class="card-img-top" src="<?php echo Items\Defaults::AVATAR_XL; ?>" data-src="<?php echo $member->getAvatar()?->getImage(Sizes\Avatar::XL, TRUE); ?>">
							<div class="card-body pt-3">
								<h2 class="card-title text-center mb-0"><?php echo $member->getFirstNames(); ?></h2>
								
								<img class="img-fluid mb-0" src="<?php echo $member->qrCode()->getProfileLink()->generate()?->getDataUri(); ?>">
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
		// Variable Defaults
		var posts    = $('.posts');
		var readMore = function(index, element) {
			// Variable Defaults
			var post    = $(element);
			var section = post.find('.read-more');
			var content = section.find('div[id^="post-read-more"]');
			var target  = '#' + content.attr('id');
			
			// Check Height
			if(content.height() > 150) {
				content.addClass('collapse read-more__content').attr('aria-expanded', false).after($('<a/>', {
					'class': 'collapsed read-more__btn',
					'aria-expanded': false,
					'data-target': target,
					'data-toggle': 'collapse',
					'role': 'button'
				}).collapse());
			}
		};
		
		// Check Read More on Posts
		posts.find('.posts__child').each(readMore);
		
		// Bind Mutation Event to Listen for New Post
		(new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				if(mutation.addedNodes && mutation.addedNodes.length > 0) {
					[].some.call(mutation.addedNodes, function(element) {
						if(element.classList.contains('posts__child')) {
							readMore(null, element);
						}
					});
				}
			});
		})).observe(document.body, {
			attributes: true,
			childList: true,
			characterData: true
		});
	});
	
	/*==============================================*/
	/*	==== Sticky Sidebar for Events ====  		*/
	/*==============================================*/
	$(window).on('load', function() {
		window.eventStickySidebar = new StickySidebar('#events-sidebar', {
			topSpacing: 120,
			bottomSpacing: 0,
			containerSelector: '#content-area > .main-content > .container',
			innerWrapperSelector: '.events-sidebar__inner',
			minWidth: 768,
			resizeSensor: true
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
