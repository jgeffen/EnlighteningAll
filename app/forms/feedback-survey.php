<?php
	// Search Engine Optimization
	$page_title       = "Feedback Survey";
	$page_description = "";
	
	// Page Variable
	$no_index = TRUE;
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 offset-lg-3">
				<div class="title-bar-trim-combo" aria-label="Feedback Survey Form" role="form">
					<div class="title-bar">
						<i class="fal fa-clipboard-list-check"></i>
						<h2>Feedback Survey</h2>
					</div>
					
					<div id="feedback-survey-form" class="form-wrap trim p-lg-4">
						<form class="mt-lg-2">
							<div class="row">
								<div class="col-12">
									<div class="card bg-light mb-3">
										<div class="card-header">
											<label class="col-form-label" for="feedback-survey-form-input-rating-staff-members">
												Overall how friendly were the hotel staff members?
											</label>
										</div>
										
										<div class="card-body">
											<div class="form-group mb-0">
												<input id="feedback-survey-form-input-rating-staff-members" class="form-control d-none" type="text" name="rating_staff_members">
											</div>
										</div>
										
										<div class="card-footer">
											<div class="form-label-group mb-0">
												<textarea id="feedback-survey-form-textarea-rating-staff-members-comments" class="form-control" name="rating_staff_members_comments" placeholder="Comments"></textarea>
												<label for="feedback-survey-form-textarea-rating-staff-members-comments">Comments</label>
											</div>
										</div>
									</div>
									
									<div class="card bg-light mb-3">
										<div class="card-header">
											<label class="col-form-label" for="feedback-survey-form-input-rating-check-in-process">
												How was your check-in process?
											</label>
										</div>
										
										<div class="card-body">
											<div class="form-group mb-0">
												<input id="feedback-survey-form-input-rating-check-in-process" class="form-control d-none" type="text" name="rating_check_in_process">
											</div>
										</div>
										
										<div class="card-footer">
											<div class="form-label-group mb-0">
												<textarea id="feedback-survey-form-textarea-rating-check-in-process-comments" class="form-control" name="rating_check_in_process_comments" placeholder="Comments"></textarea>
												<label for="feedback-survey-form-textarea-rating-check-in-process-comments">Comments</label>
											</div>
										</div>
									</div>
									
									<div class="card bg-light mb-3">
										<div class="card-header">
											<label class="col-form-label" for="feedback-survey-form-input-rating-clean-room-arrival">
												How clean was your room upon arrival?
											</label>
										</div>
										
										<div class="card-body">
											<div class="form-group mb-0">
												<input id="feedback-survey-form-input-rating-clean-room-arrival" class="form-control d-none" type="text" name="rating_clean_room_arrival">
											</div>
										</div>
										
										<div class="card-footer">
											<div class="form-label-group mb-0">
												<textarea id="feedback-survey-form-textarea-rating-clean-room-arrival-comments" class="form-control" name="rating_clean_room_arrival_comments" placeholder="Comments"></textarea>
												<label for="feedback-survey-form-textarea-rating-clean-room-arrival-comments">Comments</label>
											</div>
										</div>
									</div>
									
									<div class="card bg-light mb-3">
										<div class="card-header">
											<label class="col-form-label" for="feedback-survey-form-input-rating-room-amenities">
												How were the amenities in the room?
											</label>
										</div>
										
										<div class="card-body">
											<div class="form-group mb-0">
												<input id="feedback-survey-form-input-rating-room-amenities" class="form-control d-none" type="text" name="rating_room_amentities">
											</div>
										</div>
										
										<div class="card-footer">
											<div class="form-label-group mb-0">
												<textarea id="feedback-survey-form-textarea-rating-room-amenities-comments" class="form-control" name="rating_room_amentities_comments" placeholder="Comments"></textarea>
												<label for="feedback-survey-form-textarea-rating-room-amenities-comments">Comments</label>
											</div>
										</div>
									</div>
									
									<div class="card bg-light mb-3">
										<div class="card-header">
											<label class="col-form-label" for="feedback-survey-form-input-rating-food">
												How would you rate the food?
											</label>
										</div>
										
										<div class="card-body">
											<div class="form-group mb-0">
												<input id="feedback-survey-form-input-rating-food" class="form-control d-none" type="text" name="rating_food">
											</div>
										</div>
										
										<div class="card-footer">
											<div class="form-label-group mb-0">
												<textarea id="feedback-survey-form-textarea-rating-food-comments" class="form-control" name="rating_food_comments" placeholder="Comments"></textarea>
												<label for="feedback-survey-form-textarea-rating-food-comments">Comments</label>
											</div>
										</div>
									</div>
									
									<div class="card bg-light mb-3">
										<div class="card-header">
											<label class="col-form-label" for="feedback-survey-form-input-rating-bar-service">
												Rate bar service.
											</label>
										</div>
										
										<div class="card-body">
											<div class="form-group mb-0">
												<input id="feedback-survey-form-input-rating-bar-service" class="form-control d-none" type="text" name="rating_bar_service">
											</div>
										</div>
										
										<div class="card-footer">
											<div class="form-label-group mb-0">
												<textarea id="feedback-survey-form-textarea-rating-bar-service-comments" class="form-control" name="rating_bar_service_comments" placeholder="Comments"></textarea>
												<label for="feedback-survey-form-textarea-rating-bar-service-comments">Comments</label>
											</div>
										</div>
									</div>
									
									<div class="card bg-light mb-3">
										<div class="card-header">
											<label class="col-form-label" for="feedback-survey-form-input-rating-likely-to-return">
												How likely are you to stay at our hotel again?
											</label>
										</div>
										
										<div class="card-body">
											<div class="form-group mb-0">
												<input id="feedback-survey-form-input-rating-likely-to-return" class="form-control d-none" type="text" name="rating_likely_to_return">
											</div>
										</div>
										
										<div class="card-footer">
											<div class="form-label-group mb-0">
												<textarea id="feedback-survey-form-textarea-rating-likely-to-return-comments" class="form-control" name="rating_likely_to_return_comments" placeholder="Comments"></textarea>
												<label for="feedback-survey-form-textarea-rating-likely-to-return-comments">Comments</label>
											</div>
										</div>
									</div>
									
									<div class="card bg-light mb-3">
										<div class="card-header">
											<label class="col-form-label" for="feedback-survey-form-textarea-comments">
												Do you have any other comments, questions, or concerns?
												<br>
												Were there any team members that stood out in the performance of their duties?
											</label>
										</div>
										
										<div class="card-body">
											<div class="form-group mb-0">
												<textarea id="feedback-survey-form-textarea-comments" class="form-control" name="comments"></textarea>
											</div>
										</div>
									</div>
									
									<div class="card bg-light mb-3">
										<div class="card-header">
											If you would like to be contacted, please leave your name, email and phone number.
										</div>
										
										<div class="card-body">
											<div class="form-label-group">
												<input id="feedback-survey-form-input-contact-name" class="form-control" type="text" name="contact_name" placeholder="Name">
												<label for="feedback-survey-form-input-contact-name">Name</label>
											</div>
											
											<div class="form-label-group">
												<input id="feedback-survey-form-input-contact-email" class="form-control" type="email" name="contact_email" placeholder="Email">
												<label for="feedback-survey-form-input-contact-email">Email</label>
											</div>
											
											<div class="form-label-group mb-0">
												<input id="feedback-survey-form-input-contact-phone" class="form-control" type="text" name="contact_phone" placeholder="Phone" data-format="phone">
												<label for="feedback-survey-form-input-contact-phone">Phone</label>
											</div>
										</div>
										
										<div class="card-footer">
											<div class="form-label-group mb-0">
												<textarea id="feedback-survey-form-textarea-contact-comments" class="form-control" name="contact_comments" placeholder="Comments"></textarea>
												<label for="feedback-survey-form-textarea-contact-comments">Comments</label>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<div class="cap-wrap text-center">
											<fieldset>
												<label class="col-form-label" for="feedback-survey-form-captcha">Enter the Characters Shown Below</label>
												<input id="feedback-survey-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
											</fieldset>
											
											<noscript><p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p></noscript>
										</div>
									</div>
									
									<div class="form-group row justify-content-end">
										<div class="col-sm-7">
											<button id="feedback-survey-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
												Submit
											</button>
										</div>
									</div>
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
		var ajaxForm = $('#feedback-survey-form');
		var captcha  = ajaxForm.find('input[id$="captcha"]');
		var rating   = ajaxForm.find('input[name^="rating"]');

		// Init Scripts
		$.when(
			$.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
			$.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
			$.ajax('/js/quickdeploy/jquery.dependent.fields.min.js', { async: false, dataType: 'script' }),
			$.ajax('/library/packages/bootstrap-star-rating/js/star-rating.min.js', { async: false, dataType: 'script' }),
			$.ajax('/library/packages/bootstrap-star-rating/themes/krajee-fas/theme.min.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/realperson/jquery.realperson.ada.css' }).insertBefore(mainCSS);
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/bootstrap-star-rating/css/star-rating.min.css' }).insertBefore(mainCSS);
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/bootstrap-star-rating/themes/krajee-fas/theme.min.css' }).insertBefore(mainCSS);

			// Init Rating
			rating.rating({
				theme: 'krajee-fas',
				size: 'lg',
				step: 1,
				showCaption: false,
				showClear: false,
				displayOnly: false
			});

			// Init Captcha
			captcha.realperson();

			// Handle Submission
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();

				// Handle Ajax
				$.ajax({
					data: $(this).serializeArray(),
					dataType: 'json',
					method: 'post',
					async: true,
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
									scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 90
								}, 1000);
								break;
							case 'error':
								displayMessage(response.message || Object.keys(response.errors).map(function(key) {
									return response.errors[key];
								}).join('<br>'), 'alert', null);
								break;
							default:
								displayMessage(response.message || 'Something went wrong.', 'alert');
						}
					},
					error: function(xhr) {
						displayMessage(xhr.status + ': ' + xhr.statusText + ' - ' + this.url, 'alert');
					}
				});
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
