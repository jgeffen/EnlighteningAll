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
	
	// Variable Defaults
	$page_title = 'Edit Form Submissions: Career Application';
	
	// Set Item
	$item = Items\Forms\Careers::Init($dispatcher->getTableId());
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(404);
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<div class="card bg-light mb-3">
				<div class="card-header">
					Career Applied For
				</div>
				<div class="card-body">
					<input id="careers-career" class="form-control" type="text" name="career" value="<?php echo $item->getEncoded('career'); ?>" readonly>
				</div>
			</div>
			
			<?php if($item->getResume()): ?>
				<div class="form-group">
					<label for="resume">Resume</label>
					<div class="input-group">
						<input type="text" class="form-control" id="resume" value="<?php echo $item->getFilename(); ?>" readonly>
						<a class="input-group-text btn btn-outline-secondary" href="<?php echo $item->getResume(); ?>" target="_blank">
							<i class="fa-solid fa-download"></i>
						</a>
					</div>
				</div>
			<?php endif; ?>
			
			<div class="form-group">
				<label for="careers-name">Name</label>
				<div class="feedback-wrap">
					<input id="careers-name" class="form-control" type="text" name="name" value="<?php echo $item->getEncoded('name'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="careers-email">Email</label>
				<div class="feedback-wrap">
					<input id="careers-email" class="form-control" type="text" name="email" value="<?php echo $item->getEncoded('email'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="careers-phone">Phone</label>
				<div class="feedback-wrap">
					<input id="careers-phone" class="form-control" type="text" name="phone" value="<?php echo $item->getEncoded('phone'); ?>" data-format="phone">
				</div>
			</div>
			
			<div class="form-group">
				<label for="careers-comments">Comments</label>
				<div class="feedback-wrap">
					<input id="careers-comments" class="form-control disable-mce" type="text" name="comments" value="<?php echo $item->getEncoded('comments'); ?>">
				</div>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/forms-careers">
						<i class="fal fa-ban"></i> Cancel
					</a>
				</div>
			</div>
		</form>
	</div>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var ajaxForm = $('#ajax-wrapper');
		var item     = ajaxForm.data('item');
		var table    = ajaxForm.data('table');
		var links    = ajaxForm.data('links');

		// Handle Submission
		ajaxForm.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();

			// Handle Ajax
			$.ajax({
				data: $(this).serializeArray(),
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							location.href = links.manage_link;
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
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

