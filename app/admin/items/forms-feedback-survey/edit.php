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
	 * @var Admin\User        $admin
	 */
	
	// Variable Defaults
	$page_title = 'Edit Form Submissions: Feedback Survey';
	
	// Set Item
	$item = Items\Forms\FeedbackSurvey::Init($dispatcher->getTableId());
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(404);
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<div class="card bg-light mb-3">
				<div class="card-header">
					<label class="col-form-label" for="rating-staff-members">
						Overall how friendly were the hotel staff members?
					</label>
				</div>
				
				<div class="card-body">
					<div class="form-group mb-0">
						<input id="rating-staff-members" class="form-control d-none" type="text" name="rating_staff_members" value="<?php echo $item->getEncoded('rating_staff_members'); ?>">
					</div>
				</div>
				
				<div class="card-footer">
					<div class="form-label-group mb-0">
						<textarea id="rating-staff-members-comments" class="form-control disable-mce" name="rating_staff_members_comments" placeholder="Comments"><?php echo $item->getEncoded('rating_staff_members_comments'); ?></textarea>
						<label for="rating-staff-members-comments">Comments</label>
					</div>
				</div>
			</div>
			
			<div class="card bg-light mb-3">
				<div class="card-header">
					<label class="col-form-label" for="rating-check-in-process">
						How was your check-in process?
					</label>
				</div>
				
				<div class="card-body">
					<div class="form-group mb-0">
						<input id="rating-check-in-process" class=" form-control d-none" type="text" name="rating_check_in_process" value="<?php echo $item->getEncoded('rating_check_in_process'); ?>">
					</div>
				</div>
				
				<div class="card-footer">
					<div class="form-label-group mb-0">
						<textarea id="rating-check-in-process-comments" class="form-control disable-mce" name="rating_check_in_process_comments" placeholder="Comments"><?php echo $item->getEncoded('rating_check_in_process_comments'); ?></textarea>
						<label for="rating-check-in-process-comments">Comments</label>
					</div>
				</div>
			</div>
			
			<div class="card bg-light mb-3">
				<div class="card-header">
					<label class="col-form-label" for="rating-clean-room-arrival">
						How clean was your room upon arrival?
					</label>
				</div>
				
				<div class="card-body">
					<div class="form-group mb-0">
						<input id="rating-clean-room-arrival" class=" form-control d-none" type="text" name="rating_clean_room_arrival" value="<?php echo $item->getEncoded('rating_clean_room_arrival'); ?>">
					</div>
				</div>
				
				<div class="card-footer">
					<div class="form-label-group mb-0">
						<textarea id="rating-clean-room-arrival-comments" class="form-control disable-mce" name="rating_clean_room_arrival_comments" placeholder="Comments"><?php echo $item->getEncoded('rating_clean_room_arrival_comments'); ?></textarea>
						<label for="rating-clean-room-arrival-comments">Comments</label>
					</div>
				</div>
			</div>
			
			<div class="card bg-light mb-3">
				<div class="card-header">
					<label class="col-form-label" for="rating-room-amenities">
						How were the amenities in the room?
					</label>
				</div>
				
				<div class="card-body">
					<div class="form-group mb-0">
						<input id="rating-room-amenities" class=" form-control d-none" type="text" name="rating_room_amentities" value="<?php echo $item->getEncoded('rating_room_amentities'); ?>">
					</div>
				</div>
				
				<div class="card-footer">
					<div class="form-label-group mb-0">
						<textarea id="rating-room-amenities-comments" class="form-control disable-mce" name="rating_room_amentities_comments" placeholder="Comments"><?php echo $item->getEncoded('rating_room_amentities_comments'); ?></textarea>
						<label for="rating-room-amenities-comments">Comments</label>
					</div>
				</div>
			</div>
			
			<div class="card bg-light mb-3">
				<div class="card-header">
					<label class="col-form-label" for="rating-food">
						How would you rate the food?
					</label>
				</div>
				
				<div class="card-body">
					<div class="form-group mb-0">
						<input id="rating-food" class=" form-control d-none" type="text" name="rating_food" value="<?php echo $item->getEncoded('rating_food'); ?>">
					</div>
				</div>
				
				<div class="card-footer">
					<div class="form-label-group mb-0">
						<textarea id="rating-food-comments" class="form-control disable-mce" name="rating_food_comments" placeholder="Comments"><?php echo $item->getEncoded('rating_food_comments'); ?></textarea>
						<label for="rating-food-comments">Comments</label>
					</div>
				</div>
			</div>
			
			<div class="card bg-light mb-3">
				<div class="card-header">
					<label class="col-form-label" for="rating-bar-service">
						Rate bar service.
					</label>
				</div>
				
				<div class="card-body">
					<div class="form-group mb-0">
						<input id="rating-bar-service" class=" form-control d-none" type="text" name="rating_bar_service" value="<?php echo $item->getEncoded('rating_bar_service'); ?>">
					</div>
				</div>
				
				<div class="card-footer">
					<div class="form-label-group mb-0">
						<textarea id="rating-bar-service-comments" class="form-control disable-mce" name="rating_bar_service_comments" placeholder="Comments"><?php echo $item->getEncoded('rating_bar_service_comments'); ?></textarea>
						<label for="rating-bar-service-comments">Comments</label>
					</div>
				</div>
			</div>
			
			<div class="card bg-light mb-3">
				<div class="card-header">
					<label class="col-form-label" for="rating-likely-to-return">
						How likely are you to stay at our hotel again?
					</label>
				</div>
				
				<div class="card-body">
					<div class="form-group mb-0">
						<input id="rating-likely-to-return" class="form-control d-none" type="text" name="rating_likely_to_return" value="<?php echo $item->getEncoded('rating_likely_to_return'); ?>">
					</div>
				</div>
				
				<div class="card-footer">
					<div class="form-label-group mb-0">
						<textarea id="rating-likely-to-return-comments" class="form-control disable-mce" name="rating_likely_to_return_comments" placeholder="Comments"><?php echo $item->getEncoded('rating_likely_to_return_comments'); ?></textarea>
						<label for="rating-likely-to-return-comments">Comments</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="comments">Comments/Questions/Concerns</label>
				<div class="feedback-wrap">
					<input id="comments" class="form-control disable-mce" type="text" name="comments" value="<?php echo $item->getEncoded('comments'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="contact-name">Name</label>
				<div class="feedback-wrap">
					<input id="contact-name" class="form-control" type="text" name="contact_name" value="<?php echo $item->getEncoded('contact_name'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="contact-email">Email</label>
				<div class="feedback-wrap">
					<input id="contact-email" class="form-control" type="text" name="contact_email" value="<?php echo $item->getEncoded('contact_email'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="contact-phone">Phone</label>
				<div class="feedback-wrap">
					<input id="contact-phone" class="form-control" type="text" name="contact_phone" value="<?php echo $item->getEncoded('contact_phone'); ?>" data-format="phone">
				</div>
			</div>
			
			<div class="form-group">
				<label for="contact-comments">Comments</label>
				<div class="feedback-wrap">
					<input id="contact-comments" class="form-control disable-mce" type="text" name="contact_comments" value="<?php echo $item->getEncoded('contact_comments'); ?>">
				</div>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/forms-feedback-survey">
						<i class="fal fa-ban"></i> Cancel
					</a>
				</div>
			</div>
		</form>
	</div>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS  = $('link[href^="/css/styles-admin.min.css"]');
		var ajaxForm = $('#ajax-wrapper');
		var item     = null || <?php echo $item->toJson(); ?>;
		var rating   = ajaxForm.find('input[name^="rating"]');

		// Make All Inputs Readonly
		$(':input').prop('readonly', true);

		// Init Scripts
		$.when(
			$.ajax('/library/packages/bootstrap-star-rating/js/star-rating.min.js', { async: false, dataType: 'script' }),
			$.ajax('/library/packages/bootstrap-star-rating/themes/krajee-fas/theme.min.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/bootstrap-star-rating/css/star-rating.min.css' }).insertBefore(mainCSS);
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/bootstrap-star-rating/themes/krajee-fas/theme.min.css' }).insertBefore(mainCSS);

			// Init Rating
			rating.rating({
				theme: 'krajee-fas',
				size: 'lg',
				step: 1,
				showCaption: true,
				showClear: false,
				displayOnly: true
			});
		}, function(xhr) {
			displayMessage(xhr.status + ': ' + xhr.statusText + ' - ' + this.url, 'alert', function() {
				$(this).on('hide.bs.modal', function() {
					location.reload();
				});
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

