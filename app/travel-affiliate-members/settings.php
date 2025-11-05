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
 * @var TravelAffiliateMembership $member
 */

// Imports
use Items\Enums\Options;
use Items\Enums\Sizes;

// Search Engine Optimization
$page_title       = $member->getTitle('Settings');
$page_description = "";

// Start Header
include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-4">Settings</h1>

				<?php AffiliateDashBoardTransactionSubMenu($member); ?>

				<div role="form" class="title-bar-trim-combo mt-5">
					<div class="title-bar">
						<i class="fal fa-cogs"></i>
						<h2>Settings</h2>
					</div>

					<div id="settings-form" class="form-wrap trim p-lg-4">
						<form class="mt-lg-2">
							<div class="row justify-content-center">
								<div class="col-lg-6">
									<?php
									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Username',
										'column'     => 'username',
										'type'       => 'text',
										'validate'   => 'general',
										'max_length' => 20,
										'horizontal' => TRUE,
										'value'      => $member->getUsername()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'First Name',
										'column'     => 'first_name',
										'type'       => 'text',
										'validate'   => 'general',
										'max_length' => 50,
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getFirstName()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Last Name',
										'column'     => 'last_name',
										'type'       => 'text',
										'validate'   => 'general',
										'max_length' => 50,
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getLastName()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Email',
										'column'     => 'email',
										'type'       => 'text',
										'max_length' => 64,
										'horizontal' => TRUE,
										'value'      => $member->getEmail()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Phone',
										'column'     => 'phone',
										'type'       => 'text',
										'mask'       => 'phone',
										'max_length' => 14,
										'horizontal' => TRUE,
										'value'      => $member->getPhone()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Travel Agency',
										'column'     => 'travel_agency',
										'type'       => 'text',
										'validate'   => 'general',
										'max_length' => 255,
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getTravelAgency()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'EIN Number',
										'column'     => 'ein_number',
										'type'       => 'text',
										'validate'   => 'general',
										'max_length' => 32,
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getTravelAgencyEinNumber()
									));

									?>
								</div>

								<div class="col-lg-6">
									<?php

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Address 1',
										'column'     => 'address_line_1',
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getAddressLine1()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Address 2',
										'column'     => 'address_line_2',
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getAddressLine2()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Country',
										'column'     => 'address_country',
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getAddressCountry()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'State',
										'column'     => 'address_state',
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getAddressState()
									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'City',
										'column'     => 'address_city',
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getAddressCity()

									));

									Render::Component('form-units/input.field', array(
										'form'       => 'settings-form',
										'label'      => 'Postal Code',
										'column'     => 'address_zip_code',
										'type'       => 'text',
										'max_length' => 50,
										'horizontal' => TRUE,
										'readonly'   => TRUE,
										'value'      => $member->getAddressZipCode()
									));
									?>
								</div>
							</div>

							<hr class="mt-4 mb-4">

							<h2 class="mb-4">Affiliate Links</h2>

							<div class="row justify-content-center">
								<div class="col-lg-6">
									<p>
										As an affiliate, you'll receive special web links that are unique to you. These links have a little extra information on them, a query string (with your Affiliate ID) —that tracks anyone clicking on your link back to you. When someone uses your link to visit the enlighteningall.com website and makes an Event or Room purchase, the website knows it was you who referred them, and you earn a commission or credit for that action. It's like having your own digital referral code that ensures you get credit for every customer you bring to the business.

									</p>
									<p>

										You can append your query string <b>/?TravelAffiliate=<?php echo $member->getId(); ?></b> with your Affiliate ID to any link on the site.
									</p>

								</div>
								<div class="col-lg-6">
									<div class="input-group">
										<input class="form-control" type="url" value="https://enlighteningall.com/?TravelAffiliate=<?php echo $member->getId(); ?>" aria-label="Copy text">
										<button class="btn btn-outline-secondary copy-affiliate-link-to-clipboard" type="button">
											<i class="fa-solid fa-copy"></i>
										</button>
									</div>
									<br />
									<div class="input-group">
										<input class="form-control" type="url" value="https://enlighteningall.com/book-now-iframe?TravelAffiliate=<?php echo $member->getId(); ?>" aria-label="Copy text">
										<button class="btn btn-outline-secondary copy-affiliate-link-to-clipboard" type="button">
											<i class="fa-solid fa-copy"></i>
										</button>
									</div>

									<br />
									<div class="input-group">
										<input class="form-control" type="url" value="https://enlighteningall.com/events?TravelAffiliate=<?php echo $member->getId(); ?>" aria-label="Copy text">
										<button class="btn btn-outline-secondary copy-affiliate-link-to-clipboard" type="button">
											<i class="fa-solid fa-copy"></i>
										</button>
									</div>

									<br />
									<div class="input-group">

										<input class="form-control" type="url" value="https://enlighteningall.com/${id}.event?TravelAffiliate=<?php echo $member->getId(); ?>" aria-label="Copy text">


										<button class="btn btn-outline-secondary copy-affiliate-link-to-clipboard" type="button">
											<i class="fa-solid fa-copy"></i>
										</button>
									</div>
									<small style="margin-left: 1rem;">Replace ${id} with the event id</small>
								</div>
							</div>


							<hr class="mt-4 mb-5">

							<div class="form-group row justify-content-center">
								<div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
									<button type="submit" class="btn btn-block btn-primary submit-btn">
										<i class="fas fa-save mr-2"></i>Save
									</button>

									<div class="text-center my-2">
										<small class="text-muted">
											Need to change your password?
											<a href="/travel-affiliate-members/change-password">
												Click Here
											</a>
										</small>
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
		var mainCSS = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#settings-form');

		// Defer Scripts
		$.when(
			// Load Styles
			$('<link/>', {
				type: 'text/css',
				rel: 'stylesheet',
				href: '/library/packages/dropzone/dist/min/basic.min.css'
			}).insertBefore(mainCSS),
			$('<link/>', {
				type: 'text/css',
				rel: 'stylesheet',
				href: '/library/packages/dropzone/dist/min/dropzone.min.css'
			}).insertBefore(mainCSS),
			$('<link/>', {
				type: 'text/css',
				rel: 'stylesheet',
				href: '/library/packages/cropperjs/dist/cropper.min.css'
			}).insertBefore(mainCSS),
			$('<link/>', {
				type: 'text/css',
				rel: 'stylesheet',
				href: '/js/quickdeploy/cropper/jquery.cropper.css'
			}).insertBefore(mainCSS),

			// Load Scripts
			$.getScript('/library/packages/jquery-mask-plugin/dist/jquery.mask.min.js'),
			$.getScript('/library/packages/dropzone/dist/min/dropzone.min.js'),
			$.getScript('/library/packages/cropperjs/dist/cropper.min.js'),
			$.getScript('/js/quickdeploy/cropper/jquery.cropper.min.js'),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).done(function() {

			// Init Masked Input
			$('[data-type="date"]').mask('99/99/9999', {
				placeholder: ' '
			});
			$('[data-type="phone"]').mask('(999) 999-9999', {
				placeholder: ' '
			});
			$('[data-type="zip"]').mask('99999', {
				placeholder: ' '
			});


			// Handle Submission
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();

				// Handle Ajax
				$.ajax('/ajax/travel-affiliate-members/settings', {
					data: $(this).serializeArray(),
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
						displayMessage(xhr.status + ': ' + xhr.statusText + ' - ' + this.url, 'alert');
					}
				});
			});
		});
		//////////////////////////////////////////////////

		$(document).on('click', '.copy-affiliate-link-to-clipboard', function(event) {
			// Select the input element
			var $input = $(this).prev('input');

			// Store the original value
			var originalValue = $input.val();

			// Select the text field and copy the text inside it
			$input.select();
			document.execCommand("copy");

			// Change the input's value to show the 'Copied!' message
			$input.val('Copied!');

			// Wait a bit before resetting the input's value to its original value and removing focus
			setTimeout(function() {
				// Reset the value to the original
				$input.val(originalValue);

				// Optionally, deselect the text after copying
				$input.blur();
			}, 500); // Delay of 1500 milliseconds (1.5 seconds)
		});
		//////////////////////////////////////////////////
	});
</script>

<?php include('includes/body-close.php'); ?>