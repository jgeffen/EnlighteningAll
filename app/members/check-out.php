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
	use Items\Enums\Sizes;
	
	// Check Checked-In
	if(is_null($member->getCheckIn())) {
		Helpers::Redirect('/members/walls/public');
	}
	
	// Search Engine Optimization
	$page_title       = "";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="sr-only">Check-Out</h1>
				<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 justify-content-center">
					<div class="col">
						<div class="card">
							<img class="card-img-top" src="<?php echo Items\Defaults::AVATAR_XL; ?>" data-src="<?php echo $member->getAvatar()?->getImage(Sizes\Avatar::XL, TRUE); ?>">
							<div class="card-body pt-3">
								<h2 class="card-title text-center mb-3"><?php echo $member->getFullName(); ?></h2>
								
								<div id="check-out-toolbar" class="text-center">
									<button type="button" class="btn btn-danger btn-lg" data-check-out-action="cancel">
										<i class="fa-regular fa-ban"></i> Cancel
									</button>
									
									<button type="button" class="btn btn-success btn-lg" data-check-out-action="confirm">
										<i class="fa-regular fa-arrow-up-left-from-circle"></i> Check-Out
									</button>
								</div>
							</div>
						</div>
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
		var wrapper = $('#check-out-toolbar');
		
		// Bind Click Functionality to Actions
		wrapper.on('click', 'button[data-check-out-action]', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Variable Defaults
			var action = $(this).data('check-out-action');
			
			// Switch Action
			switch(action) {
				case 'cancel':
					location.href = '/members/walls/public';
					break;
				
				case 'confirm':
					// Handle Ajax Request
					$.ajax('/ajax/members/check-out', {
						dataType: 'json',
						async: false,
						method: 'post',
						beforeSend: showLoader,
						complete: hideLoader,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Render Message
									wrapper.html($('<p/>', { text: response.message }));
									break;
								case 'error':
									displayMessage(response.message || Object.keys(response.errors).map(function(key) {
										return response.errors[key];
									}).join('<br>'), 'alert', function() {
										$(this).on('hide.bs.modal', function() {
											location.reload();
										});
									});
									break;
								default:
									displayMessage(response.message || 'Something went wrong.', 'alert', function() {
										$(this).on('hide.bs.modal', function() {
											location.reload();
										});
									});
							}
						}
					});
					break;
				
				default:
					console.log('Unknown Check-Out Action: ', action);
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
