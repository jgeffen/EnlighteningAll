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
	
	// TODO: Clear friend request notification on approval.
	
	// Imports
	use Items\Enums\Types;
	
	// Mark Notifications as Seen
	$member->notifications()->markSeen();
	
	// Search Engine Optimization
	$page_title       = $member->getTitle('Notifications');
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<?php if(!$member->notifications()->empty()): ?>
					<h1>Notifications</h1>
					
					<div class="dashboard-data-table table-responsive">
						<div class="resp-table-lg mb-4">
							<div class="row title-row">
								<div class="col-12 col-lg">
									<p>Date</p>
								</div>
								<div class="col-12 col-lg">
									<p id="notification__type">Type</p>
								</div>
								<div class="col-12 col-lg">
									<p id="notification__user">User</p>
								</div>
								<div class="col-12 col-lg">
									<p id="notification__event">Event</p>
								</div>
								<div class="col-12 col-lg">
									<p>Action</p>
								</div>
							</div>
							
							<?php foreach($member->notifications() as $notification): ?>
								<div class="row btn-reveal-trigger align-itmes-center" data-notify-id="<?php echo $notification->getId(); ?>">
									<div class="col-12 col-lg">
										<p><b><?php echo $notification->getTimestamp()->format('Y-m-d H:i:s'); ?></b></p>
									</div>
									
									<div class="col-12 col-lg">
										<p data-tabletitle="notification__type">
											<?php echo $notification->getType()->getLabel(); ?>
										</p>
									</div>
									
									<div class="col-12 col-lg">
										<p data-tabletitle="notification__user">
											<a href="<?php echo $notification->getMember()?->getLink(); ?>">
												<?php echo $notification->getMember()?->getUsername(); ?>
											</a>
										</p>
									</div>
									
									<div class="col-12 col-lg">
										<p data-tabletitle="notification__event"></p>
									</div>
									
									<div class="col-12 col-lg resp-custom">
										<div class="row no-gutters justify-content-center align-itmes-center" data-post-id="<?php echo $notification->getPostId(); ?>" data-profile-id="<?php echo $notification->getMember()?->getId(); ?>">
											<?php if($notification->getType()->is(Types\Notification::REQUEST)): ?>
												<div class="col-sm-6 col-lg-12 col-xl-6 pr-sm-1 pr-lg-0 pr-xl-1">
													<button class="btn btn-success btn-block btn-sm my-1" 
															type="button" 
															onclick="verifyFriendRequest(<?php echo $notification->getMember()?->getId(); ?>, <?php echo $member->getId(); ?>)" 
															
															>
														<i class="fas fa-user-check"></i> Accept
													</button>
												</div>
												
												<div class="col-sm-6 col-lg-12 col-xl-6 pl-sm-1 pl-lg-0 pl-xl-1">
													<button class="btn btn-danger btn-block btn-sm my-1" type="button" data-profile-action="friend-request-decline">
														<i class="fas fa-user-times"></i> Decline
													</button>
												</div>
											<?php elseif($notification->getPost()): ?>
												<div class="col-sm-6 col-lg-12 col-xl-6 pr-sm-1 pr-lg-0 pr-xl-1">
													<button class="btn btn-success btn-block btn-sm my-1" type="button" data-post-action="modal">
														<i class="fal fa-search"></i> View Post
													</button>
												</div>
												
												<div class="col-sm-6 col-lg-12 col-xl-6 pl-sm-1 pl-lg-0 pl-xl-1">
													<button class="btn btn-danger btn-block btn-sm my-1" type="button" data-notify-action="delete">
														<i class="fas fa-trash"></i> Delete
													</button>
												</div>
											<?php else: ?>
												<div class="col">
													<button class="btn btn-danger btn-block btn-sm my-1" type="button" data-notify-action="delete">
														<i class="fas fa-trash"></i> Delete
													</button>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else: ?>
					<div class="row justify-content-center">
						<div class="col-sm-10 col-md-7 col-lg-6 col-xl-5">
							<h3 class="text-center mb-0">You do not have any notifications.</h3>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>
