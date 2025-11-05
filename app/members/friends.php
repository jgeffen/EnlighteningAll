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
     //* @var \Items\Member $member
	 */

	// Imports
	use Items\Enums\Sizes;
	
	// Search Engine Optimization
	$page_title       = sprintf("%s's Friends", $member->getFirstNames());
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<style>
	div[data-toggle="buttons"] label.btn-secondary.active:hover {
		background-color: #545b62 !important;
		border-color: #4e555b !important;
	}
</style>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-5">
					Manage Your Friends (<?php echo $member->friends()->count(); ?>)
					
					<div id="friends-sorter" class="btn-group btn-group-toggle ml-auto" data-toggle="buttons">
						<label class="btn btn-secondary active">
							<input type="radio" name="sorter-button" value="recent" checked> <i class="fa-solid fa-timer text-white mr-0"></i>
						</label>
						
						<label class="btn btn-secondary">
							<input type="radio" name="sorter-button" value="a-z"> <i class="fa-solid fa-arrow-up-a-z text-white mr-0"></i>
						</label>
						
						<label class="btn btn-secondary">
							<input type="radio" name="sorter-button" value="z-a"> <i class="fa-solid fa-arrow-down-z-a text-white mr-0"></i>
						</label>
					</div>
				</h1>
				
				<?php if(empty($member->friends())): ?>
					<p>It doesn't look like you've added any friends, yet.</p>
				<?php else: ?>
					<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 friends-list">
						<?php foreach($member->friends() as $friend): ?>
							<div class="col col-mb friends-list__child" data-profile-id="<?php echo $friend->getId(); ?>" data-sort="<?php echo $friend->getSortJson(); ?>">
								<img class="img-fluid rounded-top border mb-0 lazy" src="/images/layout/default-square-thumb.jpg" data-src="<?php echo $friend->getAvatar()?->getImage(Sizes\Avatar::LG); ?>">
								
								<div class="trim rounded-0 m-0 py-4 border-bottom-0 text-center">
									<div class="row align-items-center">
										<div class="col pl-1" style="min-width: 0;">
											<h4 class="mb-0">
												<a href="<?php echo $friend->getLink(); ?>">
													<b><?php echo $friend->getFirstNames(); ?></b>
												</a>
											</h4>
											
											<p class="text-truncate mb-0"><?php echo sprintf('@%s', $friend->getUsername()); ?></p>
											
											<p class="text-truncate mb-0">
												<small>
													Friends since <?php echo $friend->getFriendSince()->format('M. jS, Y'); ?>
												</small>
											</p>
											
											<p class="text-truncate mb-0">
												<small>
													Last Online: <?php echo $friend->getLastOnline()?->format('M. j, Y, g:i a') ?? 'Never'; ?>
												</small>
											</p>
										</div>
									</div>
								</div>
								
								<div class="toolbar-footer rounded-bottom mx-auto" style="max-width: 450px;">
									<a class="toolbar__btn" type="button" href="<?php echo $friend->getLink(); ?>" rel="tooltip-view-profile">
										<i class="far fa-address-card"></i>
									</a>
									
									<div class="toolbar__separator"></div>
									
									<button class="toolbar__btn" type="button" data-profile-action="friend-request-remove" rel="tooltip-friend-request-remove">
										<i class="far fa-user-minus"></i>
									</button>
									
									<div class="toolbar__separator"></div>
									
									<button class="toolbar__btn" type="button" data-profile-action="block" rel="tooltip-block">
										<i class="far fa-user-slash"></i>
									</button>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				
				<div class="row justify-content-center">
					<div class="col-sm-10 col-md-7 col-lg-6 col-xl-5">
						<h3 class="text-center mb-0">You have no more friends to show.</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Bind Change to Friends Sorter
		$('#friends-sorter').on('change', 'input[name="sorter-button"]', function() {
			// Variable Defaults
			var sortOrder = $(this).val();
			
			// Sort Friends List
			$('.friends-list > .friends-list__child').sort(function(a, b) {
				// Variable Defaults
				var aData = JSON.parse(a.dataset.sort);
				var bData = JSON.parse(b.dataset.sort);
				
				// Switch Sort Order
				switch(sortOrder) {
					case 'a-z':
						return aData.first_names > bData.first_names;
					
					case 'z-a':
						return aData.first_names < bData.first_names;
					
					case 'recent':
						return aData.last_online !== bData.last_online
							? aData.last_online < bData.last_online
							: aData.first_names > bData.first_names;
				}
			}).appendTo('.friends-list');
		}).find('input[name="sorter-button"]:first').trigger('change');
	});
</script>

<?php include('includes/body-close.php'); ?>
