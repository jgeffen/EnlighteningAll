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
	
	// TODO: Persist modal like/report when closing modal
	// TODO: Fetch visibility level from relationship
	
	// Imports
	use Items\Enums\Options;
	use Items\Enums\Sizes;
	use Items\Enums\Statuses;
	use Items\Members\Types as Member;
	use Items\Members\Posts\Types as Post;
	
	try {
		// Variable Defaults
		$profile = Member\Profile::FromUsername($dispatcher->getOption('username'));
		$post    = Post\Social::FromHash($dispatcher->getOption('post_id_hash'), $profile);
		
		// Check Block
		if($member->getBlockStatus($profile)->is(Statuses\Block::BLOCKED)) {
			throw new Exception('This profile is blocked.');
		}
		
		// Check Approval
		if($member->settings()->getValue('post_approval_required') && !$post->isApproved()) throw new Exception('This post is awaiting approval.');
		
		// Check Visibility
		if($post->getVisibility()->is(Options\Visibility::FRIENDS)) {
			// Check Ownership
			if($member->getId() != $profile->getId()) {
				// Check Friend Status
				if(!$member->getFriendStatus($profile)->is(Statuses\Friend::APPROVED)) {
					throw new Exception('This post is for friends only.');
				}
			}
		}
	} catch(Exception) {
		Render::ErrorDocument(404);
	}
	
	// Search Engine Optimization
	$page_title       = $post->getTitle();
	$page_description = $post->getDescription();
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div data-post-id="<?php echo $post->getId(); ?>">
			<div class="trim rounded-0 rounded-top border-bottom-0 border-lg-bottom m-0">
				<div class="row align-items-end mb-3">
					<div class="col-lg-6 col-xl-8">
						<h1 class="mb-0">
							<?php echo $post->getTitle(); ?>
						</h1>
						<hr class="d-lg-none my-3">
					</div>
					
					<div class="col-lg-6 col-xl-4 mb-3 mb-lg-0">
						<div class="row align-items-center">
							<div class="col-auto order-lg-last">
								<a href="<?php echo $profile->getLink(); ?>">
									<img class="profile-img-xs" src="<?php echo Items\Defaults::AVATAR; ?>" data-src="<?php echo $profile->getAvatar()?->getImage(Sizes\Avatar::SM); ?>">
								</a>
							</div>
							
							<div class="col pr-lg-1 pl-1 pl-lg-3 text-lg-right order-lg-first" style="min-width: 0;">
								<h4 class="mb-0">
									<a href="<?php echo $profile->getLink(); ?>">
										<b><?php echo $profile->getFirstNames(); ?></b>
									</a>
								</h4>
								<p class="text-truncate mb-0"><small>@<?php echo $profile->getUsername(); ?></small></p>
							</div>
						</div>
					</div>
				</div>
				
				<hr class="d-none d-lg-block mt-0 mt-lg-3">
				
				<?php if($post->hasImage()): ?>
					<div class="float-lg-right mt-0 mt-md-2 mb-3 col-lg-4">
						<a class="d-flex justify-content-center" href="#" data-post-action="modal">
							<img class="img-fluid m-0 border border-lg-bottom-0" src="<?php echo $post->getImage(); ?>">
						</a>
						
						<div class="d-none d-lg-block">
							<div class="toolbar-footer rounded-bottom">
								<?php
									Render::Template('members/posts/toolbar/buttons/like.twig', array(
										'count'  => $post->likes()->count(),
										'active' => boolval($post->likes()->lookup($member))
									));
									
									Render::Template('members/posts/toolbar/separator.twig');
									
									Render::Template('members/posts/toolbar/buttons/comment.twig', array(
										'action'     => 'modal',
										'active'     => boolval($post->comments()->lookup($member)),
										'collapse'   => FALSE,
										'count'      => $post->comments()->count(),
										'scrollable' => TRUE
									));
									
									Render::Template('members/posts/toolbar/separator.twig');
									
									Render::Template('members/posts/toolbar/buttons/report.twig', array(
										'active' => boolval($post->reports()->lookup($member))
									));
									
									Render::Template('members/posts/toolbar/separator.twig');
									
									Render::Template('members/posts/toolbar/buttons/dropdown.twig', array(
										'link' => $post->getLink()
									));
								?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<p><?php echo $post->getContent(); ?></p>
				
				<p><small class="text-muted"><?php echo $post->getDate(); ?></small></p>
			</div>
			
			<div class="d-block d-lg-none">
				<div class="toolbar-footer rounded-bottom">
					<?php
						Render::Template('members/posts/toolbar/buttons/like.twig', array(
							'count'  => $post->likes()->count(),
							'active' => boolval($post->likes()->lookup($member))
						));
						
						Render::Template('members/posts/toolbar/separator.twig');
						
						Render::Template('members/posts/toolbar/buttons/comment.twig', array(
							'action'     => 'modal',
							'active'     => boolval($post->comments()->lookup($member)),
							'collapse'   => FALSE,
							'count'      => $post->comments()->count(),
							'scrollable' => TRUE
						));
						
						Render::Template('members/posts/toolbar/separator.twig');
						
						Render::Template('members/posts/toolbar/buttons/report.twig', array(
							'active' => boolval($post->reports()->lookup($member))
						));
						
						Render::Template('members/posts/toolbar/separator.twig');
						
						Render::Template('members/posts/toolbar/buttons/dropdown.twig', array(
							'link' => $post->getLink()
						));
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>
