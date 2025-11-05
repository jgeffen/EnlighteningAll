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
	
	// Variable Defaults
	$member = Database::Action("SELECT * FROM `members` WHERE MD5(CONCAT(`email`, '-PASS')) = :hash", array(
		'hash' => $dispatcher->getOption('hash')
	))->fetchObject(Membership::class);
	
	// Search Engine Optimization
	$page_title       = "";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-sm-10 col-md-10 col-xl-8">
				<h1 class="sr-only">Reset Password</h1>
				<div role="form" class="title-bar-trim-combo">
					<div class="title-bar">
						<i class="fal fa-lock"></i>
						<h2>Reset Password</h2>
					</div>
					
					<?php if(!is_null($member)): ?>
						<div id="reset-password-form" class="form-wrap trim p-sm-5">
							<form>
								<?php
									Render::Component('form-units/hidden.field', array(
										'name'  => 'hash',
										'value' => $dispatcher->getOption('hash')
									));
									
									Render::Component('form-units/input.field', array(
										'form'     => 'reset-password-form',
										'label'    => 'New Password',
										'column'   => 'new_password',
										'type'     => 'password',
										'validate' => 'general'
									));
									
									Render::Component('form-units/input.field', array(
										'form'     => 'reset-password-form',
										'label'    => 'Re-Type Password',
										'column'   => 'retype_password',
										'type'     => 'password',
										'validate' => 'general'
									));
								?>
								
								<div class="password-block mt-3 mt-lg-0 mb-3">
									<h3 class="title-underlined mt-4">Password Requirements</h3>
									<div class="form-group row split-list justify-content-center mb-0">
										<div class="col-xl-6">
											<ul class="fa-ul">
												<li>
													<span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span>
													At least 8 Characters Long
												</li>
												<li>
													<span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span>
													At least 1 Uppercase Character
												</li>
												<li>
													<span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span>
													At least 1 Lowercase Character
												</li>
											</ul>
										</div>
										<div class="col-xl-6">
											<ul class="fa-ul">
												<li>
													<span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span>
													At least 1 Number
												</li>
												<li>
													<span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span>
													At least 1 Symbol($@#)
												</li>
											</ul>
										</div>
									</div>
								</div>
								
								<div class="form-group">
									<div class="cap-wrap text-center">
										<fieldset>
											<label class="col-form-label" for="captcha">Enter the Characters Shown Below</label>
											<input type="text" name="captcha" class="form-control" id="captcha" required data-type="general" placeholder="* Required">
										</fieldset>
										<noscript><p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p></noscript>
									</div>
								</div>
								
								<div class="form-group row justify-content-center mb-0">
									<div class="col-md-7 col-lg-6">
										<button type="submit" class="btn btn-block btn-primary submit-btn mt-4 mb-4 mb-sm-0">
											Submit
										</button>
									</div>
								</div>
							</form>
						</div>
					<?php else: ?>
						<div class="trim p-sm-5">
							<p class="text-center">Houston, we have a problem. It would seem someone has sent you a bad link.</p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS   = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm  = $('#reset-password-form');
		var captcha   = $('#captcha');
		var redirect  = $('span[data-redirect]').first();
		var countdown = 5;
		
		// Deferred Loading
		$.when(
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/realperson/jquery.realperson.ada.css' }).insertBefore(mainCSS),
			
			// Load Scripts
			$.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
			$.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).done(function() {
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
			
			// Init Captcha
			captcha.realperson();
			
			// Bind Submit Event to Form
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();
				
				// Handle Ajax
				$.ajax('/ajax/members/password/reset', {
					data: ajaxForm.find('form').serializeArray(),
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
								ajaxForm.replaceWith(response.html);
								
								// Scroll to Top
								$('html, body').animate({
									scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 30
								}, 1000);
								
								// Variable Defaults
								var redirect  = $('span[data-redirect]').first();
								var countdown = 5;
								
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
						displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
					}
				});
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
