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
	$page_title = 'Add Banner';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<div class="form-group">
				<label for="label">Label</label>
				
				<div class="feedback-wrap">
					<input id="label" class="form-control" type="text" name="label" maxlength="255">
				</div>
			</div>
			
			<fieldset id="image-uploader-wrapper">
				<h2>Upload Banner:</h2>
				
				<div class="frame p-4 p-lg-5">
					<div id="image-uploader" class="form-group mb-0">
						<div id="image-uploader-stage" class="dropzone-qd mx-auto d-flex align-items-center justify-content-center embed-responsive embed-responsive-16by9">
							<div class="dz-message d-flex flex-column">
								<i class="fas fa-cloud-upload-alt text-muted"></i>
								Upload Image
							</div>
						</div>
						
						<p class="note text-center mb-0"><strong>Note:</strong> Only images ending in jpeg, jpg, png and gif are supported.</p>
					</div>
				</div>
			</fieldset>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/banners">
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
		
		// Init Uploader
		$('#image-uploader-wrapper').uploader({
			acceptedFiles: 'image/png,image/jpeg,image/gif',
			deleteUrl: '/ajax/admin/uploader/delete',
			maxFilesize: settings.maxFilesize.MB,
			progressBarUrl: '/modals/admin/uploader/progress-bar',
			stageUrl: '/ajax/admin/uploader/stage',
			uploadUrl: '/ajax/admin/uploader/upload',
			additionalData: { table_name: 'banners', title: 'Banner' }
		});
		
		// Handle Submission
		ajaxForm.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Handle Ajax
			$.ajax({
				data: Object.assign($(this).serializeObject(), {}),
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							location.href = '/user/view/banners';
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

