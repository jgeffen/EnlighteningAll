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
	
	// Imports
	use Items\Collections;
	use Items\Enums\Sizes;
	use Items\Enums\Types;
	
	// Variable Defaults


$posts = new Collections\Posts(Database::Action("WITH friendPostData AS ( SELECT member_posts.*, member_post_type_social.*, member_photos_limit.private_photos_limit, ROW_NUMBER() OVER (PARTITION BY member_photos_limit.member_id ORDER BY member_posts.timestamp DESC) AS row_num  FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `member_posts`.`id` JOIN `member_photos_limit` ON `member_photos_limit`.`member_id` = `member_posts`.`member_id`
	WHERE `type` = :type AND (`approved` = :approved OR :approved IS FALSE) AND (`member_posts`.`member_id` IN (SELECT IF(`member_1` != :member_id, `member_1`, `member_2`) FROM `member_friends` WHERE :member_id IN (`member_1`, `member_2`)) OR `member_posts`.`member_id` = :member_id) AND visibility = 'PRIVATE'  ORDER BY `member_posts`.`timestamp` DESC) SELECT * FROM friendPostData WHERE   row_num <= private_photos_limit ORDER BY `timestamp` DESC  ", array(
		'type'      => Types\Post::SOCIAL->getValue(),
		'member_id' => $member->getId(),
		'approved'  => $member->settings()->getValue('post_approval_required'),
	)), Types\Post::SOCIAL);
	


	// Search Engine Optimization
	$page_title       = $member->getTitle('Friends Wall');
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="sr-only">Friends Wall</h1>
				
				<?php if(!empty($posts)): ?>
					<div class="row justify-content-center">
						<div class="col-sm-10 col-md-7 col-lg-6 col-xl-5 posts">
							<?php foreach($posts as $post): ?>
								<div class="col-mb posts__child" data-post-id="<?php echo $post->getId(); ?>">
									<div class="trim rounded-0 rounded-top m-0 py-4">
										<div class="row align-items-center">
											<div class="col-auto">
												<a href="<?php echo $post->getMember()->getLink(); ?>">
													<img class="profile-img-sm lazy" src="<?php echo Items\Defaults::AVATAR; ?>" alt="<?php echo $post->getMember()->getAlt('profile image'); ?>" data-src="<?php echo $post->getMember()->getAvatar()?->getImage(Sizes\Avatar::SM); ?>">
												</a>
											</div>
											
											<div class="col pl-1" style="min-width: 0;">
												<h4 class="mb-0">
													<a href="<?php echo $post->getMember()->getLink(); ?>">
														<b><?php echo $post->getMember()->getFirstNames(); ?></b>
													</a>
												</h4>
												<p class="text-truncate mb-0"><small>@<?php echo $post->getMember()->getUsername(); ?></small></p>
											</div>
										</div>
									</div>
									
									<?php if($post->hasImage()): ?>
										<a href="#" data-post-action="modal">
											<img class="img-fluid m-0 border border-top-0 border-bottom-0 lazy" src="<?php echo Items\Defaults::SQUARE; ?>" alt="<?php echo $post->getMember()->getAlt('post'); ?>" data-src="<?php echo $post->getImage(); ?>">
										</a>
									<?php endif; ?>
									
									<div class="trim m-0 rounded-0 border-bottom-0 <?php echo $post->hasImage() ? '' : 'border-top-0'; ?>">
										<h2>
											<a href="#" data-post-action="modal">
												<?php echo $post->getTitle(); ?>
											</a>
										</h2>
										
										<div class="read-more">
											<div id="<?php echo sprintf("post-read-more-%d", $post->getId()); ?>">
												<?php echo $post->getContent(); ?>
											</div>
										</div>
										
										<p><small class="text-muted"><?php echo $post->getDate(); ?></small></p>
									</div>
									
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
												'active' => boolval($post->reports()->lookup($member)),
												'postId' =>$post->getId()
											));
											
											Render::Template('members/posts/toolbar/separator.twig');
											
											Render::Template('members/posts/toolbar/buttons/dropdown.twig', array(
												'link' => $post->getLink()
											));
										?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else: ?>
					<p>Oh, no! It seems no one has uploaded any photos.</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var posts    = $('.posts');
		var readMore = function(index, element) {
			// Variable Defaults
			var post    = $(element);
			var section = post.find('.read-more');
			var content = section.find('div[id^="post-read-more"]');
			var target  = '#' + content.attr('id');
			
			// Check Height
			if(content.height() > 150) {
				content.addClass('collapse read-more__content').attr('aria-expanded', false).after($('<a/>', {
					'class': 'collapsed read-more__btn',
					'aria-expanded': false,
					'data-target': target,
					'data-toggle': 'collapse',
					'role': 'button'
				}).collapse());
			}
		};
		
		// Check Read More on Posts
		posts.find('.posts__child').each(readMore);
		
		// Bind Mutation Event to Listen for New Post
		(new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				if(mutation.addedNodes && mutation.addedNodes.length > 0) {
					[].some.call(mutation.addedNodes, function(element) {
						if(element.classList.contains('posts__child')) {
							readMore(null, element);
						}
					});
				}
			});
		})).observe(document.body, {
			attributes: true,
			childList: true,
			characterData: true
		});
	});
	
	/*==============================================*/
	/*	==== Sticky Sidebar for Events ====  		*/
	/*==============================================*/
	$(window).on('load', function() {
		window.eventStickySidebar = new StickySidebar('#events-sidebar', {
			topSpacing: 120,
			bottomSpacing: 0,
			containerSelector: '#content-area > .main-content > .container',
			innerWrapperSelector: '.events-sidebar__inner',
			minWidth: 768,
			resizeSensor: true
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
