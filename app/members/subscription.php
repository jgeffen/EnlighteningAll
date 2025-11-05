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
	
	// Imports
	use Items\Collections;
	
	// Variable Defaults
	$subscriptions = new Collections\Subscriptions(Database::Action("SELECT * FROM `subscriptions` ORDER BY `position` DESC"));
	
	// Search Engine Optimization
	$page_title       = "My Subscription Benefits";
	$page_description = "My Subscription Benefits";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-4">My Subscription Benefits</h1>
				
				<?php foreach($subscriptions as $subscription): ?>
					<div id="<?php echo sprintf("subscription__wrapper-%s", $subscription->getId()); ?>" class="title-bar-trim-combo mt-5" data-id="<?php echo $subscription->getId(); ?>">
						<div class="title-bar">
							<?php echo $subscription->renderIcon('fa-light'); ?>
							<h2><?php echo $subscription->getName(); ?></h2>
						</div>
						
						<div class="form-wrap trim p-lg-4">
							<div class="mt-lg-2">
								<div class="row justify-content-center">
									<div class="col-lg-6">
										<h2 class="title-underlined mb-2">Plan Benefits</h2>
										
										<?php echo $subscription->getBenefits(); ?>
									</div>
									
									<div class="col-lg-6">
										<h2 class="title-underlined mb-2">Payment</h2>
										
										<?php if($member->subscription()?->getSubscriptionId() == $subscription->getId()): ?>
											<?php if($member->subscription()->getCancellationDate()): ?>
												<p class="text-danger d-flex align-items-center">
													<i class="fa-light fa-exclamation-triangle fa-lg mr-2"></i>
													<em>
														Your subscription will cancel on
														<span class="text-nowrap">
															<?php echo $member->subscription()->getCancellationDate()->format('n/j/y'); ?>
														</span>
													</em>
												</p>
											<?php else: ?>
												<p>
													Your next bill is for
													<b><?php echo $member->subscription()->getSubscription()->getPrice(TRUE); ?></b>
													+ tax on
													<b><?php echo $member->subscription()->getRenewalDate()->format('n/j/y'); ?></b>
												</p>
											<?php endif; ?>
											
											<?php if($member->wallet()): ?>
												<div class="d-flex flex-wrap align-items-center mb-3">
													<div class="col-auto p-0 mr-2">
														<i class="fa-solid fa-credit-card fa-2x" data-type="<?php echo $member->wallet()->getAccountType(); ?>"></i>
													</div>
													
													<div class="col-auto p-0" style="line-height:1;">
														Your card ending in <?php echo $member->wallet()->getAccountNumber(TRUE); ?>
														
														<br>
														
														<?php if(!$member->wallet()->isExpired()): ?>
															<small class="text-muted">
																Expires:
																<?php echo $member->wallet()->getExpirationDate()->format('m/Y'); ?>
															</small>
														<?php else: ?>
															<small class="text-danger">
																Expired:
																<?php echo $member->wallet()->getExpirationDate()->format('m/Y'); ?>
															</small>
														<?php endif; ?>
													</div>
													
													<div class="col-auto p-0">
														<a class="btn btn-link" href="/members/billing">
															Update
														</a>
													</div>
												</div>
											<?php else: ?>
												<div class="d-flex flex-wrap align-items-center mb-3">
													<div class="col-auto p-0 mr-2">
														<i class="fa-solid fa-credit-card fa-2x"></i>
													</div>
													
													<div class="col-auto p-0" style="line-height:1;">
														No Card on File
													</div>
													
													<div class="col-auto p-0">
														<a class="btn btn-link" href="/members/billing">
															Add Payment
														</a>
													</div>
												</div>
											<?php endif; ?>
										<?php else: ?>
											<?php echo $subscription->getContent(); ?>
											
											<?php if($subscription->getPrice()): ?>
												<p>All this for just <b><?php echo $subscription->getPrice(TRUE); ?></b> a month.</p>
												
												<?php if($member->wallet()): ?>
													<div class="d-flex flex-wrap align-items-center mb-3">
														<div class="col-auto p-0 mr-2">
															<i class="fa-solid fa-credit-card fa-2x" data-type="<?php echo $member->wallet()->getAccountType(); ?>"></i>
														</div>
														
														<div class="col-auto p-0" style="line-height:1;">
															Your card ending in <?php echo $member->wallet()->getAccountNumber(TRUE); ?>
															
															<br>
															
															<?php if(!$member->wallet()->isExpired()): ?>
																<small class="text-muted">
																	Expires:
																	<?php echo $member->wallet()->getExpirationDate()->format('m/Y'); ?>
																</small>
															<?php else: ?>
																<small class="text-danger">
																	Expired:
																	<?php echo $member->wallet()->getExpirationDate()->format('m/Y'); ?>
																</small>
															<?php endif; ?>
														</div>
														
														<div class="col-auto p-0">
															<a class="btn btn-link" href="/members/billing">
																Update
															</a>
														</div>
													</div>
												<?php else: ?>
													<div class="d-flex flex-wrap align-items-center mb-3">
														<div class="col-auto p-0 mr-2">
															<i class="fa-solid fa-credit-card fa-2x"></i>
														</div>
														
														<div class="col-auto p-0" style="line-height:1;">
															No Card on File
														</div>
														
														<div class="col-auto p-0">
															<a class="btn btn-link" href="/members/billing">
																Add Payment
															</a>
														</div>
													</div>
												<?php endif; ?>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
								
								<?php if($subscription->getPrice()): ?>
									<?php if($member->subscription()?->getSubscriptionId() == $subscription->getId()): ?>
										<?php if(!$member->subscription()->getCancellationDate() || $member->wallet()): ?>
											<hr class="mt-4">
											
											<div class="form-group row justify-content-center mt-4 mb-2">
												<div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
													<?php if($member->subscription()->getCancellationDate()): ?>
														<?php if($member->wallet()): ?>
															<button class="btn btn-block btn-primary" data-subscription-action="renew">
																<i class="fa-solid fa-arrows-rotate mr-2"></i>Renew Subscription
															</button>
														<?php endif; ?>
													<?php else: ?>
														<div class="text-center my-2">
															<small class="text-muted">Need to cancel your subscription? <a href="#" data-subscription-action="cancel">Click here</a></small>
														</div>
													<?php endif; ?>
												</div>
											</div>
										<?php endif; ?>
									<?php elseif($member->wallet()): ?>
										<?php if(!$member->wallet()->isExpired()): ?>
											<hr class="mt-4">
											
											<div class="form-group row justify-content-center mt-5 mb-3">
												<div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
													<button class="btn btn-block btn-primary" data-subscription-action="sign-up">
														<i class="fa-solid fa-user-plus mr-2"></i>Sign Me Up!
													</button>
												</div>
											</div>
										<?php endif; ?>
									<?php else: ?>
										<hr class="mt-4">
										
										<div class="form-group row justify-content-center mt-5 mb-2">
											<div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
												<button class="btn btn-block btn-primary disabled">
													<i class="fa-solid fa-user-plus mr-2"></i>Sign Me Up!
												</button>
												
												<div class="text-center my-2">
													<small class="text-muted"><a href="/members/billing">Click here</a> to add a payment method first.</small>
												</div>
											</div>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Swap Credit Card Icon
		(function(card_icon) {
			switch(card_icon.data('type')) {
				case 'Visa':
					card_icon.replaceWith('<i class="fa-brands fa-cc-visa fa-2x"></i>');
					break;
				case 'MasterCard':
					card_icon.replaceWith('<i class="fa-brands fa-cc-mastercard fa-2x"></i>');
					break;
				case 'Discover':
					card_icon.replaceWith('<i class="fa-brands fa-cc-discover fa-2x"></i>');
					break;
				case 'Amex':
					card_icon.replaceWith('<i class="fa-brands fa-cc-amex fa-2x"></i>');
					break;
			}
		})($('i.fa-solid.fa-credit-card'));
		
		// Bind Click Events to Subscriptions
		$('div[id^="subscription__wrapper"]').on('click', '[data-subscription-action]', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Prevent Multiple Fires
			if(event.detail && event.detail !== 1) return;
			
			// Variable Defaults
			var wrapper = $(event.delegateTarget);
			var action  = $(this).data('subscription-action');
			var data    = wrapper.data();
			
			// Switch Action
			switch(action) {
				case 'cancel':
					// Handle Ajax
					$.ajax('/ajax/members/subscriptions/modals/cancel', {
						data: { id: data.id },
						method: 'post',
						success: function(response) {
							// Check Response
							if(typeof response === 'object') {
								// Switch Status
								switch(response.status) {
									case 'error':
									default:
										displayMessage(response.message || 'Something went wrong.', 'alert');
								}
							} else {
								// Show Modal
								$(response).on('click', '[data-action]', function(event) {
									// Prevent Default
									event.preventDefault();
									
									// Variable Defaults
									var modal  = $(event.delegateTarget);
									var action = $(this).data('action');
									var data   = modal.data();
									
									// Switch Action
									switch(action) {
										case 'submit':
											// Handle Ajax
											$.ajax('/ajax/members/subscriptions/cancel', {
												data: { id: data.id },
												dataType: 'json',
												method: 'post',
												success: function(response) {
													// Switch Status
													switch(response.status) {
														case 'success':
															// Reload HTML
															wrapper.load(location.href + ' #' + wrapper.prop('id') + '>*', function() {
																// Close Modal
																modal.modal('hide');
															});
															break;
														case 'error':
														default:
															displayMessage(response.message || 'Something went wrong.', 'alert');
													}
												}
											});
											break;
										default:
											console.error('Unknown action', action);
									}
								}).on('hidden.bs.modal', destroyModal).modal();
							}
						}
					});
					break;
				case 'renew':
					// Handle Ajax
					$.ajax('/ajax/members/subscriptions/renew', {
						data: { id: data.id },
						dataType: 'json',
						method: 'post',
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Reload HTML
									wrapper.load(location.href + ' #' + wrapper.prop('id') + '>*');
									break;
								case 'error':
								default:
									displayMessage(response.message || 'Something went wrong.', 'alert');
							}
						}
					});
					break;
				case 'sign-up':
					// Handle Ajax
					$.ajax('/ajax/members/subscriptions/modals/sign-up', {
						data: { id: data.id },
						method: 'post',
						success: function(response) {
							// Check Response
							if(typeof response === 'object') {
								// Switch Status
								switch(response.status) {
									case 'error':
									default:
										displayMessage(response.message || 'Something went wrong.', 'alert');
								}
							} else {
								// Show Modal
								$(response).on('click', '[data-action]', function(event) {
									// Prevent Default
									event.preventDefault();
									
									// Variable Defaults
									var modal  = $(event.delegateTarget);
									var action = $(this).data('action');
									var data   = modal.data();
									
									// Switch Action
									switch(action) {
										case 'submit':
											// Handle Ajax
											$.ajax('/ajax/members/subscriptions/sign-up', {
												data: { id: data.id },
												dataType: 'json',
												method: 'post',
												beforeSend: showLoader,
												complete: hideLoader,
												async: false,
												success: function(response) {
													// Switch Status
													switch(response.status) {
														case 'success':
															// Reload HTML
															wrapper.load(location.href + ' #' + wrapper.prop('id') + '>*', function() {
																// Close Modal
																modal.modal('hide');
																
																// Display Message
																displayMessage(response.message, 'success');
															});
															break;
														case 'error':
														default:
															// Close Modal
															modal.modal('hide');
															
															// Display Message
															displayMessage(response.message || 'Something went wrong.', 'alert');
													}
												}
											});
											break;
										default:
											console.error('Unknown action', action);
									}
									
								}).on('hidden.bs.modal', destroyModal).modal();
							}
						}
					});
					break;
				default:
					console.error('Unknown action', action);
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
