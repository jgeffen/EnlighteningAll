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
	
	// Variable Defaults
	use AuthorizeNet\AIM\Client;
	$config = new Config\AuthorizeNet();
	
	// Search Engine Optimization
	$page_title       = "Payment Management";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-4">Payment Management</h1>
				
				<div id="billing-form__wrapper" class="title-bar-trim-combo" aria-label="Billing Form" role="form">
					<div class="title-bar">
						<i class="fa-light fa-credit-card"></i>
						<h2>Update Payment Method</h2>
					</div>
					
					<div id="billing-form" class="form-wrap trim p-lg-4" data-vault-id="<?php echo $member->wallet()?->getCustomerVaultId(); ?>">
						<form class="mt-lg-2">
							<h2 class="title-underlined mb-3">Card Holder</h2>
							
							<div class="form-group row justify-content-center">
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-first-name">First Name:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-first-name" class="form-control" type="text" name="first_name" placeholder="* Required" maxlength="50" value="<?php echo $member->wallet()?->getEncoded('billing_first_name'); ?>">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-last-name">Last Name:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-last-name" class="form-control" type="text" name="last_name" placeholder="* Required" maxlength="50" value="<?php echo $member->wallet()?->getEncoded('billing_last_name'); ?>">
										</div>
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-phone">Phone:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone" value="<?php echo $member->wallet()?->getEncoded('billing_phone'); ?>">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-email">Email:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255" value="<?php echo $member->wallet()?->getEncoded('billing_email'); ?>">
										</div>
									</div>
								</div>
							</div>
							
							<h2 class="title-underlined mb-2">Billing Address</h2>
							
							<div class="form-group row justify-content-center">
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-address-line-1">Address Line 1:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-address-line-1" class="form-control" type="text" name="address_line_1" placeholder="* Required" maxlength="255" value="<?php echo $member->wallet()?->getEncoded('billing_address_line_1'); ?>">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-address-line-2">Address Line 2:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-address-line-2" class="form-control" type="text" name="address_line_2" maxlength="255" value="<?php echo $member->wallet()?->getEncoded('billing_address_line_2'); ?>">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-address-city">City:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-address-city" class="form-control" type="text" name="address_city" placeholder="* Required" maxlength="255" value="<?php echo $member->wallet()?->getEncoded('billing_city'); ?>">
										</div>
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-select-address-state">State</label>
										<div class="col-lg-9">
											<div class="select-wrap form-control">
												<select id="billing-form-select-address-state" name="address_state" data-value="<?php echo $member->wallet()?->getBillingState(); ?>">
													<option value="">- Required -</option>
												</select>
												<div class="select-box"></div>
											</div>
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-select-address-country">Country</label>
										<div class="col-lg-9">
											<div class="select-wrap form-control">
												<select id="billing-form-select-address-country" name="address_country" data-value="<?php echo $member->wallet()?->getBillingCountry(); ?>">
													<?php foreach(Client::FormOptions('countries') as $value => $label): ?>
														<option value="<?php echo $value; ?>">
															<?php echo $label; ?>
														</option>
													<?php endforeach; ?>
												</select>
												<div class="select-box"></div>
											</div>
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-address-zip-code">Zip Code:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-address-zip-code" class="form-control" type="text" name="address_zip_code" placeholder="* Required" maxlength="255" data-format="zip" value="<?php echo $member->wallet()?->getBillingZipCode(); ?>">
										</div>
									</div>
								</div>
							</div>
							
							<h2 class="title-underlined mb-2">Credit Card</h2>
							
							<div class="form-group row justify-content-center">
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-select-cc-type">Card Type</label>
										<div class="col-lg-9">
											<div class="select-wrap form-control">
												<select id="billing-form-select-cc-type" name="cc_type" data-value="<?php echo $member->wallet()?->getAccountType(); ?>">
													<option value="">- Required -</option>
													<?php foreach(Client::FormOptions('credit_card_types') as $value => $label): ?>
														<option value="<?php echo $value; ?>">
															<?php echo $label; ?>
														</option>
													<?php endforeach; ?>
												</select>
												<div class="select-box"></div>
											</div>
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-cc-number">Credit Card #:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-cc-number" class="form-control" type="text" name="cc_number" placeholder="<?php echo $member->wallet()?->getAccountNumber(); ?>" aria-describedby="billing-form-input-cc-number-note" maxlength="255">
										</div>
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-select-cc-expiry">Exp. Date</label>
										<div class="col-lg-9">
											<div class="row">
												<div class="col-6">
													<div class="select-wrap form-control">
														<select id="billing-form-select-cc-expiry-month" name="cc_expiry_month" data-value="<?php echo $member->wallet()?->getExpirationDate()->format('m'); ?>">
															<option value="">- Required -</option>
															<?php foreach(Client::FormOptions('expiration_months') as $value => $label): ?>
																<option value="<?php echo $value; ?>">
																	<?php echo $label; ?>
																</option>
															<?php endforeach; ?>
														</select>
														<div class="select-box"></div>
													</div>
												</div>
												
												<div class="col-6">
													<div class="select-wrap form-control">
														<select id="billing-form-select-cc-expiry-year" name="cc_expiry_year" data-value="<?php echo $member->wallet()?->getExpirationDate()->format('Y'); ?>">
															<option value="">- Required -</option>
															<?php foreach(Client::FormOptions('expiration_years') as $value => $label): ?>
																<option value="<?php echo $value; ?>">
																	<?php echo $label; ?>
																</option>
															<?php endforeach; ?>
														</select>
														<div class="select-box"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="billing-form-input-cc-cvv">CVV2:</label>
										<div class="col-lg-9">
											<input id="billing-form-input-cc-cvv" class="form-control" type="text" name="cc_cvv" placeholder="* Required" maxlength="4">
										</div>
									</div>
								</div>
							</div>
							
							<hr class="mt-4 mb-5">
							
							<div class="form-group row justify-content-center">
								<div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
									<button id="billing-form-button-submit" type="submit" class="btn btn-block btn-primary submit-btn">
										<i class="fa-solid fa-lock-keyhole mr-2"></i>Update
									</button>
									
									<?php if($member->wallet()): ?>
										<div class="text-center my-2">
											<small class="text-muted">Need to delete this method of payment? <a href="#" data-billing-action="delete">Click here</a></small>
										</div>
									<?php else: ?>
										<div class="text-center my-2">
											<small class="text-muted"><b>Note:</b> Your card will not be charged until you agree to a subscription.</small>
										</div>
									<?php endif; ?>
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
		var ajaxForm = $('#billing-form');
		
		// Bind Change Event to Countries
		ajaxForm.on('change', 'select[name="address_country"]', function() {
			// Variable Defaults
			var stateSelect = ajaxForm.find('select[name="address_state"]');
			
			// Handle Ajax
			$.ajax('/ajax/options/mobiuspay', {
				data: { type: 'states', sub_type: this.value },
				dataType: 'json',
				method: 'post',
				async: true,
				success: function(response) {
					// Switch Status
					switch(response.status) {
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
							
							// Trigger Change
							stateSelect.trigger('change');
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
		}).find('select[name="address_country"]').trigger('change');
		
		// Handle Submission
		ajaxForm.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Handle Ajax
			$.ajax(!ajaxForm.data('vault-id') ? '/ajax/members/billing/authnet/add' : '/ajax/members/billing/authnet/update', {
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
							ajaxForm.replaceWith(response.html);
							
							// Scroll to Top
							$('html, body').animate({
								scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 30
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
		
		// Bind Click Events to Billing
		ajaxForm.on('click', '[data-billing-action]', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Variable Defaults
			var action = $(this).data('billing-action');
			
			// Switch Action
			switch(action) {
				case 'delete':
					// Confirm Deletion
					if(confirm('Are you sure you want to remove this method of payment?')) {
						$.ajax('/ajax/members/billing/authnet/delete', {
							dataType: 'json',
							async: false,
							method: 'post',
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
										displayMessage(response.message, 'alert', function() {
											$(this).on('shown.bs.modal', hideLoader);
										});
										break;
									case 'debug':
									default:
										displayMessage(response, 'alert', function() {
											$(this).on('shown.bs.modal', hideLoader);
										});
								}
							},
							error: function(xhr) {
								displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert', function() {
									$(this).on('shown.bs.modal', hideLoader);
								});
							}
						});
					}
					break;
				
				// Handle Ajax Request
				default:
					console.error('Unknown Action:', action);
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
