<?php
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Fetch tips
	$tips = Database::Action("SELECT * FROM `transactions` WHERE `member_id` = :member_id AND `type` = 'tips' ORDER BY `timestamp` DESC", array(
		'member_id' => $member->getId()
	))->fetchAll(PDO::FETCH_ASSOC);
	
	// Total tips accrued
	$totalTips = Database::Action("SELECT SUM(`amount`) FROM `transactions` WHERE `member_id` = :member_id AND `type` = 'tips'", array(
		'member_id' => $member->getId()
	))->fetchColumn();
	
	// SEO
	$page_title       = "";
	$page_description = "";
	
	include('includes/header.php');
?>
<div class="container-fluid main-content">
	<div class="container">
		<div class="row mb-4">
			<div class="col-md-6">
				<?php if($member->wallet()->getPoints() > 0): ?>
					<h2><b style="color: black;">My Points Total:</b> <?php echo $member->wallet()->getPoints(); ?></h2>
				<?php endif; ?>
			</div>
			<div class="col-md-6 text-md-end">
				<h2><b style="color: black;">Total Tips Accrued:</b> <?php echo Helpers::FormatCurrency((float)$totalTips); ?></h2>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
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
			</div>
		</div>
		
		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
			<?php foreach($tips as $tip): ?>
				<?php
				$isPaidOut   = (int)$tip['paid_out'] === 1;
				$cardClass   = $isPaidOut ? 'bg-success text-white' : '';
				$paidOutText = $isPaidOut ? 'Yes' : 'No';
				?>
				<div class="col">
					<div class="card shadow-sm h-100 <?php echo $cardClass; ?>">
						<div class="card-body d-flex flex-column">
							<h5 class="card-title text-center mb-2">
								Tips
							</h5>
							
							<ul class="list-unstyled small mb-3">
								<?php if(!empty($tip['payment_status'])): ?>
									<li><strong>Status:</strong> <?php echo $tip['payment_status']; ?></li>
								<?php endif; ?>
								
								<li><strong>Amount:</strong> <?php echo Helpers::FormatCurrency((float)$tip['amount']); ?></li>
								
								<?php if(!empty($tip['invoice'])): ?>
									<li><strong>Invoice:</strong> <?php echo $tip['invoice']; ?></li>
								<?php endif; ?>
								
								<li><strong>Paid Out:</strong> <?php echo $paidOutText; ?></li>
								
								<?php if(!empty($tip['timestamp'])): ?>
									<li><strong>Timestamp:</strong>
										<?php echo date('Y-m-d H:i:s', strtotime($tip['timestamp'])); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
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
