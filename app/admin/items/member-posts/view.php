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
 * @var Admin\User        $admin
 */

// Imports
use Items\Collections;
use Items\Enums\Sizes;
use Items\Enums\Types;

$limit = 6;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$offset = ($page - 1) * $limit;

// Check for Member
if (!is_null($dispatcher->getTableId())) {
	// Variable Defaults
	$member = Membership::Init($dispatcher->getTableId());
	$posts  = new Collections\Posts(Database::Action("SELECT * FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `id` WHERE `type` = :type AND `member_id` = :member_id ORDER BY `timestamp` DESC LIMIT :offset, :limit", array(
		'type'      => Types\Post::SOCIAL->getValue(),
		'member_id' => $dispatcher->getTableId(),
		'offset'      => $offset,
		'limit'       => $limit
	)), Types\Post::SOCIAL);

	// Variable Defaults
	$page_title = sprintf("View %s", $member->getTitle('Posts'));
} else {
	// Variable Defaults
	$posts = new Collections\Posts(Database::Action("SELECT * FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `id` WHERE `type` = :type ORDER BY `timestamp` DESC LIMIT :offset, :limit", array(
		'type' => Types\Post::SOCIAL->getValue(),
		'offset'      => $offset,
		'limit'       => $limit
	)), Types\Post::SOCIAL);

	// Variable Defaults
	$page_title = 'View Member Posts';
}

// Start Header
include('includes/header.php');
?>

