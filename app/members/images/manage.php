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
	
	// TODO: Look at scrolling window while dragging
	
	// Imports
	use Items\Collections;
	use Items\Enums\Options;
	use Items\Enums\Types;
	
	// Variable Defaults
	$member = new Membership();
	$posts  = new Collections\Posts(Database::Action("SELECT * FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `id` WHERE `member_id` = :member_id ORDER BY `position`, `timestamp` DESC", array(
		'member_id' => $member->getId()
	)), Types\Post::SOCIAL);
	
	// Search Engine Optimization
	$page_title       = "Private Photos: Management";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-5">
					Private Photos: Management
					<a class="btn btn-success btn-sm ml-auto" href="/members/posts/social/add">
						+ New Photos
					</a>
				</h1>
				
				<?php if(!empty($posts)): ?>
					<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4">
						<div class="col add-item col-mb d-xl-block d-none">
							<a href="/members/posts/social/add" style="max-width: 450px;">
								<i class="fal fa-plus-circle"></i>
							</a>
						</div>
						
						<?php foreach($posts as $post): ?>
							<?php if($post->hasImage()): ?>
								<div id="<?php echo sprintf("image-cropper-wrapper-%d", $post->getId()); ?>" class="col col-mb"
									data-cropper-aspect="1"
									data-cropper-id="<?php echo $post->getId(); ?>"
									data-cropper-source="<?php echo $post->getImageSource(); ?>">
									
									<div class="<?php echo $post->isApproved() ? 'not-pending' : 'pending'; ?>">
										<img class="img-fluid mb-0 rounded-top border border-bottom-0 lazy" src="/images/layout/default-square-thumb.jpg" data-src="<?php echo $post->getImage(); ?>">
									</div>
									
									<div class="toolbar-footer rounded-bottom mx-auto" style="max-width: 450px;">
										<button class="toolbar__btn" type="button" data-cropper-action="view">
											<i class="far fa-search-plus"></i>
										</button>
										
										<div class="toolbar__separator"></div>
										
										<button class="toolbar__btn" type="button" data-action="delete">
											<i class="far fa-trash"></i>
										</button>
										
										<div class="toolbar__separator"></div>
										
										<button class="toolbar__btn" type="button" data-cropper-action="crop">
											<i class="far fa-crop"></i>
										</button>
										
										<div class="toolbar__separator"></div>
										
										<div class="dropdown">
											<button class="toolbar__btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="far fa-bars"></i>
											</button>
											
											<div class="dropdown-menu">
												<a class="dropdown-item" href="#" data-cropper-action="crop">Crop</a>
												<a class="dropdown-item" href="#" data-action="delete">Delete</a>
												<a class="dropdown-item" href="#" data-cropper-action="view">View</a>
												
												<div role="separator" class="dropdown-divider"></div>
												
												<?php foreach(Options\Visibility::cases() as $visibility): ?>
													<?php $disabled = ($post->getVisibility() == $visibility) ? 'disabled' : ''; ?>
													<a class="dropdown-item <?php echo $disabled; ?>" href="#" data-visibility="<?php echo $visibility->getValue(); ?>" data-action="toggle-visibility"><?php echo $visibility->getLabel(); ?></a>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							<?php else: ?>
								<div id="<?php echo sprintf("image-cropper-wrapper-%d", $post->getId()); ?>" class="col col-mb" data-cropper-id="<?php echo $post->getId(); ?>">
									
									<div class="<?php echo $post->isApproved() ? 'not-pending' : 'pending'; ?>">
										<img class="img-fluid mb-0 rounded-top border border-bottom-0 lazy" src="/images/layout/default-square-thumb.jpg" data-src="/images/no-image-temp.png">
									</div>
									
									<div class="toolbar-footer rounded-bottom mx-auto" style="max-width: 450px;">
										<button class="toolbar__btn" type="button" data-action="delete">
											<i class="far fa-trash"></i>
										</button>
										
										<div class="toolbar__separator"></div>
										
										<div class="dropdown">
											<button class="toolbar__btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="far fa-bars"></i>
											</button>
											
											<div class="dropdown-menu">
												<a class="dropdown-item" href="#" data-action="edit">Edit</a>
												<a class="dropdown-item" href="#" data-action="delete">Delete</a>
												
												<div role="separator" class="dropdown-divider"></div>
												
												<?php foreach(Options\Visibility::cases() as $visibility): ?>
													<?php $disabled = ($post->getVisibility() == $visibility) ? 'disabled' : ''; ?>
													<a class="dropdown-item <?php echo $disabled; ?>" href="#" data-visibility="<?php echo $visibility->getValue(); ?>" data-action="toggle-visibility"><?php echo $visibility->getLabel(); ?></a>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<p>Oh, no! It seems you haven't uploaded any posts. Go ahead and <a href="/members/posts/social/add">click here</a> to upload your first post!</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS = $('link[href^="/css/styles-main.min.css"]');
		
		// Init Member Scripts
		$.when(
			$.getScript('/library/packages/cropperjs/dist/cropper.min.js'),
			$.getScript('/js/quickdeploy/cropper/jquery.cropper.min.js'),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).done(function() {
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/cropperjs/dist/cropper.min.css' }).insertBefore(mainCSS);
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/quickdeploy/cropper/jquery.cropper.css?v=1' }).insertBefore(mainCSS);
			
			// Init Image Handler
			$('div[id^="image-cropper-wrapper"]').cropper({
				acceptedFiles: 'image/png,image/jpeg',
				backgroundColor: '#FFFFFF',
				template: { width: 900, height: 900 },
				cropperModalUrl: '/modals/members/posts/image/cropper',
				cropUrl: '/ajax/members/posts/image/crop',
				deleteUrl: '/ajax/members/posts/image/delete',
				maxFilesize: settings.maxFilesize.MB,
				progressBarUrl: '/modals/members/posts/image/progress-bar',
				stageUrl: '/ajax/members/posts/image/html/stage',
				uploadUrl: '/ajax/members/posts/image/upload',
				onView: function(cropper, instance) {
					// Variable Defaults
					var data = ($(instance).find('[data-cropper-source]').length ? $(instance).find('[data-cropper-source]') : $(instance)).data();
					
					// Show Fancybox
					$.fancybox.open([{ src: data.cropperSource }]);
				}
			});
			
			// Bind Click Events to Non-Cropper Actions
			$('div[data-cropper-id]').on('click', '[data-action]', function(event) {
				// Prevent Default
				event.preventDefault();
				
				// Variable Defaults
				var action  = $(this).data('action');
				var wrapper = $(event.delegateTarget);
				var data    = wrapper.data();
				
				// Switch Action
				switch(action) {
					case 'delete':
						// Confirm Deletion
						if(confirm('Are you sure you want to delete this?')) {
							// Handle Ajax Requqest
							$.ajax('/ajax/members/posts/delete/' + data.cropperId, {
								dataType: 'json',
								async: false,
								method: 'post',
								beforeSend: showLoader,
								complete: hideLoader,
								success: function(response) {
									// Switch Status
									switch(response.status) {
										case 'success':
											// Reload Page
											location.reload();
											break;
										case 'error':
											displayMessage(response.message, 'alert');
											break;
										case 'debug':
										default:
											displayMessage(response, 'alert');
									}
								},
								error: function(xhr) {
									displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
								}
							});
						}
						break;
					case 'edit':
						location.href = '/members/posts/social/edit/' + data.cropperId;
						break;
					case 'toggle-visibility':
						// Handle Ajax Request
						$.ajax('/ajax/members/posts/visibility/' + data.cropperId, {
							dataType: 'json',
							async: false,
							method: 'post',
							data: { visibility: $(this).data('visibility') },
							beforeSend: showLoader,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Reload Page
										location.reload();
										break;
									case 'error':
										displayMessage(response.message, 'alert', function() {
											$(this).on('shown.bs.modal', hideLoader);
										});
										break;
									case 'debug':
									default:
										displayMessage(response, 'alert', function() {
											$(this).on('shown.bs.modal', hideLoader);
										});
								}
							}
						});
						break;
					default:
						console.error('Unknown Action:', action);
				}
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
