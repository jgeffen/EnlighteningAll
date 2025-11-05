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
	 */
	
	// Imports
	use Items\Enums\Requests;
	
	// Variable Defaults
	$member = Database::Action("SELECT * FROM `members` WHERE MD5(`email`) = :hash", array(
		'hash' => $dispatcher->getOption('hash')
	))->fetchObject(Membership::class) ?: NULL;
	
	// Attempt Email Validation
	$member?->account()->setAction(Requests\Account::VERIFY)->execute();
	
	// Search Engine Optimization
	$page_title       = "Verify Email";
	$page_description = "";
	
	// Page Variables
	$no_index = TRUE;
	
	use Items\Enums\Statuses;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
				<h1 class="sr-only">Email Verification</h1>
				
				<div class="title-bar-trim-combo">
					<div class="title-bar">
						<i class="fal fa-user-check"></i>
						<h2>Email Verification</h2>
					</div>
					
					<?php if(!is_null($member)): ?>
						<div class="trim p-sm-5">
							<p class="text-center">Great job! This email has been verified.</p>
							
							<hr class="mt-5 mb-3">
							
							<p class="text-center">
								<small class="text-muted">
									You will be redirected in <span data-redirect="/members/login">5</span>s.
								</small>
							</p>
							
							<?php
								// Has the member ever subscribed to THIS plan before?
								$free_trial_used = (int)Database::Action("SELECT COUNT(*) FROM `member_subscriptions` WHERE `member_id` = :member_id AND `subscription_id` = :subscription_id", array(
										'member_id'       => $member?->getId(),
										'subscription_id' => 25187
									))->fetchColumn() > 0;
								
								// Update Database
								$member_subscription_id = Database::Action("INSERT INTO `member_subscriptions` SET `status` = :status, `member_id` = :member_id, `subscription_id` = :subscription_id, `transaction_id` = :transaction_id, `date_start` = :date_start, `date_renewal` = :date_renewal, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
									'status'          => Statuses\Subscription::ACTIVE->getValue(),
									'member_id'       => $member->getId(),
									'subscription_id' => 25187,
									'date_start'      => date_create()->format('Y-m-d'),
									'date_renewal'    => !$free_trial_used ? date_create()->add(new DateInterval('P90D'))->format('Y-m-d') : Helpers::MonthShifter(date_create(), 1)->format('Y-m-d'),
									'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
									'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
								), TRUE);
								
								// Log Action
								$member->log()->setData(
									type       : Types\Log::CREATE,
									table_name : Tables\Members::SUBSCRIPTIONS,
									table_id   : $member_subscription_id
								)->execute();
							?>
						</div>
					<?php else: ?>
						<div class="trim p-sm-5">
							<p class="text-center">This Link Has Expired. Wait a few minutes and then try again.</p>
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
	});
</script>

<?php include('includes/body-close.php'); ?>

