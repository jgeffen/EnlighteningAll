<?php
	// Search Engine Optimization
	$page_title       = "Membership Survey";
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
				<div class="title-bar-trim-combo" aria-label="Membership Survey Form" role="form">
					<div class="title-bar">
						<i class="fal fa-clipboard-list-check"></i>
						<h2>Membership Survey Form</h2>
					</div>
					
					<div id="membership-survey-form" class="form-wrap trim p-lg-4">
						<form class="mt-lg-2">
							<div class="row">
								<div class="col-12">
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-select-hear-about">How did you find out about Secrets?</label>
										
										<div class="select-wrap form-control">
											<select id="membership-survey-form-select-hear-about" name="hear_about">
												<option>Internet</option>
												<option>Radio</option>
												<option>Friend</option>
												<option>Other</option>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-input-hear-about">How did you find out about Secrets? (Other)</label>
										
										<input id="membership-survey-form-input-hear-about" class="form-control" type="text" name="hear_about" maxlength="50">
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-select-local-resident">Are you a local resident?</label>
										
										<div class="select-wrap form-control">
											<select id="membership-survey-form-select-local-resident" name="local_resident">
												<option>Yes</option>
												<option>No</option>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-input-visiting-from">If not, where are you visiting us from?</label>
										
										<input id="membership-survey-form-input-visiting-from" class="form-control" type="text" name="visiting_from" maxlength="50">
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-select-stay-overnight">Would you usually stay overnight on property?</label>
										
										<div class="select-wrap form-control">
											<select id="membership-survey-form-select-stay-overnight" name="stay_overnight">
												<option>Yes</option>
												<option>No</option>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-select-how-many-visits">How many times a year would you visit Secrets?</label>
										
										<div class="select-wrap form-control">
											<select id="membership-survey-form-select-how-many-visits" name="how_many_visits">
												<option>1-2/yr</option>
												<option>3-6/yr</option>
												<option>Monthly</option>
												<option>More than Once a Month</option>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-select-learn-more">Would you like to learn more about becoming a Condo owner at Secrets?</label>
										
										<div class="select-wrap form-control">
											<select id="membership-survey-form-select-learn-more" name="learn_more">
												<option>Yes</option>
												<option>No</option>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-select-visit-type">Is Secrets your vacation destination or nightly getaway?</label>
										
										<div class="select-wrap form-control">
											<select id="membership-survey-form-select-visit-type" name="visit_type">
												<option>Destination</option>
												<option>Nightly Getaway</option>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-input-name">Name:</label>
										
										<input id="membership-survey-form-input-name" class="form-control" type="text" name="name" placeholder="* Required" maxlength="50">
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-input-phone">Phone:</label>
										
										<input id="membership-survey-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone">
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-input-email">Email:</label>
										
										<input id="membership-survey-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255">
									</div>
									
									<div class="form-group">
										<label class="col-form-label" for="membership-survey-form-textarea-comments">Questions / Comments:</label>
										<textarea id="membership-survey-form-textarea-comments" class="form-control" name="comments" placeholder="* Required" rows="4"></textarea>
									</div>
									
									<div class="form-group">
										<div class="cap-wrap text-center">
											<fieldset>
												<label class="col-form-label" for="membership-survey-form-captcha">Enter the Characters Shown Below</label>
												<input id="membership-survey-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
											</fieldset>
											
											<noscript><p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p></noscript>
										</div>
									</div>
									
									<div class="form-group row justify-content-end">
										<div class="col-sm-7">
											<button id="membership-survey-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
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
		var ajaxForm = $('#membership-survey-form');
		var captcha  = $('#membership-survey-form-captcha');
		
		// Init Scripts
		$.when(
			$.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
			$.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
			$.ajax('/js/quickdeploy/jquery.dependent.fields.min.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/realperson/jquery.realperson.ada.css' }).insertBefore(mainCSS);
			
			// Init Captcha
			captcha.realperson();
			
			// Init Depends On
			(function(settings) {
				settings.forEach(function(setting) {
					setting.element.dependsOn({
						selector: setting.selector,
						value: setting.value,
						wrapper: setting.wrapper
					});
				});
			})([
				{
					element: $('input[name="hear_about"]'),
					selector: $('select[name="hear_about"]'),
					value: ['Other'],
					wrapper: '.form-group'
				},
				{
					element: $('input[name="visiting_from"]'),
					selector: $('select[name="local_resident"]'),
					value: ['No'],
					wrapper: '.form-group'
				}
			]);
			
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
									scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 30
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
