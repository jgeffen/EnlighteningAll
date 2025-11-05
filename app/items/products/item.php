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
	
	// Fetch/Set Item
	$item = Items\Product::Init($dispatcher->getRoute()?->getTableId());
	
	// right after $item is loaded
	$purchaseEndpoint = $item->getMerchant() == "MobiusPay" ? '/ajax/products/purchase' : '/ajax/products/purchase/auth';
	
	// Check Item
	if(is_null($item)) Render::ErrorDocument(404);
	
	$staff = Database::Action("SELECT id, first_name, last_name FROM `members` WHERE `is_staff` = 1 OR `teacher_approved` = 1 ORDER BY last_name ASC, first_name")->fetchAll(PDO::FETCH_ASSOC);
	
	// Search Engine Optimization
	$page_title       = $item->getTitle();
	$page_description = $item->getDescription();
	
	// Page Variables
	$top_image = $item->getLandscapeImage();
	
	// Start Header
	include('includes/header.php');
?>

<style>
	.flex-center {
		display: grid;
		gap: 1rem;
		justify-content: center;
	}

	@media (min-width: 768px) {
		.flex-center {
			display: flex;
		}
	}
</style>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $item->getHeading(); ?></h1>
				
				<?php echo $item->getContent(); ?>
				
				<?php if($item->getImage()) : ?>
					<div class="lightbox mt-5">
						<a class="inset border mt-0 mt-sm-0 mt-md-1 mx-auto" href="<?php echo $item->getImage(); ?>">
							<img class="lazy" src="/images/layout/default-landscape.jpg" data-src="<?php echo $item->getImage(); ?>" alt="<?php echo $item->getAlt(); ?>">
						</a>
					</div>
				<?php endif; ?>
				
				<hr class="clear my-5">
				
				<?php if(Membership::LoggedIn(FALSE)): ?>
					<div id="create-account-buttons" class="container">
						<div class="row">
							<div class="col flex-center">
								<a href="/members/register" target="_blank" type="button" class="create-member-account btn btn-primary">Create Member Account</a>
								<a href="/members/login?rel=<?php echo $_SERVER["REDIRECT_URL"]; ?>" type="button" class="member-login btn btn-primary">Login</a>
								<button type="button" class="continue-as-guest btn btn-secondary">Continue as Guest</button>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<form class="package-data-table table-responsive <?php echo Membership::LoggedIn(FALSE) ? "d-none" : ""; ?>" autocomplete="off">
					<div class="resp-table-lg mb-4">
						<div class="row title-row">
							<div class="col-12 col-lg">
								<p id="package__name">Package</p>
							</div>
							
							<div class="col-12 col-lg">
								<p id="package__price">Price</p>
							</div>
							
							<div class="col-12 col-lg">
								<p id="package__qty">Quantity</p>
							</div>
							
							<div class="col-12 col-lg">
								<p id="package__stock">Left in Stock</p>
							</div>
						</div>
						
						<?php if(!$item->isOutOfStock()): ?>
							<div class="row btn-reveal-trigger" data-package="<?php echo $item->toJson(JSON_HEX_QUOT); ?>">
								<div class="col-12 col-lg">
									<p data-tabletitle="package__name">
										<?php echo sprintf("%s - %s", $item->getPrice(TRUE), $item->getLabel()); ?>
									</p>
								</div>
								
								<input name="product_id" value="<?php echo $item->getId(); ?>" type="hidden"/>
								
								<div class="col-12 col-lg">
									<p data-tabletitle="package__price">
										<?php echo Helpers::FormatCurrency($item->getPrice()); ?>
									</p>
								</div>
								
								<div class="col-12 col-lg">
									<div data-tabletitle="package__qty">
										<div class="select-wrap form-control">
											<select name="product_quantity">
												<?php foreach(range(0, $item->getStockQuantity()) as $value) : ?>
													<option><?php echo $value; ?></option>
												<?php endforeach; ?>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
								</div>
								
								<div class="col-12 col-lg">
									<p data-tabletitle="package__stock" class="<?php echo ($item->getStockQuantity() < 6) ? 'text-danger fw-bold' : ''; ?>">
										<?php if($item->getStockQuantity() < 6): ?>
											Only&nbsp;<u><?php echo $item->getStockQuantity(); ?></u>&nbsp;Left!
										<?php else: ?>
											<?php echo $item->getStockQuantity(); ?>
										<?php endif; ?>
									</p>
								</div>
							</div>
						<?php else: ?>
							<h3 class="text-center mt-3">This item is temporarily unavailable. New stock is on the way, so check back shortly!</h3>
						<?php endif; ?>
					</div>
				</form>
				
				<?php if($item->getStockQuantity() > 0): ?>
					<div id="product-form__wrapper" class="title-bar-trim-combo <?php echo Membership::LoggedIn(FALSE) ? "d-none" : ""; ?>" aria-label="Purchase Pass Form" role="form">
						<div class="title-bar">
							<i class="fal fa-clipboard-list-check"></i>
							<h2>Purchase Pass Form</h2>
						</div>
						
						<div id="product-form" class="form-wrap trim p-lg-4" data-endpoint="<?php echo $purchaseEndpoint; ?>">
							<form class="mt-lg-2">
								<input type="hidden" name="event_id" value="<?php echo $item->getId(); ?>">
								<div id="product-form-quantity" data-quantity="<?php echo $item->getStockQuantity(); ?>">
									<div class="row">
										<div class="col-lg-6">
											<?php if($item->isTip()): ?>
												<div class="form-group row">
													<label for="staff_id" class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">
														Staff/Teacher
													</label>
													<div class="col-lg-9">
														<select name="staff_id" id="staff_id" class="form-control">
															<option value="">Search Staff & Teachers</option>
															<?php foreach($staff as $row): ?>
																<option value="<?php echo (int) $row['id']; ?>">
																	<?php echo trim($row['last_name'] . ', ' . $row['first_name']); ?>
																</option>
															<?php endforeach; ?>
														</select>
													</div>
												</div>
												<hr>
											<?php endif; ?>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-amount">Amount:</label>
												<div class="col-lg-9">
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text" id="product-form-addon-amount">
																<i class="fas fa-fw fa-dollar-sign"></i>
															</span>
														</div>
														<input id="product-form-input-amount" class="form-control" type="text" value="0.00" aria-describedby="product-form-addon-amount" readonly>
													</div>
												</div>
											</div>
											
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-first-name">First Name:</label>
												<div class="col-lg-9">
													<input id="product-form-input-first-name" class="form-control" type="text" name="first_name" placeholder="* Required" maxlength="50">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-last-name">Last Name:</label>
												<div class="col-lg-9">
													<input id="product-form-input-last-name" class="form-control" type="text" name="last_name" placeholder="* Required" maxlength="50">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-phone">Phone:</label>
												<div class="col-lg-9">
													<input id="product-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-email">Email:</label>
												<div class="col-lg-9">
													<input id="product-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-address-line-1">Address Line 1:</label>
												<div class="col-lg-9">
													<input id="product-form-input-address-line-1" class="form-control" type="text" name="address_line_1" placeholder="* Required" maxlength="255">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-address-line-2">Address Line 2:</label>
												<div class="col-lg-9">
													<input id="product-form-input-address-line-2" class="form-control" type="text" name="address_line_2" maxlength="255">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-address-city">City:</label>
												<div class="col-lg-9">
													<input id="product-form-input-address-city" class="form-control" type="text" name="address_city" placeholder="* Required" maxlength="255">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-select-address-state">State</label>
												<div class="col-lg-9">
													<div class="select-wrap form-control">
														<select id="product-form-select-address-state" name="address_state">
															<option value="">- Required -</option>
														</select>
														<div class="select-box"></div>
													</div>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-select-address-country">Country</label>
												<div class="col-lg-9">
													<div class="select-wrap form-control">
														<select id="product-form-select-address-country" name="address_country">
															<?php foreach(MobiusPay\Client::FormOptions('countries') as $value => $label) : ?>
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
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-address-zip-code">Zip Code:</label>
												<div class="col-lg-9">
													<input id="product-form-input-address-zip-code" class="form-control" type="text" name="address_zip_code" placeholder="* Required" maxlength="255" data-format="zip">
												</div>
											</div>
										</div>
										
										<div class="col-lg-6">
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-select-cc-type">Card Type</label>
												<div class="col-lg-9">
													<div class="select-wrap form-control">
														<select id="product-form-select-cc-type" name="cc_type">
															<option value="">- Required -</option>
															<?php foreach(MobiusPay\Client::FormOptions('credit_card_types') as $value => $label) : ?>
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
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-cc-number">Credit Card #:</label>
												<div class="col-lg-9">
													<input id="product-form-input-cc-number" class="form-control" type="text" name="cc_number" placeholder="* Required" maxlength="255">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-select-cc-expiry-month">Expiration Month</label>
												<div class="col-lg-9">
													<div class="select-wrap form-control">
														<select id="product-form-select-cc-expiry-month" name="cc_expiry_month">
															<option value="">- Required -</option>
															<?php foreach(MobiusPay\Client::FormOptions('expiration_months') as $value => $label) : ?>
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
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-select-cc-expiry-year">Expiration Year</label>
												<div class="col-lg-9">
													<div class="select-wrap form-control">
														<select id="product-form-select-cc-expiry-year" name="cc_expiry_year">
															<option value="">- Required -</option>
															<?php foreach(MobiusPay\Client::FormOptions('expiration_years') as $value => $label) : ?>
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
												<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="product-form-input-cc-cvv">CVV2:</label>
												<div class="col-lg-9">
													<input id="product-form-input-cc-cvv" class="form-control" type="text" name="cc_cvv" placeholder="* Required" maxlength="4">
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-form-label" for="product-form-textarea-comments">Comments:</label>
												<textarea id="product-form-textarea-comments" class="form-control" name="comments" rows="6"></textarea>
											</div>
											
											<div class="form-group">
												<div class="cap-wrap text-center">
													<fieldset>
														<label class="col-form-label" for="product-form-captcha">Enter the Characters Shown Below</label>
														<input id="product-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
													</fieldset>
													
													<noscript>
														<p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p>
													</noscript>
												</div>
											</div>
											
											<div class="form-group row justify-content-end">
												<div class="col-sm-7">
													<button id="product-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
														Submit Reservation
													</button>
												</div>
											</div>
										</div>
									</div>
							</form>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php if(Membership::LoggedIn(FALSE)): ?>
	
	<script>
		$(function() {

			var checkInterval = setInterval(function() {
				$.ajax({
					url: '/ajax/members/member-logged-in',  // URL to the PHP script
					type: 'post',  // GET or POST depending on your preference
					dataType: 'json',  // Expect JSON in response
					success: function(data) {
						if(data.loggedIn) {

							console.log('User is logged in');

							$(document).find('.package-data-table').removeClass('d-none');
							$(document).find('#create-account-buttons').remove();

							clearInterval(checkInterval);
							// Handle logged-in status, e.g., redirect or display content
						} else {
							console.log(data.loggedIn);

							// Handle not logged-in status, e.g., redirect to login page
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log('Error fetching login status:', textStatus);
					}
				});
			}, 5000);

			$(document).on('click', '.continue-as-guest', function(event) {

				event.preventDefault();

				$(document).find('.package-data-table').removeClass('d-none');

				$(document).find('#create-account-buttons').remove();

			});
		});
	</script>

<?php endif; ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#product-form');
		var captcha  = $('#product-form-captcha');
		var packages = $('select[name="product_quantity"]');
		var endpoint = $('#product-form').data('endpoint') || '/ajax/products/purchase';
		var quantity = $('#product-form-quantity').data('quantity');


		$('#staff_id').select2({
			placeholder: 'Search for a referrer',
			allowClear: true
		});
		// Init Scripts
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
			$.ajax('/library/packages/accounting/accounting.min.js', {
				async: false,
				dataType: 'script'
			}),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Init Captcha
			captcha.realperson();

			// Init Depends On
			$('div#product-form__wrapper').dependsOn({
				selector: packages,
				value: Array.apply(null, {
					length: quantity
				}).map(function(key, value) {
					return (value + 1).toString();
				}),
				wrapper: null
			});

			// Bind Change Event to Package Quantities
			packages.on('change', function() {
				// Update Amount Input
				$('#product-form-input-amount').val(accounting.formatNumber(packages.toArray().map(function(element) {
					// Variable Defaults
					var price    = ($(element).parents('div[data-package]').data('package').price).toFixed(2);
					var quantity = $(element).val();

					return price * quantity;
				}).reduce(function(total, value) {
					return total + value;
				}, 0), 2));
			}).trigger('change');

			// Bind Change Event to Countries
			ajaxForm.on('change', 'select[name="address_country"]', function() {
				// Variable Defaults
				var stateSelect = ajaxForm.find('select[name="address_state"]');

				// Handle Ajax
				$.ajax('/ajax/options/mobiuspay', {
					data: {
						type: 'states',
						sub_type: this.value
					},
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
										text: response.options[value]
									}));
								});
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
						//displayMessage(xhr.status + ': ' + xhr.statusText + ' - ' + this.url, 'alert');
					}
				});
			}).find('select[name="address_country"]').trigger('change');

			// Handle Submission
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();

				var requestData = Object.assign($(this).serializeObject(), packages.parents('.package-data-table').serializeObject());

				// Handle Ajax
				$.ajax(endpoint, {
					data: requestData,
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

								// Remove Package Selections
								packages.parents('.package-data-table').remove();

								var transactionResponse = response;

								$.ajax({
									type: 'post',
									url: '/ajax/events/packages/transaction-lookup',
									data: {
										transaction_id: transactionResponse.transaction_id
									},
									dataType: 'json',
									async: true,
									success: function(response) {

										// Switch Status
										switch(response.status) {
											case 'success':

												switch(response.data.payment_status) {
													case 'Approved':
														var affiliateRequestData = {
															affiliate_id: getAffiliateCookieValue(),
															transaction_id: response.data.transaction_id,
															amount: response.data.amount,
															purchaser_social_member_id: response.data.member_id,
															purchaser_email: response.data.billing_email,
															transactions_table_id: response.data.id,
															confirmed_payment: response.data.payment_status,
															type_table_name: response.data.table_name,
															type_table_id: response.data.table_id
														};
														$.ajax({

															type: 'post',
															url: '/ajax/events/packages/affiliate-lookup',
															data: {
																id: affiliateRequestData.affiliate_id
															},
															dataType: 'json',
															async: true,
															success: function(response) {
																// Switch Status
																switch(response.status) {
																	case 'success':

																		affiliateRequestData.ticket_commission_rate = response.data.ticket_commission_rate;

																		$.ajax({
																			type: 'post',
																			url: '/ajax/events/packages/update-affiliate-transaction',
																			data: affiliateRequestData,
																			dataType: 'json',
																			async: true,
																			success: function(response) {
																				// Switch Status
																				switch(response.status) {
																					case 'success':
																						console.log('Successfully Updated the table affiliate_transations');
																						document.cookie = 'AffiliateEventCookie=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
																					case 'error':
																						console.log('affiliate_transations table not updated');

																				}
																			},
																			error: function(xhr, status, error) {
																				console.log('error' + status);

																			}
																		});
																		break;
																	case 'error':
																		console.log('Affiliate ID is not a registered member');
																		break;

																}
															},
															error: function(xhr, status, error) {
																console.log('error' + status);

															}
														});

														break;
													case 'Errored':
													case 'Declined':
														//Invert this for Production, testing on decline status only
														console.error('Transaction Payment Status Declined or Errored');

														break;

												}

												break;
											case 'error':
												console.error('Transaction Lookup Failed');
											default:
												console.error('Transaction Lookup Failed');
										}
									},
									error: function(xhr, status, error) {
										console.log('error' + status);

									}
								});

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

		function getAffiliateCookieValue() {
			let nameEQ = 'AffiliateEventCookie=';
			let ca     = document.cookie.split(';');
			for(let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while(c.charAt(0) == ' ') c = c.substring(1, c.length);
				if(c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
			}
			return null; // Return null if the cookie was not found
		}
	});
</script>

<?php include('includes/body-close.php'); ?>
