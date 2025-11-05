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
	
	// Set Room
	$item = Items\Room::Init($dispatcher->getId());
	
	// Check Room
	if(is_null($item)) Render::ErrorDocument(HttpStatusCode::NOT_FOUND);
	
	// Set Review
	$review = $member->getRoom($item->getId())?->getReview();
	
	// Search Engine Optimization
	$page_title       = "";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content" data-room="<?php echo $item->toJson(JSON_HEX_QUOT); ?>">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $item->getHeading(); ?></h1>
				
				<?php $item->renderGallery(); ?>
				
				<?php echo $item->getContent(); ?>
				
				<hr class="clear my-4">
				
				<div role="form" class="title-bar-trim-combo" aria-label="Member Room Review Form">
					<div class="title-bar">
						<i class="fal fa-circle-star"></i>
						<h2>Member Room Review</h2>
					</div>
					
					<div id="member-room-review-form" class="form-wrap trim p-lg-4">
						<form>
							<div class="form-group">
								<textarea id="member-room-review-form-textarea-review" class="form-control tinymce" name="review"><?php echo $review?->getContent(); ?></textarea>
							</div>
							
							<div class="row mt-4">
								<div class="col-md-6 my-2 order-md-last">
									<button class="btn btn-primary btn-block" type="submit">
										<i class="fas fa-save mr-2"></i>Save
									</button>
								</div>
								
								<div class="col-md-6 my-2 order-md-first">
									<a class="btn btn-outline btn-block" href="/members/rooms">
										<i class="fas fa-ban mr-2"></i>Cancel
									</a>
								</div>
							</div>
						</form>
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
		var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#member-room-review-form');
		var room     = $('div[data-room]').data('room');
		
		// Init TinyMCE
		tinymce.init({
			selector: '.tinymce',
			theme: 'silver',
			cache_suffix: '?v=6.1.2',
			base_url: '/library/packages/tinymce',
			browser_spellcheck: true,
			document_base_url: '/',
			element_format: 'html',
			forced_root_block: 'p',
			formats: {
				bold: { inline: 'strong' },
				italic: { inline: 'em' },
				underline: { inline: 'u' }
			},
			height: 362,
			keep_styles: false,
			menubar: false,
			mobile: { toolbar_mode: 'scrolling' },
			plugins: 'emoticons',
			protect: [/<div class="clear"><\/div>/g],
			relative_urls: false,
			toolbar: 'bold italic underline emoticons',
			valid_elements: 'p,br,strong/b,em/i,u',
			verify_html: true,
			statusbar: false
		});
		
		// Handle Submission
		ajaxForm.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Handle Ajax
			$.ajax('/ajax/members/rooms/review/' + room.id, {
				data: $(this).serializeArray(),
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							// Show Success Message
							ajaxForm.html(response.html);
							
							// Scroll to Top
							$('html, body').animate({
								scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 75
							}, 1000);
							
							// Redirect User
							(function(redirect, countdown) {
								// Check Redirect
								if(redirect.length) {
									// Countdown Redirect
									var countdownInterval = setInterval(function() {
										// Update Text
										redirect.text(countdown);
										
										// Check Countdown
										if(--countdown <= 0) {
											location.href = redirect.data('redirect');
											
											clearInterval(countdownInterval);
										}
									}, 1000);
								}
							})($('span[data-redirect]').first(), 5);
							break;
						case 'error':
							displayMessage(response.message || Object.keys(response.errors).map(function(key) {
								return response.errors[key];
							}).join('<br>'), 'alert', null);
							break;
						default:
							displayMessage(response.message || 'Something went wrong.', 'alert');
					}
				}
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