<main class="page-content">

	<section id="view-table" role="region">

		<h1 class="text-center"><?php echo $page_title; ?></h1>

		<?php if ($posts->valid()) : ?>
			<div class="row justify-content-center">
				<div class="col-sm-10 col-md-7 col-lg-6 col-xl-5 posts">
					<?php foreach ($posts as $post) : ?>
						<div id="<?php echo sprintf("post-%d", $post->getId()); ?>" class="col-mb posts__child" data-post-id="<?php echo $post->getId(); ?>">
							<div class="trim rounded-0 rounded-top m-0 py-4">
								<div class="row align-items-center">
									<div class="col-auto">
										<img class="profile-img-sm lazy" src="<?php echo Items\Defaults::AVATAR; ?>" alt="<?php echo $post->getMember()->getAlt('profile image'); ?>" data-src="<?php echo $post->getMember()->getAvatar()?->getImage(Sizes\Avatar::SM, TRUE); ?>">
									</div>

									<div class="col pl-1" style="min-width: 0;">
										<h4 class="mb-0">
											<b><?php echo $post->getMember()->getFirstNames(); ?></b>
										</h4>
										<p class="text-truncate mb-0"><small>@<?php echo $post->getMember()->getUsername(); ?></small></p>
									</div>
								</div>
							</div>

							<img class="img-fluid m-0 border border-top-0 border-bottom-0 lazy" src="<?php echo Items\Defaults::SQUARE; ?>" alt="<?php echo $post->getMember()->getAlt('post'); ?>" data-src="<?php echo $post->getImage(); ?>">

							<div class="trim m-0 rounded-0 border-bottom-0">
								<h2><?php echo $post->getTitle(); ?></h2>

								<?php echo $post->getContent(); ?>

								<p><small class="text-muted"><?php echo $post->getDate(); ?></small></p>
							</div>

							<div class="toolbar-footer rounded-bottom">
								<?php
								Render::Template('admin/items/member-posts/toolbar/buttons/like.twig', array(
									'count' => $post->likes()->count()
								));

								Render::Template('admin/items/member-posts/toolbar/separator.twig');

								Render::Template('admin/items/member-posts/toolbar/buttons/comment.twig', array(
									'count' => $post->comments()->count(),
								));

								Render::Template('admin/items/member-posts/toolbar/separator.twig');

								Render::Template('admin/items/member-posts/toolbar/buttons/report.twig', array(
									'count' => $post->reports()->count()
								));

								Render::Template('admin/items/member-posts/toolbar/separator.twig');

								Render::Template('admin/items/member-posts/toolbar/buttons/dropdown.twig', array(
									'id'    => $post->getId(),
									'flags' => array(
										'approved' => $post->isApproved(FALSE)
									)
								));
								?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var wrapper = $('#view-table');

		// Delegate Click Functionality to Post Actions
		wrapper.on('click', '[data-post-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);
			var action = button.data('post-action');
			var post = button.parents('[data-post-id]');
			var dataset = post.data();

			// Switch Action
			switch (action) {
				case 'delete':
					// Handle Ajax Request
					$.ajax('/modals/admin/items/member-posts/delete/' + dataset.postId, {
						dataType: 'json',
						async: false,
						method: 'get',
						beforeSend: showLoader,
						complete: hideLoader,
						success: function(response) {
							console.log(response);
							// Switch Status
							switch (response.status) {
								case 'success':
									$(response.modal).on('shown.bs.modal', function() {
										// Variable Defaults
										var modal = $(this);
										var form = modal.find('form');

										// Bind Submit Functionality to Form
										form.on('submit', function(event) {
											// Prevent Default
											event.preventDefault();

											// Handle Ajax Request
											$.ajax('/user/delete/member-posts/' + dataset.postId, {
												data: JSON.stringify($(this).serializeObject()),
												dataType: 'json',
												method: 'delete',
												async: true,
												beforeSend: showLoader,
												complete: hideLoader,
												success: function(response) {
													// Hide Modal
													modal.on('hidden.bs.modal', function() {
														// Switch Status
														switch (response.status) {
															case 'success':
																// Destroy Modal + Post
																modal.remove();
																post.remove();

																// Display Message
																displayMessage(response.message, 'success');
																break;
															case 'error':
																// Destroy Modal
																modal.remove();

																// Display Message
																displayMessage(response.message || Object.keys(response.errors).map(function(key) {
																	return response.errors[key];
																}).join('<br>'), 'alert', null);
																break;
															default:
																// Destroy Modal
																modal.remove();

																// Display Message
																displayMessage(response.message || 'Something went wrong.', 'alert');
														}
													}).modal('hide');
												}
											});
										});
									}).on('hidden.bs.modal', function() {
										$(this).remove();
									}).modal({
										backdrop: 'static',
										keyboard: false
									});
									break;
								case 'error':
									displayMessage(response.message || Object.keys(response.errors).map(function(key) {
										return response.errors[key];
									}).join('<br>'), 'alert', null);
									break;
								default:
									displayMessage(response.message || 'Something went wrong.', 'alert');
							}
						}
					});
					break;
				case 'toggle':
					// Variable Defaults
					var field = $(this).data('field');

					// Switch Field
					switch (field) {
						case 'approved':
							// Handle Ajax
							$.ajax('/user/toggle/member-posts/approved/' + dataset.postId, {
								data: {
									status: button.data('status') ? 1 : 0
								},
								dataType: 'json',
								method: 'post',
								async: true,
								beforeSend: showLoader,
								complete: hideLoader,
								success: function(response) {
									// Switch Status
									switch (response.status) {
										case 'success':
											// Reload Post
											post.load(location.href + ' #' + post.prop('id') + '>*');
											break;
										case 'error':
											displayMessage(response.message || Object.keys(response.errors).map(function(key) {
												return response.errors[key];
											}).join('<br>'), 'alert', null);
											break;
										default:
											displayMessage(response.message || 'Something went wrong.', 'alert');
									}
								}
							});
							break;
						default:
							console.error('Unknown Field:', field);
					}
					break;
				default:
					console.error('unknown post action:', action);
			}
		});


		// Initialize data attributes on page load
		$('.posts').data('next-page', 2); // Starts from the second page because the first page is loaded by default
		$('.posts').data('loading', false); // Indicates whether data is currently being loaded

		$(window).scroll(function() {
			var lastPost = $('.posts__child').last(); // Get the last post
			var lastPostPosition = lastPost.offset().top + lastPost.outerHeight(); // Calculate the bottom position of the last post
			var windowBottom = $(window).scrollTop() + $(window).height(); // Calculate the bottom of the window

			if (windowBottom > lastPostPosition - 100) { // Check if the bottom of the window is close to the bottom of the last post
				console.log("Last post is in view");
				loadMorePosts();
			}
		});

		function loadMorePosts() {
			var nextPage = $('.posts').data('next-page');
			var isLoading = $('.posts').data('loading');

			if (!isLoading) { // Check if not currently loading
				$('.posts').data('loading', true);

				$.get('?page=' + nextPage, function(data) {
					var newPosts = $(data).find('.posts__child');
					console.log(newPosts); // Assume response contains HTML of new posts
					if (newPosts.length) {
						$('.posts').append(newPosts);
						$('.posts').data('next-page', nextPage + 1); // Increment the page number
						$('.posts').data('loading', false);

						// Re-bind any necessary JavaScript to new elements
						newPosts.each(function() {
							// Log the data-src of the .img-fluid
							console.log($(this).find(".img-fluid").data("src"));

							// Get the data-src attribute for .profile-img-sm, or use a default if it's empty or undefined
							var profileImgSrc = $(this).find(".profile-img-sm").data("src");
							if (!profileImgSrc) {
								profileImgSrc = '/images/layout/default-avatar.jpg'; // Set your default image path here
							}
							$(this).find(".profile-img-sm").attr("src", profileImgSrc);

							// Set the src attribute for .img-fluid using its data-src or a default image if data-src is empty or undefined
							var imgFluidSrc = $(this).find(".img-fluid").data("src");
							if (!imgFluidSrc) {
								imgFluidSrc = '/images/layout/default-square.jpg'; // Optionally, set a default for .img-fluid too
							}
							$(this).find(".img-fluid").attr("src", imgFluidSrc);
						});
					} else {
						// No more posts to load
						$('.posts').data('loading', true); // Prevent further AJAX calls if no more posts
					}
				});
			}
		}


	});
</script>

<?php include('includes/body-close.php'); ?>