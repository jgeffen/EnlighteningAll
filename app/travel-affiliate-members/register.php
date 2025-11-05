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
use Items\Collections;
use Items\Enums\Options;

// Search Engine Optimization
$page_title       = sprintf("Register Now - %s", SITE_COMPANY);

$page_description = "Verified Members Event Network";

// Start Header
include('includes/header.php');
?>

<style>
	.checkbox.checked .check-btn:before {
		opacity: 1;
		-webkit-transform: scale(1);
		-moz-transform: scale(1);
		-ms-transform: scale(1);
		-o-transform: scale(1);
		transform: scale(1);
	}
</style>

<div class="container-fluid main-content">

	<div class="container">

		<div class="row">

			<div class="col">

				<h1 class="title-underlined mb-4">
					Join Enlightening All™ - Travel Affiliate Network
				</h1>

				<h1 class="title mb-4">
					Your Affiliate Account Details
				</h1>



			</div>

		</div>

		<p>Use the form below to create an affiliate account for Enlightening All™.</p>



		<div role="form" class="title-bar-trim-combo mt-5">
			<div class="title-bar">
				<i class="fal fa-clipboard-list-check"></i>
				<h2>Registration Form</h2>
			</div>

			<div id="register-form" class="form-wrap trim p-lg-4">

				<form class="mt-lg-2">

					<div class="row justify-content-center">

						<div class="col-lg-6">
							<?php
							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'First Name',
								'column'     => 'first_name',
								'type'       => 'text',
								'validate'   => 'general',
								'max_length' => 16,
								'horizontal' => TRUE
							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Last Name',
								'column'     => 'last_name',
								'type'       => 'text',
								'validate'   => 'general',
								'max_length' => 16,
								'horizontal' => TRUE
							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Email',
								'column'     => 'email',
								'type'       => 'email',
								'validate'   => 'email',
								'max_length' => 64,
								'horizontal' => TRUE
							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Phone',
								'column'     => 'phone',
								'type'       => 'text',
								'validate'   => 'general',
								'mask'       => 'phone',
								'max_length' => 14,
								'horizontal' => TRUE
							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Travel Agency',
								'column'     => 'travel_agency',
								'type'       => 'text',
								'validate'   => 'general',
								'max_length' => 255,
								'horizontal' => TRUE
							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'EIN Number',
								'column'     => 'ein_number',
								'type'       => 'text',
								'validate'   => 'general',
								'max_length' => 32,
								'horizontal' => TRUE
							));

							?>
						</div>

						<div class="col-lg-6">

							<?php




							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Address Line 1',
								'column'     => 'address_line_1',
								'type'       => 'text',
								'validate'   => 'general',
								'max_length' => 255,
								'horizontal' => TRUE,
							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Address Line 2',
								'column'     => 'address_line_2',
								'type'       => 'text',
								'max_length' => 255,
								'horizontal' => TRUE,
							));

							Render::Component('form-units/select.field', array(
								'form'       => 'register-form',
								'label'      => 'Country',
								'column'     => 'address_country',
								'horizontal' => TRUE,
								'options'    => Locations\Country::Options(Database::Action("SELECT * FROM `location_countries` ORDER BY `name`")),
								'validate'   => 'general',
								'default'    => 'US'
							));

							Render::Component('form-units/select.field', array(
								'form'       => 'register-form',
								'label'      => 'State',
								'column'     => 'address_state',
								'horizontal' => TRUE,
								'validate'   => 'general',

							));

							Render::Component('form-units/select.field', array(
								'form'       => 'register-form',
								'label'      => 'City',
								'column'     => 'address_city',
								'horizontal' => TRUE,
								'validate'   => 'general',

							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Postal Code',
								'column'     => 'address_zip_code',
								'type'       => 'text',
								'validate'   => 'general',
								'max_length' => 5,
								'horizontal' => TRUE,
								'mask'       => 'postal'
							));

							?>



						</div>
					</div>



					<hr class="mt-0 mb-4">

					<div class="row justify-content-center">
						<div class="col-lg-6">
							<?php
							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Username',
								'column'     => 'username',
								'type'       => 'text',
								'validate'   => 'general',
								'max_length' => 16,
								'horizontal' => TRUE
							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Password',
								'column'     => 'password',
								'type'       => 'password',
								'validate'   => 'general',
								'horizontal' => TRUE
							));

							Render::Component('form-units/input.field', array(
								'form'       => 'register-form',
								'label'      => 'Re-Type Password',
								'column'     => 'retype_password',
								'type'       => 'password',
								'validate'   => 'general',
								'horizontal' => TRUE
							));
							?>

						</div>
						<div class="col-lg-6">
							<div class="password-block mt-3 mt-lg-0 mb-3">
								<h3 class="title-underlined">Password Requirements</h3>
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
						</div>
					</div>

					<hr class="mt-0 mb-4">



					<div class="col-lg-6">

						<div class="form-group row" style="align-items:center;">

							<label for="terms_privacy" class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">
								Terms & Privacy :
							</label>

							<div class="col-lg-9">
								<div class="checkbox" style="padding-bottom:0">

									<span class="check-btn">
										<input id="terms_privacy" class="form-control" type="checkbox" name="terms_privacy[]" disabled required>
									</span>

									<a href="#" data-toggle="modal" data-target="#terms-privacy">View Terms & Privacy</a>

									<span id="terms_privacy_signature_span"></span>

									<input id="terms_privacy_signature" class="form-control" type="hidden" name="terms_privacy_signature[]" required>

								</div>
							</div>

							<label for="affiliate_terms_conditions" class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">
								Affiliate Terms & Conditions :
							</label>

							<div class="col-lg-9">
								<div class="checkbox" style="padding-bottom:0px">

									<span class="check-btn">
										<input id="affiliate_terms_conditions" class="form-control" type="checkbox" name="affiliate_terms_conditions[]" disabled required>
									</span>
									<a href="#" data-toggle="modal" data-target="#affiliate-terms-conditions">Affiliate Terms & Conditions</a>

									<span id="affiliate_terms_conditions_signature_span"></span>

									<input id="affiliate_terms_conditions_signature" class="form-control" type="hidden" name="affiliate_terms_conditions_signature[]" required>

								</div>
							</div>

						</div>

					</div>

					<hr class="my-4">

					<div class="row justify-content-center">
						<div class="col-lg-6">
							<div class="form-group">
								<div class="cap-wrap text-center">
									<fieldset>
										<label class="col-form-label" for="captcha">Enter the Characters Shown Below</label>
										<input type="text" name="captcha" class="form-control" id="captcha" required data-type="general" placeholder="* Required">
									</fieldset>
									<noscript>
										<p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p>
									</noscript>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group row h-100 align-items-end justify-content-end">
								<div class="col-sm-7">
									<button type="submit" class="btn btn-block btn-primary submit-btn">
										Submit
									</button>

									<div class="text-center my-2">
										<small class="text-muted">
											By clicking "Submit" you agree to the <a href="#" data-toggle="modal" data-target="#terms-privacy">Terms and Privacy</a> and <a href="#" data-toggle="modal" data-target="#affiliate-terms-conditions">Affiliate Terms & Conditions</a> for users of this websites.
										</small>
									</div>

									<div class="text-center my-2">
										<small class="text-muted">
											Already have an account?
											<a href="/travel-affiliate-members/login">
												Login
											</a>
										</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>



