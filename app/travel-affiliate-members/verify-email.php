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
 */

// Imports
use Items\Enums\Requests;

// Variable Defaults
$member = Database::Action("SELECT * FROM `travel_affiliate_members` WHERE MD5(`email`) = :hash", array(
	'hash' => $dispatcher->getOption('hash')
))->fetchObject(TravelAffiliateMembership::class) ?: NULL;

// Attempt Email Validation
$member?->account()->setAction(Requests\Account::VERIFY)->execute();

// Search Engine Optimization
$page_title       = "Verify Email";
$page_description = "";

// Page Variables
$no_index = TRUE;

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

					<?php if (!is_null($member)) : ?>
						<div class="trim p-sm-5">
							<p class="text-center">Great job! This email has been verified.</p>

							<hr class="mt-5 mb-3">

							<p class="text-center">
								<small class="text-muted">
									You will be redirected in <span data-redirect="/travel-affiliate-members/login">5</span>s.
								</small>
							</p>
						</div>
					<?php else : ?>
						<div class="trim p-sm-5">
							<p class="text-center">Houston, we have a problem. It would seem someone has sent you a bad link.</p>
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
		var redirect = $('span[data-redirect]').first();
		var countdown = 5;

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
	});
</script>

<?php include('includes/body-close.php'); ?>