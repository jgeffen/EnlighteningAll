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
	use Items\Enums\Sizes;
	
	// Variable Defaults
	$page_title = 'View Entries';
	
	// Set Item
	$item = Items\Contest::Fetch(Database::Action("SELECT * FROM `contests` WHERE `id` = :table_id", array(
		'table_id' => $dispatcher->getTableId()
	)));
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(404);
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<h1 class="text-center"><?php echo $page_title; ?></h1>
		
		<div class="row justify-content-center">
			<div class="col-sm-10 col-md-7 col-lg-6 col-xl-5 posts">
				<?php foreach($item->getPosts() as $post): ?>
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
							?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
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
			var button  = $(this);
			var action  = button.data('post-action');
			var post    = button.parents('[data-post-id]');
			var dataset = post.data();

			// Switch Action
			switch(action) {
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
							switch(response.status) {
								case 'success':
									$(response.modal).on('shown.bs.modal', function() {
										// Variable Defaults
										var modal = $(this);
										var form  = modal.find('form');

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
														switch(response.status) {
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
					switch(field) {
						case 'approved':
							// Handle Ajax
							$.ajax('/user/toggle/member-posts/approved/' + dataset.postId, {
								data: { status: button.data('status') ? 1 : 0 },
								dataType: 'json',
								method: 'post',
								async: true,
								beforeSend: showLoader,
								complete: hideLoader,
								success: function(response) {
									// Switch Status
									switch(response.status) {
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
	});
</script>

<?php include('includes/body-close.php'); ?>

