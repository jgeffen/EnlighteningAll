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
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// TODO: Persist modal like/report when closing modal
	
	// Imports
	use Items\Collections;
	use Items\Enums\Options;
	use Items\Enums\Sizes;
	use Items\Enums\Types;
	
	// Variable Defaults
	$events = Items\Event::FetchAll(Database::Action("SELECT * FROM `events` WHERE `published` IS TRUE AND `date_end` >= CURDATE() ORDER BY `date_start`, `date_end`, `page_title` LIMIT 100"));
	
	$limit  = 6;  // Number of posts per page
	$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$offset = ($page - 1) * $limit;
	
	$posts = new Collections\Posts(Database::Action("SELECT * FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `id` WHERE `visibility` = :visibility AND `type` = :type AND (`approved` = :approved OR :approved IS FALSE) AND NOT JSON_CONTAINS(:blocked_ids, `member_id`) ORDER BY `timestamp` DESC LIMIT :offset, :limit", array(
		'visibility'  => Options\Visibility::MEMBERS->getValue(),
		'type'        => Types\Post::SOCIAL->getValue(),
		'approved'    => $member->settings()->getValue('post_approval_required'),
		'blocked_ids' => json_encode($member->getBlockedIds()),
		'offset'      => $offset,
		'limit'       => $limit
	)), Types\Post::SOCIAL);
	
	// Search Engine Optimization
	$page_title       = $member->getTitle('Public Wall');
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>
	
	<div class="container-fluid main-content">
		<div class="container">
			<div class="row events-sidebar__wrapper">
				<div class="col">
					<h1 class="sr-only">Members Wall</h1>
					
					<?php if(!$posts->empty()) : ?>
						
						<div class="row justify-content-center">
							
							<div class="col-sm-10 col-md-7 col-lg-6 col-xl-5 posts">
								
								<?php foreach($posts as $post) : ?>
									
									<div class="col-mb posts__child" data-post-id="<?php echo $post->getId(); ?>" id="post_id_<?php echo $post->getId(); ?>">
										
										<div class="trim rounded-0 rounded-top m-0 py-4">
											
											<div class="row align-items-center">
												
												<div class="col-auto">
													
													<a href="<?php echo $post->getMember()->getLink(); ?>">
														<img class="profile-img-sm lazy" src="<?php echo Items\Defaults::AVATAR; ?>" alt="<?php echo $post->getMember()->getAlt('profile image'); ?>" data-src="<?php echo $post->getMember()->getAvatar()?->getImage(Sizes\Avatar::SM); ?>"> <?php echo $post->getMember()->isIdVerified() ? '<i class="fa-solid fa-badge-check" alt="ID Verified" title="ID Verified" style="font-size: 1rem;float: right;color: #005695;"></i>' : ""; ?>
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
										
										<?php if($post->hasImage()) : ?>
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
													'postId' => $post->getId()
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
							
							<?php if(!empty($events)) : ?>
								<div class="col-sm-10 col-md-5 col-lg-4 col-xl-3 d-none d-md-block">
									<div id="events-sidebar">
										<div class="events-sidebar__inner">
											<div class="title-bar-trim-combo mt-0">
												<div class="title-bar">
													<h2>Upcoming Events</h2>
												</div>
												
												<div class="trim p-4">
													<?php foreach($events as $event) : ?>
														<h4>
															<b><a href="<?php echo $event->getLink(); ?>" title="<?php echo $event->getAlt(); ?>" target="_blank">
																	<?php echo $event->getHeading(); ?>
																</a></b>
														</h4>
														<p>
															<em>
																<a href="<?php echo $event->getLink(); ?>" title="<?php echo $event->getAlt(); ?>" class="text-default" target="_blank">
																	<?php echo $event->getEventDates(); ?>
																</a>
															</em>
														</p>
														<hr>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			
			<div class="row justify-content-center mt-4 mt-md-5">
				<div class="col-sm-10 col-md-7 col-lg-6 col-xl-5">
					<h3 class="text-center mb-0">You have reached the end of this wall.</h3>
				</div>
			</div>
		</div>
	</div>

<?php include('includes/footer.php'); ?>
	
	<script>
		$(function() {
			// Initialize data attributes on page load
			$('.posts').data('next-page', 2); // Starts from the second page because the first page is loaded by default
			$('.posts').data('loading', false); // Indicates whether data is currently being loaded

			$(window).scroll(function() {
				var lastPost         = $('.posts__child').last(); // Get the last post
				var lastPostPosition = lastPost.offset().top + lastPost.outerHeight(); // Calculate the bottom position of the last post
				var windowBottom     = $(window).scrollTop() + $(window).height(); // Calculate the bottom of the window

				if(windowBottom > lastPostPosition - 100) { // Check if the bottom of the window is close to the bottom of the last post
					console.log('Last post is in view');
					loadMorePosts();
				}
			});

			function loadMorePosts() {
				var nextPage  = $('.posts').data('next-page');
				var isLoading = $('.posts').data('loading');

				if(!isLoading) { // Check if not currently loading
					$('.posts').data('loading', true);

					$.get('?page=' + nextPage, function(data) {
						var newPosts = $(data).find('.posts__child');
						console.log(newPosts); // Assume response contains HTML of new posts
						if(newPosts.length) {
							$('.posts').append(newPosts);
							$('.posts').data('next-page', nextPage + 1); // Increment the page number
							$('.posts').data('loading', false);

							// Re-bind any necessary JavaScript to new elements
							newPosts.each(function() {
								// Log the data-src of the .img-fluid
								console.log($(this).find('.img-fluid').data('src'));

								// Get the data-src attribute for .profile-img-sm, or use a default if it's empty or undefined
								var profileImgSrc = $(this).find('.profile-img-sm').data('src');
								if(!profileImgSrc) {
									profileImgSrc = '/images/layout/default-avatar.jpg'; // Set your default image path here
								}
								$(this).find('.profile-img-sm').attr('src', profileImgSrc);

								// Set the src attribute for .img-fluid using its data-src or a default image if data-src is empty or undefined
								var imgFluidSrc = $(this).find('.img-fluid').data('src');
								if(!imgFluidSrc) {
									imgFluidSrc = '/images/layout/default-square.jpg'; // Optionally, set a default for .img-fluid too
								}
								$(this).find('.img-fluid').attr('src', imgFluidSrc);
							});
						} else {
							// No more posts to load
							$('.posts').data('loading', true); // Prevent further AJAX calls if no more posts
						}
					});
				}
			}
		});
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
				containerSelector: '.events-sidebar__wrapper',
				innerWrapperSelector: '.events-sidebar__inner',
				minWidth: 768,
				resizeSensor: true
			});
		});
	</script>

<?php include('includes/body-close.php'); ?>