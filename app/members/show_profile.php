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
	 * @var Membership        $member
	 */
	
	// TODO: Persist modal like/report when closing modal
	// TODO: Fetch visibility level from relationship
	// TODO: Throw exception on initialization
	
	// Imports
	use Items\Collections;
	use Items\Enums\Options;
	use Items\Enums\Sizes;
	use Items\Enums\Statuses;
	use Items\Enums\Types;
	use Items\Members\Types as Member;
	
	try {
		// Variable Defaults
		$profile = Member\Profile::FromUsername($dispatcher->getOption('username'));
		$posts   = $member->getBlockStatus($profile)->is(Statuses\Block::NONE) ? new Collections\Posts(Database::Action("SELECT * FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `id` WHERE `type` = :type AND (`approved` = :approved OR :approved IS FALSE) AND (`visibility` = :visibility OR `member_id` = :member_id OR `member_id` IN (SELECT IF(`member_1` != :member_id, `member_1`, `member_2`) FROM `member_friends` WHERE :member_id IN (`member_1`, `member_2`))) AND `member_id` = :profile_id ORDER BY `timestamp` DESC", array(
			'type'       => Types\Post::SOCIAL->getValue(),
			'visibility' => Options\Visibility::MEMBERS->getValue(),
			'profile_id' => $profile->getId(),
			'member_id'  => $member->getId(),
			//'approved'   => $member->settings()->getValue('post_approval_required')
                'approved' => true, // or false depending on your site logic

		)), Types\Post::SOCIAL) : array();
		
		// Check Block
		if($member->getBlockStatus($profile)->is(Statuses\Block::BLOCKED) && $member->getBlockInitiatedBy($profile)) {
			throw new Exception('This profile is blocked.');
		}
	} catch(Exception $exception) {
		Render::ErrorDocument(404);
	}
	
	$currentFriendsRequest = $member?->getFriendsCount()[0];
	$subscriptions         = $member->getSubscriptionsID();
	
	$default_message = Database::Action("SELECT * FROM `member_default_message` WHERE `type` = :type", array('type' => $profile->isCouple() ? "COUPLES" : "SINGLES"))->fetchAll(PDO::FETCH_ASSOC);
	
	$verified = Database::Action("SELECT COUNT(`verified`) FROM `member_confirmation_friend_request` WHERE `initiated_by` = :initiated_by AND `verified` = 1 ", array('initiated_by' => $profile->getId()))->fetchColumn();
	
	$verifiedByMamber = Member\Friend::FetchAll(Database::Action("SELECT * FROM `member_confirmation_friend_request` AS mcfr JOIN `members` ON `members`.`id` = `member_id` WHERE `mcfr`.`initiated_by` = :initiated_by AND `mcfr`.`verified` = 1  GROUP BY `member_id`", array('initiated_by' => $profile->getId())));
	
	// Search Engine Optimization
	$page_title       = $profile->getTitle('Posts');
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<style>
	em-emoji-picker {
		margin-left: auto;
		margin-right: auto;
	}

	.verified-count {
		position: relative;
		top: -18px;
		left: -9px;
		border-radius: 50%;
		font-size: 12px;
		color: green;
	}

