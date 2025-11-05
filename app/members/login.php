<?php
	/*
		Copyright (c) 2021, 2022 FenclWebDesign.com
		This script may not be copied, reproduced or altered in whole or in part.
		We check the Internet regularly for illegal copies of our scripts.
		Do not edit or copy this script for someone else, because you will be held responsible as well.
		This copyright shall be enforced to the full extent permitted by law.
		Licenses to use this script on a single website may be purchased from FenclWebDesign.com
		@Author: Deryk
		*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Search Engine Optimization
	$page_title       = "Enlightening All Members Network - Login";
	$page_description = "Login or Join Enlightening All Members Network";
	
	// Start Header
	include('includes/header.php');
?>
	
	<div class="container-fluid main-content">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
					<h1 class="sr-only">Login</h1>
					
					<div class="title-bar-trim-combo" role="form">
						<div class="title-bar">
							<i class="fal fa-sign-in"></i>
							<h2>Login</h2>
						</div>
						
						<div id="login-form" class="form-wrap trim p-sm-5">
							<form>
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="login-form-input-email">
										Email :
									</label>
									<div class="col-lg-9">
										<input id="login-form-input-email" class="form-control" type="text" name="email" placeholder="* Required" maxlength="255" data-type="general" required>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="login-form-input-password">
										Password :
									</label>
									<div class="col-lg-9">
										<input id="login-form-input-password" class="form-control" type="password" name="password" placeholder="* Required" maxlength="255" data-type="general" required>
									</div>
								</div>
								
								<div class="form-group justify-content-center">
									<button class="btn btn-block btn-primary submit-btn mt-4 mb-4 mb-sm-0" type="submit">
										Submit
									</button>
								</div>
								
								<div class="form-group text-center mb-0">
									<small class="text-muted">
										<a href="/members/register">
											Register
										</a>
										&diamond;
										<a href="/members/forgot-password">
											Forgot Password
										</a>
									</small>
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
		navigator.geolocation.getCurrentPosition(function(position) {
			const latitude  = position.coords.latitude;
			const longitude = position.coords.longitude;
			// Send this data to your server for verification
			Cookies.set('latitude', latitude, { expires: 1 }); // expires in 1 day
			Cookies.set('longitude', longitude, { expires: 1 });

		});
	</script>
	<script>
		$(function() {
			// Variable Defaults
			var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
			var ajaxForm = $('#login-form');
			var captcha  = $('#captcha');

			// Deferred Loading
			$.when(
				// Load Styles
				$('<link/>', {
					type: 'text/css',
					rel: 'stylesheet',
					href: '/js/realperson/jquery.realperson.ada.css'
				}).insertBefore(mainCSS),

				// Load Scripts
				$.ajax('/js/realperson/jquery.plugin.min.js', {
					async: false,
					dataType: 'script'
				}),
				$.ajax('/js/realperson/jquery.realperson.ada.js', {
					async: false,
					dataType: 'script'
				}),
				$.Deferred(function(deferred) {
					$(deferred.resolve);
				})
			).done(function() {
				// Init Captcha
				captcha.realperson();

				// Bind Submit Event to Form
				ajaxForm.on('submit', 'form', function(event) {
					// Prevent Default
					event.preventDefault();
					var ajaxFormData = ajaxForm.find('form').serializeArray();
					ajaxFormData.push({ name: 'latitude', value: Cookies.get('latitude') }, { name: 'longitude', value: Cookies.get('longitude') });
					// Handle Ajax
					$.ajax('/ajax/members/login', {
						data: ajaxFormData,
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
									ajaxForm.parents('div[role="form"]').html(response.html);

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
												location.href = (new URLSearchParams(location.search)).get('rel') || redirect.data('redirect');

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
						}
					});
				});
			});
		});
	</script>

<?php include('includes/body-close.php'); ?>