<?php include('includes/footer.php'); ?>

<script>
	$(function() {

		// Variable Defaults
		var mainCSS = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#register-form');
		var captcha = $('#captcha');

		/*
		var partner = {
			section: $('#register-form-partner'),
			selector: $('select[name="couple"]')
		};
		*/

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
			$.ajax('/js/quickdeploy/jquery.dependent.fields.min.js', {
				async: false,
				dataType: 'script'
			}),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).done(function() {

			// Init Captcha
			captcha.realperson();

			// Init Depends On
			/*partner.section.dependsOn({
				selector: partner.selector,
				value: ['1'],
				wrapper: null
			});*/

			//////////////////////////////////////////////////////////
			// Bind Change Event to Countries
			ajaxForm.on('change', 'select[name="address_country"]', function() {

				// Variable Defaults
				var stateSelect = ajaxForm.find('select[name="address_state"]');

				// Handle Ajax
				$.ajax('/ajax/travel-affiliate-members/account/state-options', {
					data: {
						type: 'states',
						sub_type: this.value
					},
					dataType: 'json',
					method: 'post',
					async: true,
					success: function(response) {
						// Switch Status
						switch (response.status) {
							case 'success':
								// Empty Values
								stateSelect.children().not(':first').remove();

								// Append New Options
								Object.keys(response.options).forEach(function(value) {
									stateSelect.append($('<option/>', {
										value: value,
										text: response.options[value],
										selected: stateSelect.data('value') === value
									}));
								});

								// Check Default Value
								if (stateSelect.val().length === 0) {
									stateSelect.find('option').each(function() {
										// Check if the current option's value matches the default value
										if ($(this).val() === stateSelect.data('default')) {
											// Set this option as selected
											$(this).prop('selected', true);

											// Break the loop since we found the match
											return false; // In jQuery each loop, return false is equivalent to breaking the loop
										}
									});
								}

								// Trigger Change
								stateSelect.trigger('change');
								break;
							case 'error':
								displayMessage(response.message || Object.keys(response.errors).map(function(key) {
									let element = ajaxForm[0].querySelector(`[name="${key}"]`);
									let feedback = element?.parentElement.querySelector('.invalid-feedback');
									element?.setCustomValidity(response.errors[key]);

									if (feedback) {
										feedback.innerHTML = response.errors[key];
									} else {
										feedback = document.createElement('div');
										feedback.className = 'invalid-feedback';
										feedback.innerHTML = response.errors[key];
										element?.parentElement.appendChild(feedback);
									}

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
			}).find('select[name="address_country"]').trigger('change');

			//////////////////////////////////////////////////////////



			//////////////////////////////////////////////////////////

			// Bind Change Event to File Input
			/*ajaxForm.on('change', 'input[type="file"]', function() {
				// Variable Defaults
				var input = $(this);
				var allowed = input.prop('accept').split(',');

				// Check File Size
				if (this.files[0].size > window.settings.maxFilesize.B) {
					// Reset Value
					input.val('');

					// Swap Label
					input.siblings('label').text('* Required');

					// Display Notification
					displayMessage('The maximum file size is ' + window.settings.maxFilesize.MB + 'MB.', 'alert');

					return;
				}

				// Check File Type
				if ($.inArray(this.files[0].type, allowed) === -1) {
					// Reset Value
					input.val('');

					// Swap Label
					input.siblings('label').text('* Required');

					// Display Notification
					displayMessage('The only acceptable file types are ' + allowed.join(' or '), 'alert');

					return;
				}

				// Swap Label
				input.siblings('label').text(this.files[0].name);
			});*/




			// Bind Submit Event to Form
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();

				// Variable Defaults
				var progressBar;

				var termsDisabled = $(document).find("#terms_privacy").is(':disabled');
				var affiliateDisabled = $(document).find("#affiliate_terms_conditions").is(':disabled');

				if (!termsDisabled && !termsDisabled) {



					var formData = new FormData(this);

					var updateTermsPrivacySignatureValue = $(document).find('#terms_privacy_signature').val();

					var updateAffiliateSignatureValue = $(document).find('#affiliate_terms_conditions_signature').val();

					formData.set('terms_privacy_signature', updateTermsPrivacySignatureValue);

					formData.set('affiliate_terms_conditions_signature', updateAffiliateSignatureValue);

					// Handle Ajax
					$.ajax('/ajax/travel-affiliate-members/registration', {
						data: formData,
						dataType: 'json',
						method: 'post',
						contentType: false,
						processData: false,
						async: true,
						beforeSend: function() {
							// Show Progress Bar
							$.ajax('/ajax/travel-affiliate-members/registration/progress-bar', {
								method: 'post',
								dataType: 'html',
								async: false,
								success: function(modal) {
									// Variable Defaults
									progressBar = $(modal);

									// Init Progress Bar
									progressBar.on('hidden.bs.modal', destroyModal).modal({
										backdrop: 'static',
										keyboard: false
									});
								}
							});
						},
						success: function(response) {
							// Hide Progress Bar
							progressBar.on('hidden.bs.modal', function() {
								// Switch Status
								switch (response.status) {
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
							}).modal('hide');
						},
						xhr: function() {
							var myXhr = $.ajaxSettings.xhr();

							if (myXhr.upload) {
								myXhr.upload.addEventListener('progress', function(event) {
									// Variable Defaults
									var progress = (event.loaded / event.total * 100).toFixed(0) + '%';

									// Update Progress Bar
									if (progressBar) {
										progressBar.find('.progress-bar').css('width', progress);
										progressBar.find('#progress-label').html(progress);
									}
								}, false);
							}

							return myXhr;
						}
					});
				} else {

					alert("Please view all required Terms & Conditions before submitting.");
				}
			});


		});

		//////////////////////////////////////////////////////////////////////////////////

		$(this.body).on("keyup", '#affiliate_terms_conditions_signature_modal',
			function(event) {
				// Get the current value of the input
				var currentValue = $(this).val();
				//console.log(currentValue)
				// Update the span#terms_privacy_signature_span with this value
				$(document).find('#affiliate_terms_conditions_signature_span').text(currentValue);

				$(document).find('#affiliate_terms_conditions_signature').val(currentValue);

				$(document).find("#affiliate_terms_conditions").removeAttr('disabled').prop('checked', true);
				$(document).find("#affiliate_terms_conditions").parent().addClass("checked-btn");
				$(document).find("#affiliate_terms_conditions").parent().parent().addClass("checked");
			});

		//////////////////////////////////////////////////////////////////////////////////

		$(this.body).on("keyup", '#terms_privacy_signature_modal',
			function(event) {
				// Get the current value of the input
				var currentValue = $(this).val();
				//console.log(currentValue)
				// Update the span#terms_privacy_signature_span with this value
				$(document).find('#terms_privacy_signature_span').text(currentValue);

				$(document).find('#terms_privacy_signature').val(currentValue);

				$(document).find("#terms_privacy").removeAttr('disabled').prop('checked', true);
				$(document).find("#terms_privacy").parent().addClass("checked-btn");
				$(document).find("#terms_privacy").parent().parent().addClass("checked");
			});

		//////////////////////////////////////////////////////////////////////////////////

		$(this.body).on("change", '#terms_privacy, #affiliate_terms_conditions',
			function(event) {

				const $this = $(this);

				if ($this.is(':checked')) {
					$this.parent().parent().addClass("checked");
				} else {
					$this.parent().parent().removeClass("checked");
				}

			});
		//////////////////////////////////////////////////////////////////////////////////
		// Insert Page Content for Terms & Privacy
		//////////////////////////////////////////////////////////////////////////////////

		$(this.body).on("click", '[data-target="#terms-privacy"]',
			function(event) {

				const $this = $(this);

				$.ajax({
					type: "post",
					url: "/ajax/pages/fetch-page-content-by-url",
					data: {
						page_slug: 'terms-privacy',
					},
					success: function(response) {
						// Handle success
						//console.log(response.data.content);
						$(document).find("#terms-privacy").find(".modal-body").html(response.data.content)


					},
					error: function(xhr, status, error) {
						// Handle error
						console.error(error);
					}
				});

			});
		//////////////////////////////////////////////////////////////////////////////////
		// Insert Page Content for Affiliate Terms of Use
		//////////////////////////////////////////////////////////////////////////////////

		$(this.body).on("click", '[data-target="#affiliate-terms-conditions"]',
			function(event) {

				const $this = $(this);

				$.ajax({
					type: "post",
					url: "/ajax/pages/fetch-page-content-by-url",
					data: {
						page_slug: 'affiliate-terms-conditions',
					},
					success: function(response) {
						// Handle success
						$(document).find("#affiliate-terms-conditions").find(".modal-body").html(response.data.content);

					},
					error: function(xhr, status, error) {
						// Handle error
						console.error(error);
					}
				});

			});
		//////////////////////////////////////////////////////////////////////////////////
	});

	jQuery(document).ready(function($) {

		$('#register-form').on('change', 'select[name="address_state"]', function() {

			// Variable Defaults
			var citySelect = $('#register-form').find('select[name="address_city"]');
			console.log(citySelect)
			// Handle Ajax
			$.ajax('/ajax/travel-affiliate-members/account/city-options', {
				data: {
					type: 'cities',
					sub_type: this.value
				},
				dataType: 'json',
				method: 'post',
				async: true,
				success: function(response) {
					// Switch Status
					switch (response.status) {
						case 'success':
							// Empty Values
							citySelect.children().not(':first').remove();

							// Append New Options
							Object.keys(response.options).forEach(function(value) {
								citySelect.append($('<option/>', {
									value: value,
									text: response.options[value],
									selected: citySelect.data('value') === value
								}));
							});

							// Check Default Value
							if (citySelect.val().length === 0) {
								citySelect.find('option').each(function() {
									// Check if the current option's value matches the default value
									if ($(this).val() === citySelect.data('default')) {
										// Set this option as selected
										$(this).prop('selected', true);

										// Break the loop since we found the match
										return false; // In jQuery each loop, return false is equivalent to breaking the loop
									}
								});
							}

							// Trigger Change
							citySelect.trigger('change');
							break;
						case 'error':
							displayMessage(response.message || Object.keys(response.errors).map(function(key) {
								let element = ajaxForm[0].querySelector(`[name="${key}"]`);
								let feedback = element?.parentElement.querySelector('.invalid-feedback');
								element?.setCustomValidity(response.errors[key]);

								if (feedback) {
									feedback.innerHTML = response.errors[key];
								} else {
									feedback = document.createElement('div');
									feedback.className = 'invalid-feedback';
									feedback.innerHTML = response.errors[key];
									element?.parentElement.appendChild(feedback);
								}

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
		}).find('select[name="address_state"]').trigger('change');
	});
</script>

<?php include('includes/body-close.php'); ?>