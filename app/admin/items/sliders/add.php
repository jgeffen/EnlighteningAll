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
	$page_title  = 'Add Slider';
	$image_sizes = array(
		'slide' => array(
			'width'  => 1600,
			'height' => 667
		)
	);
	
	// Static Pages
	$pages = array(
		'homepage'  => 'Homepage',
		'condos'    => 'Condos for Sale',
		'condos_ii' => 'Condos II for Sale'
	);
	
	// Set Template
	$template = reset($image_sizes);
	
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
				<label for="page-url">Page</label>
				
				<div class="select-wrap form-control">
					<select id="page-url" name="page_url">
						<?php foreach($pages as $value => $label): ?>
							<option value="<?php echo $value; ?>">
								<?php echo $label; ?>
							</option>
						<?php endforeach; ?>
					</select>
					<div class="select-box"></div>
				</div>
			</div>
			
			<fieldset id="image-cropper-wrapper">
				<h2>Upload Slide:</h2>
				
				<div class="frame p-4 p-lg-5">
					<div id="image-cropper" class="form-group mb-0">
						<p class="text-center">
							<b>Recommended Image Size:</b>
							<span class="nobr">Width: <?php echo $template['width']; ?>px - Height: <?php echo $template['height']; ?>px</span>
						</p>
						
						<div id="image-cropper-uploader" class="dropzone-qd mx-auto d-flex align-items-center justify-content-center embed-responsive embed-responsive-16by9" data-template="<?php echo htmlentities(json_encode($template), ENT_QUOTES); ?>">
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
						
						<div class="row justify-content-center mt-3">
							<div class="col-sm-12 col-md-10 col-lg-8 col-xl-5 mb-2 text-center">
								<a class="btn btn-secondary btn-block" href="<?php echo sprintf("/user/image-template?width=%d&height=%d", $template['width'], $template['height']); ?>" download>
									<i class="fal fa-download"></i> Download Image Template
								</a>
							</div>
						</div>
						
						<p class="note text-center mb-0"><strong>Note:</strong> Only images ending in jpeg, jpg and png are supported.</p>
					</div>
				</div>
			</fieldset>
			
			<div class="form-collapse">
				<div id="advanced-options" class="collapse">
					<div class="form-group">
						<label for="heading">Heading</label>
						
						<div class="feedback-wrap">
							<input id="heading" class="form-control" type="text" name="heading" maxlength="255">
						</div>
					</div>
					
					<div class="form-group">
						<label for="content">Content</label>
						
						<div class="feedback-wrap">
							<textarea id="content" class="form-control disable-mce" name="content" rows="20" maxlength="150"></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label for="content-position">Content Position</label>
						
						<div class="select-wrap form-control">
							<select id="content-position" name="content_position">
								<?php foreach(array('left' => 'Left', 'right' => 'Right', 'center' => 'Center') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="link">Link</label>
						
						<div class="feedback-wrap">
							<input id="link" class="form-control" type="text" name="link" maxlength="255">
						</div>
					</div>
					
					<div class="form-group">
						<label for="link-text">Link Text</label>
						
						<div class="feedback-wrap">
							<input id="link-text" class="form-control" type="text" name="link_text" maxlength="35">
						</div>
					</div>
					
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
						<label for="expiration">Expiration?</label>
						
						<div class="select-wrap form-control">
							<select id="expiration" name="expiration">
								<?php foreach(array('No', 'Yes') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div id="expiration">
						<div class="form-group">
							<label for="expiration-date">Expiration Date:</label>
							
							<div class="feedback-wrap">
								<input id="expiration-date" class="form-control" type="text" name="expiration_date">
							</div>
						</div>
						
						<div class="form-group">
							<label for="delete-on-expiration">Delete on Expiration?</label>
							
							<div class="select-wrap form-control">
								<select id="delete-on-expiration" name="delete_on_expiration">
									<?php foreach(array('No', 'Yes') as $value => $label): ?>
										<option value="<?php echo $value; ?>">
											<?php echo $label; ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="select-box"></div>
							</div>
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
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/sliders">
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
		var ajaxForm   = $('#ajax-wrapper');
		var imageSizes = null || <?php echo json_encode($image_sizes); ?>;

		// Init Cropper
		$('#image-cropper-wrapper').cropper({
			acceptedFiles: 'image/png,image/jpeg',
			backgroundColor: '#FFF',
			template: imageSizes[Object.keys(imageSizes)[0]],
			cropperModalUrl: '/modals/admin/cropper',
			cropUrl: '/ajax/admin/cropper/crop',
			deleteUrl: '/ajax/admin/cropper/delete',
			maxFilesize: settings.maxFilesize.MB,
			progressBarUrl: '/modals/admin/cropper/progress-bar',
			stageUrl: '/ajax/admin/cropper/stage',
			uploadUrl: '/ajax/admin/cropper/upload',
			additionalData: { table_name: 'sliders', sizes: imageSizes }
		});

		// Init Flatpickr (Published Date)
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

		// Init Flatpickr (Expiration Date)
		flatpickr('#expiration-date', {
			mode: 'single',
			altInput: true,
			altFormat: 'M j, Y',
			dateFormat: 'Y-m-d',
			defaultDate: new Date().fp_incr(7),
			plugins: [new confirmDatePlugin({
				confirmIcon: '<i class="fa-solid fa-circle-check ml-1"></i>',
				confirmText: 'Okay!',
				showAlways: true,
				theme: 'light'
			})]
		});

		// Init Depends On
		$('div#expiration').dependsOn({
			selector: $('select[name="expiration"]'),
			value: ['1'],
			wrapper: null
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
							location.href = '/user/view/sliders';
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

