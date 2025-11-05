<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/
	
	try {
		// Check Logged In
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Set Member
		$member       = new Membership();
		$subscription = Items\Subscription::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Subscription
		if(is_null($subscription)) throw new Exception('Subscription not found in database. Please refresh your page.');
		
		// Check Active Subscription
		if($member->subscription()->getSubscriptionId() != $subscription->getId()) throw new Exception('This subscription is not active on your account.');
		
		// Check Cancellation
		if($member->subscription()->getCancellationDate()) {
			throw new Exception(sprintf("This subscription will already cancel on %s", $member->subscription()->getCancellationDate()->format('n/j/y')));
		}
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
				<?php echo $subscription->renderIcon('fa-light'); ?>
				<h2 class="modal-title"><?php echo $subscription->getName(); ?></h2>
			</div>
			
			<div class="modal-body">
				<p>Are you certain you would like to cancel this subscription? You'll keep all the benefits until your subscription is cancelled on the next billing date.</p>
			</div>
			
			<div class="modal-footer">
				<div class="mx-auto">
					<button type="button" class="btn btn-outline" data-dismiss="modal">I've changed my mind</button>
					<button type="button" class="btn btn-primary" data-action="submit">Yes, cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>
