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
	$page_title = 'Add Partner';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<h2>Search Engine Optimization:</h2>
			
			<div class="form-group">
				<label for="heading">Heading</label>
				
				<div class="feedback-wrap">
					<input id="heading" class="form-control" type="text" name="heading" maxlength="255">
				</div>
			</div>
			
			<h2>Page Content:</h2>
			
			<div class="form-group">
				<label for="content">Content</label>
				
				<div class="feedback-wrap">
					<textarea id="content" class="form-control" name="content" rows="20"></textarea>
				</div>
			</div>
			
			<div class="form-group">
				<label for="link">Link</label>
				
				<div class="feedback-wrap">
					<input id="link" class="form-control" type="text" name="link" maxlength="255">
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
						
						<div class="row justify-content-center mt-3">
							<div class="col-sm-12 col-lg-8 col-xl-6 mb-2">
								<div class="form-group">
									<label for="filename-alt">Image Alt:</label>
									
									<div class="feedback-wrap">
										<input id="filename-alt" class="form-control" type="text" name="filename_alt">
									</div>
									
									<p class="note">
										<strong>Note:</strong> Image alt text is used for ADA compliance. Provide an accurate description of the image provided.
									</p>
								</div>
							</div>
						</div>
						
						<p class="note text-center mb-0"><strong>Note:</strong> Only images ending in jpeg, jpg, png and gif are supported.</p>
					</div>
				</div>
			</fieldset>
			
			<div class="form-collapse">
				<div id="advanced-options" class="collapse">
					<div class="form-group">
						<label for="analytics">Analytics?</label>
						
						<div class="select-wrap form-control">
							<select id="analytics" name="analytics">
								<?php foreach(array('No', 'Yes') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="published">Published?</label>
						
						<div class="select-wrap form-control">
							<select id="published" name="published">
								<?php foreach(array(1 => 'Yes', 0 => 'No') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="published-date">Published Date:</label>
						
						<div class="feedback-wrap">
							<input id="published-date" class="form-control" type="text" name="published_date">
						</div>
					</div>
				</div>
				
				<hr>
				
				<div class="text-center">
					<a class="btn btn-outline" href="#" aria-controls="advanced-options" aria-expanded="false" data-target="#advanced-options" data-toggle="collapse" role="button">
						Advanced Options
					</a>
				</div>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/partners">
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
			additionalData: { table_name: 'partners', title: 'Banner' }
		});
		
		// Init Flatpickr
		flatpickr('#published-date', {
			mode: 'single',
			altInput: true,
			altFormat: 'M j, Y',
			dateFormat: 'Y-m-d',
			defaultDate: new Date(),
			plugins: [new confirmDatePlugin({
				confirmIcon: '<i class="fa-solid fa-circle-check ml-1"></i>',
				confirmText: 'Okay!',
				showAlways: true,
				theme: 'light'
			})]
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
							location.href = '/user/view/partners';
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

