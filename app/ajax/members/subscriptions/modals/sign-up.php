<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	try {
		// Check Logged In
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Set Member
		$member = new Membership();
		$subscription = Database::Action("SELECT * FROM `subscriptions` WHERE `id` = :id", array(
			'id' => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
		))->fetchObject(Items\Subscription::class);
		
		// Check Subscription
		if(!$subscription) throw new Exception('Subscription not found in database. Please refresh your page.');
		
		// Check Active Subscription
		if($member->subscription()?->getSubscriptionId() == $subscription->getId()) throw new Exception('This subscription is already active on your account.');
		
		// Check Wallet
		if(!$member->wallet()) throw new Exception('No method of payment found for this account.');
		
		// Check Expiration
		if($member->wallet()->isExpired()) throw new Exception('Your card has expired. Please update your method of payment.');
	} catch(Exception $exception) {
		// Set Response
		echo json_encode(array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		));
		exit;
	}
?>

<div id="ajax-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="<?php echo $subscription->getId(); ?>">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<?php echo sprintf("<i class=\"fa-light %s\" aria-hidden=\"true\"></i>", $subscription->getIcon()); ?>
				<h2 class="modal-title"><?php echo $subscription->getName(); ?></h2>
			</div>
			
			<div class="modal-body">
				<p>
					By clicking "I Agree" you agree to the
					<a href="/terms-privacy.html#terms" target="_blank">terms</a> of this site.
					Your subscription includes a 90-day free trial.
					Unless you cancel before the end of the trial, your method of payment will
					automatically be charged <?php echo $subscription->getPrice(TRUE); ?> per month
					beginning on the 91st day and continuing each month thereafter.
					You may cancel your subscription at any time.
				</p>
			</div>
			
			<div class="modal-footer">
				<div class="mx-auto">
					<button type="button" class="btn btn-outline" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" data-action="submit">I Agree</button>
				</div>
			</div>
		</div>
	</div>
</div>
