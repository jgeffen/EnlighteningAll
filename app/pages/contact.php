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
	
	// Search Engine Optimization
	$page_title       = "Contact " . SITE_COMPANY;
	$page_description = "Do you have any feedback? Want to make a suggestion?.";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1>Contact <?php echo SITE_COMPANY; ?></h1>
				
				<div class="row">
					<div class="col-lg-4">
						<hr class="d-block d-md-none">
						<p>
							<b>TEXT or CALL: </b><a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a><br>
							<?php if(!empty(SITE_FAX)): ?>
								<b>Fax: </b><a href="fax:<?php echo SITE_FAX; ?>"><?php echo SITE_FAX; ?></a><br>
							<?php endif; ?>
							<b>Email: </b><a class="email-link"></a>
						</p>
						<p>
							<?php echo SITE_ADDRESS; ?><br>
							<?php echo SITE_CITY; ?>, <?php echo SITE_STATE; ?> <?php echo SITE_ZIP; ?>
						</p>
					</div>
				</div>
				
				<div class="title-bar-trim-combo" aria-label="Contact Form" role="form">
					<div class="title-bar">
						<i class="fal fa-clipboard-list-check"></i>
						<h2>Contact Form</h2>
					</div>
					
					<div id="contact-form" class="form-wrap trim p-lg-4">
						<form class="mt-lg-2">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="contact-form-input-name">Name:</label>
										<div class="col-lg-9">
											<input id="contact-form-input-name" class="form-control" type="text" name="name" placeholder="* Required" maxlength="50">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="contact-form-input-phone">Phone:</label>
										<div class="col-lg-9">
											<input id="contact-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="contact-form-input-email">Email:</label>
										<div class="col-lg-9">
											<input id="contact-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="contact-form-input-address">Address:</label>
										<div class="col-lg-9">
											<input id="contact-form-input-address" class="form-control" type="text" name="address" maxlength="255">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="contact-form-input-city">City:</label>
										<div class="col-lg-9">
											<input id="contact-form-input-city" class="form-control" type="text" name="city" maxlength="255">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="contact-form-input-state">State:</label>
										<div class="col-lg-9">
											<input id="contact-form-input-state" class="form-control" type="text" name="state" maxlength="255">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="contact-form-input-zip-code">Zip Code:</label>
										<div class="col-lg-9">
											<input id="contact-form-input-zip-code" class="form-control" type="text" name="zip_code" placeholder="* Required" maxlength="255" data-format="zip">
										</div>
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group">
										<label class="col-form-label" for="contact-form-textarea-comments">Questions / Comments:</label>
										<textarea id="contact-form-textarea-comments" class="form-control" name="comments" placeholder="* Required" rows="4"></textarea>
									</div>
									
									<div class="form-group">
										<div class="cap-wrap text-center">
											<fieldset>
												<label class="col-form-label" for="contact-form-captcha">Enter the Characters Shown Below</label>
												<input id="contact-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
											</fieldset>
											
											<noscript><p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p></noscript>
										</div>
									</div>
									
									<div class="form-group row justify-content-end">
										<div class="col-sm-7">
											<button id="contact-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
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

<div class="container-fluid main-content p-0" style="line-height: 0;">
	<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3518.787092332972!2d-80.64312858727668!3d28.122517875846782!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88de0ff4f0105a0d%3A0xfcef3092e88962e5!2sEnlightening%20All!5e0!3m2!1sen!2sus!4v1754335980221!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#contact-form');
		var captcha  = $('#contact-form-captcha');
		
		// Init Scripts
		$.when(
			$.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
			$.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/realperson/jquery.realperson.ada.css' }).insertBefore(mainCSS);
			
			// Init Captcha
			captcha.realperson();
			
			// Handle Submission
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();
				
				// Handle Ajax
				$.ajax('/ajax/contact', {
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
