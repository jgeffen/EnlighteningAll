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
$page_title       = "";
$page_description = "";

// Start Header
include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-sm-10 col-md-10 col-xl-8">
				<h1 class="sr-only">Change Password</h1>

				<div role="form" class="title-bar-trim-combo">
					<div class="title-bar">
						<i class="fal fa-lock"></i>
						<h2>Change Password</h2>
					</div>

					<div id="change-password-form" class="form-wrap trim p-sm-5">
						<form>
							<?php
							Render::Component('form-units/input.field', array(
								'form'     => 'change-password-form',
								'label'    => 'Old Password',
								'column'   => 'old_password',
								'type'     => 'password',
								'validate' => 'general'
							));

							Render::Component('form-units/input.field', array(
								'form'     => 'change-password-form',
								'label'    => 'New Password',
								'column'   => 'new_password',
								'type'     => 'password',
								'validate' => 'general'
							));

							Render::Component('form-units/input.field', array(
								'form'     => 'change-password-form',
								'label'    => 'Re-Type Password',
								'column'   => 'retype_password',
								'type'     => 'password',
								'validate' => 'general'
							));
							?>

							<div class="form-group row justify-content-center mb-0">
								<div class="col-md-7 col-lg-6">
									<button type="submit" class="btn btn-block btn-primary submit-btn mt-4 mb-4 mb-sm-0">
										Submit
									</button>
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
		var mainCSS = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#change-password-form');
		var captcha = $('#captcha');

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

				// Handle Ajax
				$.ajax('/ajax/members/password/change', {
					data: ajaxForm.find('form').serializeArray(),
					dataType: 'json',
					method: 'post',
					async: false,
					beforeSend: showLoader,
					complete: hideLoader,
					success: function(response) {
						// Switch Status
						switch (response.status) {
							case 'success':
								// Show Success Message
								ajaxForm.replaceWith(response.html);

								// Scroll to Top
								$('html, body').animate({
									scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 30
								}, 1000);

								// Redirect User
								(function(redirect, countdown) {
									// Check Redirect
									if (redirect.length) {
										// Countdown Redirect
										var countdownInterval = setInterval(function() {
											// Update Text
											redirect.text(countdown);

											// Check Countdown
											if (--countdown <= 0) {
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