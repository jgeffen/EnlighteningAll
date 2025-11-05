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
	 * @var null|Membership   $member
	 */

	// Variable Defaults
	$item = Database::Action("SELECT * FROM `events` WHERE `published` IS TRUE AND `page_url` = :child_url AND `date_start` > CURDATE()", array(
		'child_url' => $dispatcher->getOption('child_url')
	))->fetchObject(Items\Event::class);

	// Get the referral ID
	if(isset($_GET['ref'])) {
		$referrer_id = (int)$_GET['ref'];

		// Skip if it's the same as the logged-in member
		if(empty($member) || $referrer_id !== $member->getId()) {
			// Save into session until checkout
			if(!empty($referrer_id) && (!isset($_SESSION['referrer_id']) || $_SESSION['referrer_id'] != $referrer_id)) {
				$_SESSION['referrer_id'] = $referrer_id;
			}
		}
	}

	// Check Item & Packages
	if(empty($item) || empty($item->getPackagesIds())) Render::ErrorDocument(404);

	// Search Engine Optimization
	$page_title       = sprintf("%s Pass Purchase Auth", $item->getTitle());
	$page_description = "";

	// Page Variables
	$no_index = TRUE;

	$now       = new DateTime('today');
	$threshold = (clone $now)->modify('+10 days');

	$start_date = $item->getStartDate() instanceof DateTime
		? $item->getStartDate()
		: new DateTime($item->getStartDate());

	// within the next 10 days, inclusive, and not past
	$discount_applies = ($start_date > $threshold) && $member->subscription()->isPaid();
	$discount_rate    = $discount_applies ? 0.10 : 0.00;

	// $4 flat discount for subscribed members only
	$flat_discount = 0.00;
	if(!empty($member) && $member->subscription()->isPaid()) {
		$flat_discount = 4.00;
	}

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
					<h1><?php echo $page_title; ?></h1>

					<div class="trim pt-4 pb-2">
						<div class="row">
							<div class="col-md-6">
								<p>
									<i class="fal fa-calendar-alt"></i>
                                    <b>Date:</b> To Be Announced<?php /* echo $item->getDate(); */?>
								</p>
							</div>

							<?php if($item->getLocation()) : ?>
								<div class="col-md-6">
									<p>
										<i class="fal fa-map-marked"></i>
										<b>Location:</b> <?php echo $item->getLocation(); ?>
									</p>
								</div>
							<?php endif; ?>

							<div class="col-md-6">
								<p>
									<i class="fa-solid fa-share"></i>
									<b>Share:</b>
									<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($item->getLink()); ?>"
										target="_blank"
										rel="noopener noreferrer"
										class="btn btn-sm btn-facebook">
										<i class="fab fa-facebook-f"></i>
									</a>

									<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($item->getLink()); ?>&text=<?php echo urlencode($item->getLink()); ?>"
										target="_blank"
										rel="noopener noreferrer"
										class="btn btn-sm btn-twitter">
										<i class="fab fa-x-twitter"></i>
									</a>
								</p>
							</div>
						</div>
					</div>

					<?php if($member?->reservations()->lookup($item)?->isPaid()) : ?>
						<div class="alert alert-danger text-center" role="alert">
							<p class="font-weight-bolder">You have already registered for this event.</p>
							<p class="mb-0">By continuing you are aware that you are <u>making an additional reservation.</u></p>
						</div>
					<?php endif; ?>

					<?php if($member): ?>
						<div class="alert alert-info">
							<label for="referral-link" class="form-label mb-1">Share this event with friends:</label>
							<div class="input-group">
								<input id="referral-link" type="text" class="form-control" readonly value="<?php echo SITE_URL; ?>/events/<?php echo $dispatcher->getOption('child_url'); ?>/purchase-pass-auth?ref=<?php echo $member->getId(); ?>">
								<button type="button" class="btn btn-primary" onclick="navigator.clipboard.writeText(document.getElementById('referral-link').value)">Copy Link</button>
							</div>
						</div>
					<?php endif; ?>

					<?php /* if($item->getImage()) : ?>
						<div class="lightbox mt-5">
							<a class="inset border mt-0 mt-sm-0 mt-md-1 mx-auto" href="<?php echo $item->getImage(); ?>">
								<img class="lazy" src="/images/layout/default-landscape.jpg" data-src="<?php echo $item->getImage(); ?>" alt="<?php echo $item->getAlt(); ?>">
							</a>
						</div>
					<?php endif; */ ?>

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

								<!--<div class="col-12 col-lg">
									<p id="package__stock">Tickets Left</p>
								</div> -->
							</div>

							<?php foreach($item->getPackages() as $package) : ?>
								<?php
								$base_price = $package->getPrice();
								// total discount (percent off + flat off)
								$total_discount = ($base_price * $discount_rate) + $flat_discount;

								// final price
								$final_price = max($base_price - $total_discount, 0);
								?>
								<?php if($package->getAvailableQuantity()): ?>
									<div class="row btn-reveal-trigger" data-package="<?php echo $package->toJson(JSON_HEX_QUOT); ?>">
										<div class="col-12 col-lg">
											<p data-tabletitle="package__name">
												<?php echo sprintf("%s - %s", $package->getPrice(TRUE), $package->getName()); ?>
											</p>
										</div>

										<input name="package_id" value="<?php echo $package->getId(); ?>" type="hidden"/>

										<div class="col-12 col-lg">
											<p data-tabletitle="package__price">
												<?php echo Helpers::FormatCurrency($package->getPrice()); ?>
												<br>
												<?php if($package->isBogo()): ?>
													Buy One Get One Free!
												<?php endif; ?>
											</p>
										</div>

										<div class="col-12 col-lg">
											<div data-tabletitle="package__qty">
												<div class="select-wrap form-control">
													<select name="<?php echo sprintf("event_packages[%d]", $package->getId()); ?>">
														<?php foreach(range(1, 1) as $value) : ?>
															<option><?php echo $value; ?></option>
														<?php endforeach; ?>
													</select>
													<div class="select-box"></div>
												</div>
											</div>
										</div>

										<!--<div class="col-12 col-lg">
											<p data-tabletitle="package__stock">
												<?php /* echo $package->getAvailableQuantity(); */ ?>
											</p>
										</div> -->
									</div>

									<div class="form-row justify-content-center">
										<div class="col-lg-6 text-center">
											<?php /* if(!$package->isBogo()): ?>
												<?php if($package->isSeatable()): ?>
													<?php if($seats = $package->getAvailableSeats()): ?>
														<?php $seat_id = sprintf('seat-number-%d', $package->getId()); ?>
														<div class="select-wrap form-control">
															<select id="<?php echo $seat_id; ?>" name="seat_number">
																<option value=""><strong>Pick A Seat</strong></option>
																<?php foreach($seats as $seat): ?>
																	<option value="<?php echo htmlspecialchars($seat, ENT_QUOTES); ?>">
																		<?php echo htmlspecialchars($seat, ENT_QUOTES); ?>
																	</option>
																<?php endforeach; ?>
															</select>

															<div class="select-box"></div>
														</div>
													<?php endif; ?>
												<?php endif; ?>
											<?php else: ?>
												<?php if($seats = $package->getAvailableSeats()): ?>
													<?php $seat_id = sprintf('seat-number-%d', $package->getId()); ?>
													<div class="form-group mt-3">
														<label for="<?php echo $seat_id; ?>"><strong>Pick Your Seats</strong></label>
														<div class="frame pb-4">
															<select id="<?php echo $seat_id; ?>" name="seat_number[]" class="seat-bogo-select" multiple>
																<?php foreach($seats as $seat): ?>
																	<option value="<?php echo $seat; ?>">
																		<?php echo $seat; ?>
																	</option>
																<?php endforeach; ?>
															</select>
														</div>
													</div>
												<?php endif; ?>
											<?php endif; */ ?>

											<?php if($package->isMusical()): ?>
												<div class="col-12 col-lg-6">
													<label for="purchase-pass-form-input-song-request" class="form-label">Song Request</label>
													<input id="purchase-pass-form-input-song-request" class="form-control w-100" placeholder="Enter Your Song Request" type="text" name="song_request" maxlength="50">
												</div>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</form>

					<div id="purchase-pass-form__wrapper" class="title-bar-trim-combo" aria-label="Purchase Pass Form" role="form">
						<div class="title-bar">
							<i class="fal fa-clipboard-list-check"></i>
							<h2>Purchase Pass Form</h2>
						</div>

						<div id="purchase-pass-form" class="form-wrap trim p-lg-4">
							<form class="mt-lg-2">
								<input type="hidden" name="event_id" value="<?php echo $item->getId(); ?>">

								<div class="row">
									<div class="col-lg-6">
										<div class="form-group row">
											<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-amount">Amount:</label>
											<div class="col-lg-9">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text" id="purchase-pass-form-addon-amount">
															<i class="fas fa-fw fa-dollar-sign"></i>
														</span>
														<input type="hidden" name="original_price" value="<?php echo $base_price; ?>">
														<input type="hidden" name="discount" value="<?php echo number_format($total_discount, 2, '.', ''); ?>">
														<input id="purchase-pass-form-input-amount" class="form-control" name="price" type="text" value="<?php echo number_format($final_price, 2, '.', ''); ?>" aria-describedby="purchase-pass-form-addon-amount" readonly>
													</div>
													<?php if($total_discount > 0): ?>
														<!-- <div class="alert alert-success my-3">
															<strong>Discount applied:</strong>
															<?php // echo sprintf("-$%0.2f off total for Subscribed Membership", $total_discount); ?>
														</div> -->
													<?php endif; ?>
												</div>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">
												Purchase Ticket for a Friend?
											</label>
											<div class="col-lg-9 pt-1">
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="purchase_for_friend" id="purchase-friend-no" value="0" checked>
													<label class="form-check-label" for="purchase-friend-no">No</label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="purchase_for_friend" id="purchase-friend-yes" value="1">
													<label class="form-check-label" for="purchase-friend-yes">Yes</label>
												</div>
											</div>
										</div>

										<div id="name-on-pass-wrapper" class="form-group row d-none">
											<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="name_on_pass">
												Name on Pass:
											</label>
											<div class="col-lg-9">
												<select id="name_on_pass" name="name_on_pass" class="form-control bg-light">
													<option value="<?php echo $member?->getId(); ?>" selected>
														<?php echo $member?->getFullName(); ?><?php echo $member ? ' (Me)' : ''; ?>
													</option>
													<?php if(!empty($member) && !empty($member->friends())): ?>
														<?php foreach($member->friends() as $friend): ?>
															<option value="<?php echo $friend->getId(); ?>"><?php echo $friend->getFullName(); ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
												<input type="hidden" id="name_on_pass_hidden" name="name_on_pass_hidden" value="<?php echo $member?->getId(); ?>">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-first-name">First Name:</label>
											<div class="col-lg-9">
												<input id="purchase-pass-form-input-first-name" class="form-control" type="text" name="first_name" placeholder="* Required" maxlength="50">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-last-name">Last Name:</label>
											<div class="col-lg-9">
												<input id="purchase-pass-form-input-last-name" class="form-control" type="text" name="last_name" placeholder="* Required" maxlength="50">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-phone">Phone:</label>
											<div class="col-lg-9">
												<input id="purchase-pass-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone">
											</div>
										</div>

										<div class="form-group row">
											<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-email">Email:</label>
											<div class="col-lg-9">
												<input id="purchase-pass-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255">
											</div>
										</div>

										<?php if($member?->wallet()->getPoints() >= $package->getPrice()): ?>
									</div>
									<div class="col-lg-6">
										<div class="alert alert-success text-center">
											<p>You have enough points to cover this package!</p>
											<p>
												<strong><?php echo $package->getPrice(); ?> points</strong> will be deducted from your wallet.
												<br>
												<small>(You must use points if your points are greater than or equal to the total amount.)</small>
											</p>
										</div>
										<input type="hidden" name="payment_method" value="points">

										<div class="form-group">
											<label class="col-form-label" for="purchase-pass-form-textarea-comments">Comments:</label>
											<textarea id="purchase-pass-form-textarea-comments" class="form-control" name="comments" rows="6"></textarea>
										</div>

										<div class="form-group">
											<div class="cap-wrap text-center">
												<fieldset>
													<label class="col-form-label" for="purchase-pass-form-captcha">Enter the Characters Shown Below</label>
													<input id="purchase-pass-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
												</fieldset>

												<noscript>
													<p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p>
												</noscript>
											</div>
										</div>

										<div class="form-group row justify-content-end">
											<div class="col-sm-7">
												<button id="purchase-pass-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
													Submit Reservation
												</button>
											</div>
										</div>
									</div>
									<?php else: ?>
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-address-line-1">Address Line 1:</label>
										<div class="col-lg-9">
											<input id="purchase-pass-form-input-address-line-1" class="form-control" type="text" name="address_line_1" placeholder="* Required" maxlength="255">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-address-line-2">Address Line 2:</label>
										<div class="col-lg-9">
											<input id="purchase-pass-form-input-address-line-2" class="form-control" type="text" name="address_line_2" maxlength="255">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-address-city">City:</label>
										<div class="col-lg-9">
											<input id="purchase-pass-form-input-address-city" class="form-control" type="text" name="address_city" placeholder="* Required" maxlength="255">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-select-address-state">State</label>
										<div class="col-lg-9">
											<div class="select-wrap form-control">
												<select id="purchase-pass-form-select-address-state" name="address_state">
													<option value="">- Required -</option>
												</select>
												<div class="select-box"></div>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-select-address-country">Country</label>
										<div class="col-lg-9">
											<div class="select-wrap form-control">
												<select id="purchase-pass-form-select-address-country" name="address_country">
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
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-address-zip-code">Zip Code:</label>
										<div class="col-lg-9">
											<input id="purchase-pass-form-input-address-zip-code" class="form-control" type="text" name="address_zip_code" placeholder="* Required" maxlength="255" data-format="zip">
										</div>
									</div>
								</div>

								<div class="col-lg-6">

									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-select-cc-type">Card Type</label>
										<div class="col-lg-9">
											<div class="select-wrap form-control">
												<select id="purchase-pass-form-select-cc-type" name="cc_type">
													<option value="">- Required -</option>
													<?php foreach(AuthorizeNet\AIM\Client::FormOptions('credit_card_types') as $value => $label) : ?>
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
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-cc-number">Credit Card #:</label>
										<div class="col-lg-9">
											<input id="purchase-pass-form-input-cc-number" class="form-control" type="text" name="cc_number" placeholder="* Required" maxlength="255">
										</div>

										<input type="hidden" name="payment_method" value="credit_card">
									</div>

									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-select-cc-expiry-month">Expiration Month</label>
										<div class="col-lg-9">
											<div class="select-wrap form-control">
												<select id="purchase-pass-form-select-cc-expiry-month" name="cc_expiry_month">
													<option value="">- Required -</option>
													<?php foreach(AuthorizeNet\AIM\Client::FormOptions('expiration_months') as $value => $label) : ?>
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
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-select-cc-expiry-year">Expiration Year</label>
										<div class="col-lg-9">
											<div class="select-wrap form-control">
												<select id="purchase-pass-form-select-cc-expiry-year" name="cc_expiry_year">
													<option value="">- Required -</option>
													<?php foreach(AuthorizeNet\AIM\Client::FormOptions('expiration_years') as $value => $label) : ?>
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
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-cc-cvv">CVV2:</label>
										<div class="col-lg-9">
											<input id="purchase-pass-form-input-cc-cvv" class="form-control" type="text" name="cc_cvv" placeholder="* Required" maxlength="4">
										</div>
									</div>

									<div class="form-group">
										<label class="col-form-label" for="purchase-pass-form-textarea-comments">Comments:</label>
										<textarea id="purchase-pass-form-textarea-comments" class="form-control" name="comments" rows="6"></textarea>
									</div>

									<div class="form-group">
										<div class="cap-wrap text-center">
											<fieldset>
												<label class="col-form-label" for="purchase-pass-form-captcha">Enter the Characters Shown Below</label>
												<input id="purchase-pass-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
											</fieldset>

											<noscript>
												<p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p>
											</noscript>
										</div>
									</div>

									<div class="form-group row justify-content-end">
										<div class="col-sm-7">
											<button id="purchase-pass-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
												Submit Reservation
											</button>
										</div>
									</div>
								</div>
								<?php endif; ?>
							</form>
						</div>
					</div>
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
			// Keep visible & optionally lock
			var mainCSS       = $('link[href^="/css/styles-main.min.css"]');
			var ajaxForm      = $('#purchase-pass-form');
			var captcha       = $('#purchase-pass-form-captcha');
			var packages      = $('select[name^="event_packages["][name$="]"]');
			const yes         = document.getElementById('purchase-friend-yes');
			const no          = document.getElementById('purchase-friend-no');
			const nameSelect  = document.getElementById('name_on_pass');
			const hiddenName  = document.getElementById('name_on_pass_hidden');
			const nameWrapper = document.getElementById('name-on-pass-wrapper');

			// 🔒 Force disable on page load if "No" is selected
			if(no && no.checked && nameSelect) {
				nameSelect.disabled = true;
				nameSelect.classList.add('bg-light');
				nameSelect.selectedIndex = 0;
				hiddenName.value         = nameSelect.value;
			}

			// Enforce BOGO seat limit
			$('select.seat-bogo-select').on('afterSelect', function(event, values) {
				var $select         = $(this);
				var maxSeats        = 2;
				var selectedOptions = $select.find('option:selected');

				if(selectedOptions.length > maxSeats) {
					var lastValue = values[values.length - 1];
					$select.multiSelect('deselect', lastValue);
					alert('You can only select ' + maxSeats + ' seats.');
				}
			});

			function syncNameOnPass() {
				if(!yes || !no || !nameSelect || !hiddenName || !nameWrapper) return;

				if(no.checked) {
					nameWrapper.classList.add('d-none'); // hide it entirely
					nameSelect.selectedIndex = 0;
					nameSelect.disabled      = true;
					hiddenName.value         = nameSelect.value;
				} else {
					nameWrapper.classList.remove('d-none'); // show when needed
					nameSelect.disabled = false;
					hiddenName.value    = '';
				}
			}

			yes && yes.addEventListener('change', syncNameOnPass);
			no && no.addEventListener('change', syncNameOnPass);

			// Mirror hidden field on submit if locked
			$('#purchase-pass-form form').on('submit', function() {
				if(nameSelect && nameSelect.disabled && hiddenName) {
					$(this).find('input[name="name_on_pass"]').remove();
					$('<input>', {
						type: 'hidden',
						name: 'name_on_pass',
						value: hiddenName.value
					}).appendTo(this);
				}
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
				$('div#purchase-pass-form__wrapper').dependsOn({
					selector: packages,
					value: Array.apply(null, {
						length: 10
					}).map(function(key, value) {
						return (value + 1).toString();
					}),
					wrapper: null
				});

				// Bind Change Event to Countries
				ajaxForm.on('change', 'select[name="address_country"]', function() {
					// Variable Defaults
					var stateSelect = ajaxForm.find('select[name="address_state"]');

					// Handle Ajax
					$.ajax('/ajax/options/authorizenet', {
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
					$.ajax('/ajax/events/packages/auth-purchase-pass', {
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

									//console.log("transactionResponse");
									//console.log(transactionResponse);

									$.ajax({
										type: 'post',
										url: '/ajax/events/packages/transaction-lookup',
										data: {
											transaction_id: transactionResponse.transaction_id
										},
										dataType: 'json',
										async: true,
										success: function(response) {

											//console.log("response.data");
											//console.log(response.data);

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

															//console.log("affiliateRequestData.affiliate_id");
															//console.log(affiliateRequestData.affiliate_id);
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

																			//console.log("affiliate-lookup response.data");
																			//console.log(response.data);

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