</style>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div id="profile" class="col" data-profile-id="<?php echo $profile->getId(); ?>">
				<div class="row mb-5">
					<div class="col-md-auto mb-4 text-center">
						<img class="profile-img d-block mx-auto" style="max-width: 300px;" src="<?php echo Items\Defaults::AVATAR_MD; ?>" data-src="<?php echo $profile->getAvatar()?->getImage(Sizes\Avatar::MD); ?>" alt="<?php echo $profile->getAlt('profile picture'); ?>">
						<?php echo $profile->isIdVerified() ? '<i class="fa-solid fa-badge-check" alt="ID Verified" title="ID Verified" style="font-size: 2rem;float: right;color: #005695;"></i>' : ""; ?>
						<?php if($profile->getId() === $member->getId()): ?>
							<?php if($member->subscription()?->isPaid()): ?>
								<button class="btn btn-primary mt-2" type="button" data-subscription-action="cancel" id="freePrivatePlan" data-subscriptionid="<?php echo $member->subscription()?->getSubscriptionId(); ?>">
									Switch to Free Private Plan
								</button>
							<?php else: ?>
								<button class="btn btn-primary mt-2" type="button" data-subscription-action="sign-up" id="upgradeToPremium">
									Upgrade to Premium Membership
								</button>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					
					<div class="col-md">
						<h1 class="title-underlined mb-4"><?php echo $profile->getUsername(); ?>
							
							<?php if($verified): ?>
								<i class="fa-solid fa-badge-check ml-2" style="color: #63E6BE; font-size: 24px;" <?php if($verified): ?> data-toggle="modal" data-target="#verifyByMember" <?php endif; ?>><span class="verified-count badge  "><?php echo $verified / 2; ?></span></i>
							<?php endif; ?>

							<div class="col-md">
								<button type="button" class="btn btn-primary float-lg-right" data-toggle="modal" data-target="#showQRcode" onClick="verificationQrCode(<?php echo $profile->getId(); ?>)">Generate QR Code</button>
							</div>
						</h1>
						<h2 class="text-truncate col-md"><?php echo $profile->getFirstNames(); ?>
						
						</h2>
						<div class="col-md float-lg-right">
							<?php
								if(!$member->isDisplayRsvps()):
									if($profile->getCheckIn()) :
										?>
										
										<i class="fa-duotone fa-solid fa-check fa-beat" style="--fa-primary-color: #008000; --fa-secondary-color: #008000;"></i> Checked-in event  <?php else : ?>
										<i class="fa-duotone fa-solid fa-check fa-beat" style="--fa-primary-color: red; --fa-secondary-color: red;"></i> Checked-out event
									
									<?php endif;
								endif; ?> </div>
						<?php echo $profile->getBio(); ?>
					</div>
					
					<?php if($profile->isCouple()) : ?>
						<div class="w-100"></div>
					<?php endif; ?>
					
					<div class="mt-4 col-xl-auto mt-xl-0">
						<div class="title-bar-trim-combo my-0 d-flex flex-column align-items-stretch h-100">
							<?php if($member->getBlockStatus($profile)->is(Statuses\Block::NONE)) : ?>
								<?php if($member->getId() != $profile->getId()) : ?>
									<div id="profile-toolbar-combo" class="toolbar-footer rounded-bottom">
										<?php if($member->getFriendStatus($profile)->is(Statuses\Friend::NONE, Statuses\Friend::DECLINED, Statuses\Friend::CANCELLED)) : ?>
											<?php if($currentFriendsRequest < 3 || count($subscriptions) > 0) : ?>
												<button class="toolbar__btn toolbar_btn-friend-request-send" type="button"
													
													data-toggle="modal" data-target="#send_friend_request"
													rel="tooltip-friend-request-send">
													<i class="fas fa-user-plus"></i>
												</button>
												
												<div class="toolbar__separator"></div>
											<?php else: ?>
												<button class="toolbar__btn toolbar_btn-friend-request-send" type="button"
													
													data-profile-action="send-friend-request-limt"
													rel="tooltip-friend-request-send">
													<i class="fas fa-user-plus"></i>
												</button>
												
												<div class="toolbar__separator"></div>
											<?php endif; ?>
										
										<?php endif; ?>
										
										<?php if($member->getFriendStatus($profile)->is(Statuses\Friend::PENDING)) : ?>
											<?php if($member->getFriendInitiatedBy($profile, $member)) : ?>
												<button class="toolbar__btn toolbar_btn-friend-request-cancel" type="button" data-profile-action="friend-request-cancel" rel="tooltip-friend-request-cancel">
													<i class="fas fa-user-minus"></i>
												</button>
												
												<div class="toolbar__separator"></div>
											<?php else : ?>
												<button class="toolbar__btn toolbar_btn-friend-request-accept" type="button" data-profile-action="friend-request-accept" rel="tooltip-friend-request-accept">
													<i class="fas fa-user-check"></i>
												</button>
												
												<div class="toolbar__separator"></div>
												
												<button class="toolbar__btn toolbar_btn-friend-request-decline" type="button" data-profile-action="friend-request-decline" rel="tooltip-friend-request-decline">
													<i class="fas fa-user-times"></i>
												</button>
												
												<div class="toolbar__separator"></div>
											<?php endif; ?>
										<?php endif; ?>
										
										<?php if($member->getFriendStatus($profile)->is(Statuses\Friend::APPROVED)) : ?>
											<button class="toolbar__btn toolbar_btn-friend-request-remove" type="button" data-profile-action="friend-request-remove" rel="tooltip-friend-request-remove">
												<i class="fas fa-user-minus"></i>
											</button>
											
											<div class="toolbar__separator"></div>
										<?php endif; ?>
										
										<button class="toolbar__btn toolbar_btn-block" type="button" data-profile-action="block" rel="tooltip-block">
											<i class="fas fa-user-slash"></i>
										</button>
										
										<div class="toolbar__separator"></div>
										<?php if($member->subscription()?->isPaid()): ?>
											<button class="toolbar__btn toolbar__btn-message" type="button" data-profile-action="message" rel="tooltip-message">
												<i class="fas fa-comment-alt-lines"></i>
											</button>
											
											<div class="toolbar__separator"></div>
										
										<?php else: ?>
											
											<div class="dropdown">
												<a href="#" class="toolbar__btn toolbar__btn-report" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" rel="tooltip-message">
													<i class="fas fa-comment-alt-lines"></i>
												</a>
												
												<div class="dropdown-menu">
													<h6 class="dropdown-header text-center py-0">Default Message</h6>
													<div class="dropdown-divider"></div>
													<?php foreach($default_message as $key => $val): ?>
														<a class="dropdown-item" href="#" onClick="sendDefaultMessage(<?php echo $profile->getId(); ?>, '<?php echo $val['message']; ?>')"><?php echo $val['message']; ?></a>
													<?php endforeach; ?>
													<a class="dropdown-item" href="#" data-custom-message-action="custom-message-suggestion" data-type="Abusive Language">Custom Message</a>
												</div>
											</div>
											<div class="toolbar__separator"></div>
										<?php endif; ?>
										<div class="dropdown">
											<a href="#" class="toolbar__btn toolbar__btn-report" aria-expanded="false" rel="tooltip-profile-report" onClick="showProfileReportModal(<?php echo $profile->getId(); ?>)">
												<i class="fas fa-exclamation-triangle"></i>
											</a>
											
											<div class="dropdown-menu">
												<h6 class="dropdown-header text-center py-0">Report Profile</h6>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Abusive Language">Abusive Language</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Criminal">Criminal</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Fake">Fake</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Fraudulent">Fraudulent</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Harassment">Harassment</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Hateful">Hateful</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Illegal">Illegal</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Inappropriate">Inappropriate</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Racist">Racist</a>
												<a class="dropdown-item" href="#" data-profile-action="report" data-type="Spam">Spam</a>
											</div>
										</div>
									</div>
								<?php endif; ?>
							<?php elseif($member->getBlockStatus($profile)->is(Statuses\Block::BLOCKED)) : ?>
								<div id="profile-toolbar-combo" class="toolbar-footer rounded-bottom">
									<button class="toolbar__btn toolbar_btn-friend-request-send" type="button" data-profile-action="friend-request-send" rel="tooltip-friend-request-send">
										<i class="fas fa-user-plus"></i>
									</button>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				
				<?php if(!empty($posts)) : ?>
					<hr class="mb-5">
					
					<div class="row posts">
						<?php foreach($posts as $post) : ?>
							<div class="col-md-6 col-xl-4 col-mb posts__child" data-post-id="<?php echo $post->getId(); ?>">
								<a href="#" data-post-action="modal">
									<img class="img-fluid m-0 border border-bottom-0 lazy" src="<?php echo Items\Defaults::SQUARE; ?>" alt="<?php echo $post->getMember()->getUsername(); ?>" data-src="<?php echo $post->getImage(); ?>">
								</a>
								
								<div class="trim m-0 rounded-0 border-bottom-0 equal-trim">
									<h4>
										<a href="#" data-post-action="modal">
											<?php echo $post->getTitle(); ?>
										</a>
									</h4>
									
									<p><?php echo $post->getContent(); ?></p>
									
									<p class="text-muted"><?php echo $post->getDate(); ?></p>
								</div>
								
								<div class="toolbar-footer rounded-bottom">
									<?php
										Render::Template('members/posts/toolbar/buttons/like.twig', array(
											'count'  => $post->likes()->count(),
											'active' => $post->likes()->lookup($member)
										));
									?>
									
									<div class="toolbar__separator"></div>
									
									<?php
										Render::Template('members/posts/toolbar/buttons/comment.twig', array(
											'action'     => 'modal',
											'active'     => $post->comments()->lookup($member),
											'collapse'   => FALSE,
											'count'      => $post->comments()->count(),
											'scrollable' => TRUE
										));
									?>
									
									<div class="toolbar__separator"></div>
									
									<?php
										Render::Template('members/posts/toolbar/buttons/report.twig', array(
											'active' => $post->reports()->lookup($member)
										));
									?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>
			</div>
		</div>
	</div>
