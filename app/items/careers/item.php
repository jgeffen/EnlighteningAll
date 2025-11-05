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
	
	// TODO: Convert gallery images to class
	
	// Fetch/Set Item
	$item    = Items\Career::Init($dispatcher->getRoute()?->getTableId());
	$careers = Items\Career::FetchAll(Database::Action("SELECT * FROM `careers` ORDER BY `position` DESC"));
	
	// Check Item
	if(is_null($item)) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $item->getTitle();
	$page_description = $item->getDescription();
	
	// Page Variables
	$top_image = $item->getLandscapeImage();
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $item->getHeading(); ?></h1>
				
				<?php $item->renderGallery(); ?>
				
				<p><b>Posted: </b><?php echo $item->getPublishedDate()->format('F jS, Y'); ?></p>
				
				<?php echo $item->getContent(); ?>
				
				<?php if($item->getYoutubeId()): ?>
					<hr class="clear mb-5">
					
					<div class="row justify-content-center">
						<div class="col-md-8 col-lg-6">
							<div class="fitvid">
								<?php echo $item->getYoutubeEmbed(); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<hr class="mb-5">
				
				<div class="col-12 d-flex justify-content-center">
					<div class="title-bar-trim-combo" aria-label="Careers Form" role="form" style="max-width: 600px; width: 100%;">
						<div class="title-bar">
							<i class="fal fa-clipboard-list-check"></i>
							<h2>Application</h2>
						</div>
						<div id="careers-form" class="form-wrap trim p-lg-4">
							<form class="mt-lg-2" novalidate>
								<div class="row">
									<div class="col-12">
										<div class="form-group">
											<label class="col-form-label" for="careers-form-input-career">Position You Are Applying For:</label>
											
											<select id="careers-form-input-career" class="form-control" name="career" required style="appearance: auto;">
												<option value="" disabled>Select One</option>
												<?php foreach($careers as $career): ?>
													<option value="<?php echo $career->getHeading(); ?>" <?php echo ($career->getHeading() === $item->getHeading()) ? 'selected' : ''; ?>>
														<?php echo $career->getHeading(); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
										
										<div class="form-group">
											<label class="col-form-label" for="careers-form-input-name">Name:</label>
											
											<input id="careers-form-input-name" class="form-control" type="text" name="name" placeholder="* Required" maxlength="50">
										</div>
										
										<div class="form-group">
											<label class="col-form-label" for="careers-form-input-phone">Phone:</label>
											
											<input id="careers-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone">
										</div>
										
										<div class="form-group">
											<label class="col-form-label" for="careers-form-input-email">Email:</label>
											
											<input id="careers-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255">
										</div>
										
										<div class="form-group">
											<label class="col-form-label" for="careers-form-textarea-comments">Questions / Comments:</label>
											
											<textarea id="careers-form-textarea-comments" class="form-control" name="comments" placeholder="* Required" rows="4"></textarea>
										</div>
										
										<div class="form-group">
											<label for="careers-form-resume" class="form-label"><b>Upload Your Resume</b></label>
											
											<input id="careers-form-resume" class="form-control px-2 py-1" type="file" name="resume" aria-describedby="careers-form-resume-button" aria-label="Upload" accept=".doc,.docx,.pdf,.txt">
										</div>
										
										<p><strong>Resumes Accepted In The Following Formats:</strong><br>.txt, .doc, .docx, & .pdf</p>
										
										<div class="form-group">
											<div class="cap-wrap text-center">
												<fieldset>
													<label class="col-form-label" for="careers-form-captcha">Enter the Characters Shown Below</label>
													<input id="careers-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
												</fieldset>
												
												<noscript><p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p></noscript>
											</div>
										</div>
										
										<div class="form-group row justify-content-end">
											<div class="col-sm-7">
												<button id="careers-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
													Submit
												</button>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				
				<?php if($item->getPDFs()): ?>
					<hr class="clear my-5">
					
					<?php
					Render::component('one-page-articles/titlebar-trim-article/titlebar-trim-article', array(
						'items'    => $item->getPDFs(),
						'icon'     => '<i class="fa-light fa-file-pdf"></i>',
						'cols'     => 3,
						'btn_text' => 'Download PDF'
					));
					?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#careers-form form');
		var captcha  = $('#careers-form-captcha');

		// Init Scripts
		$.when(
			$.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
			$.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
			$.ajax('/js/quickdeploy/jquery.dependent.fields.min.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/realperson/jquery.realperson.ada.css' }).insertBefore(mainCSS);

			// Init Captcha
			captcha.realperson();

			// Bind Resume Functionality
			(function() {
				var $uploadInput   = ajaxForm.find('input[type="file"]');
				var $uploadWrapper = $uploadInput.parents('.form-group');
				var $uploadLabel   = $uploadWrapper.find('label');
				var uploadAllowed  = $uploadInput.prop('accept').split(',');
				var uploadText     = $uploadLabel.text().trim();

				// Bind Change Event to File Input
				$uploadInput.on('change', function() {
					// Check File Size
					if(this.files.length && this.files[0].size > window.settings.maxFilesize.B) {
						try {
							this.value = ''; // safe reset
						} catch(e) {
							console.warn('Could not reset file input:', e);
						}
						$uploadLabel.text(uploadText);
						displayMessage('The maximum file size is ' + window.settings.maxFilesize.MB + 'MB.', 'alert');
						return;
					}

					// Check File Type
					if(
						this.files.length &&
						$.inArray('.' + this.files[0].name.split('.').pop(), uploadAllowed) === -1
					) {
						try {
							this.value = '';
						} catch(e) {
							console.warn('Could not reset file input:', e);
						}
						$uploadLabel.text(uploadText);
						displayMessage('The only acceptable file types are ' + uploadAllowed.join(' or '), 'alert');
						return;
					}

					// Swap Label
					$uploadLabel.text(this.files[0].name);
				});
			})();

			// Handle Submission
			ajaxForm.on('submit', function(event) {
				// Prevent Default
				event.preventDefault();

				// Add Validation Class
				this.classList.add('was-validated');

				// Check Validation
				if(this.querySelector(':invalid')) {
					displayMessage('Form has invalid fields.', 'alert');
					return;
				}

				// Show Loader
				showLoader();

				// Handle Ajax
				$.ajax({
					url: '/ajax/forms/careers',
					method: 'POST',
					data: new FormData(this),
					processData: false,
					contentType: false,
					dataType: 'json',
					complete: hideLoader,
					success: function(response) {
						// Switch Status
						switch(response.status) {
							case 'success':
								// Show Success Message
								ajaxForm.html(response.html);

								// Scroll to Top
								$('html, body').animate({
									scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 30
								}, 1000);
								break;
							case 'error':
								displayMessage(response.message || Object.keys(response.errors).map(function(key) {
									var element  = ajaxForm[0].querySelector('[name="' + key + '"]');
									var feedback = element?.parentElement.querySelector('.invalid-feedback');
									element?.setCustomValidity(response.errors[key]);

									if(feedback) {
										feedback.innerHTML = response.errors[key];
									} else {
										feedback           = document.createElement('div');
										feedback.className = 'invalid-feedback';
										feedback.innerHTML = response.errors[key];
										element?.parentElement.appendChild(feedback);
									}

									return response.errors[key];
								}).join('<br>'), 'alert', null);
								break;
							default:
								displayMessage(response.message || 'Something went wrong.', 'alert');
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						displayMessage('An error occurred during the request.', 'alert');
					}
				});
			});

			// Process Custom Validity
			ajaxForm[0].addEventListener('blur', function(event) {
				if(!event.target.validity.valid && event.target.validationMessage) {
					event.target.setCustomValidity('');
				}
			}, true);
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