</div>

<div id="emoji-modal" class="modal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body" style="background:none;"></div>
		</div>
	</div>
</div>

<script>
	$(function() {
		// Defer Scripts
		$.when(
			// Load Scripts
			$.getScript('/library/packages/emoji-mart/dist/browser.js'),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).done(function() {
			$('#emoji-modal').one('show.bs.modal', function() {
				$(this).find('.modal-body').append(new EmojiMart.Picker({
					onEmojiSelect: console.log
				}));
			});

			// Add Lifestyle Classes
			$('[data-necklace-color]').each(function() {
				// Switch Necklace Color
				switch($(this).data('necklace-color').toLowerCase()) {
					case 'green':
						$(this).addClass('text-success');
						break;
					case 'red':
						$(this).addClass('text-danger');
						break;
					default:
						console.error('Unknown necklace color.');
				}
			});
		});
	});

	// Bind Click Events to Subscriptions
	$('body').on('click', '[data-subscription-action]', function(event) {
		// Prevent Default
		event.preventDefault();

		// Prevent Multiple Fires
		if(event.detail && event.detail !== 1) return;

		// Variable Defaults

		var action = $('#freePrivatePlan').data('subscription-action');

		// Switch Action
		switch(action) {
			case 'cancel':
				// Handle Ajax
				$.ajax('/ajax/members/subscriptions/modals/cancel', {
					data: { id: $('#freePrivatePlan').data('subscriptionid') },
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
</script>

<?php include('includes/body-close.php'); ?>
<?php /*--||--------------------------------------------------------||------
----------||				<- Sample Modal Template ->				||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="verifyByMember" tabindex="-1" role="dialog" aria-labelledby="verifyByMember" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				
				<h3 class="modal-title">Verified By </h3>
			</div>
			<div class="modal-body">
				<?php foreach($verifiedByMamber as $verified): ?>
					
					<div class="trim rounded-0 rounded-top m-0 py-4 mb-3">
						<div class="row align-items-center">
							<div class="col-auto">
								<a href="<?php echo $verified->getLink(); ?>">
									<img class="profile-img-sm lazy" src="<?php echo Items\Defaults::AVATAR; ?>" alt="<?php echo $verified->getAlt('profile image'); ?>" data-src="<?php echo $verified->getAvatar()?->getImage(Sizes\Avatar::SM); ?>">
								</a>
							</div>
							
							<div class="col pl-1" style="min-width: 0;">
								<h4 class="mb-0">
									<a href="<?php echo $verified->getLink(); ?>">
										<b><?php echo $verified->getFirstNames(); ?></b>
									</a>
								</h4>
								<p class="text-truncate mb-0"><small>@<?php echo $verified->getUsername(); ?></small></p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="modal-footer">
			
			</div>
		</div>
	</div>
</div>