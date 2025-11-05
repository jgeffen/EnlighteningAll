<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// TODO: Move image into tmp directory until saved.
	
	// Imports
	use Items\Enums\Options;
	
	// Variable Defaults
	$contests = array_filter(Items\Contest::FetchAll(), function(Items\Contest $contest) use ($member) {
		return $contest->isCurrent() && !$member->contests()?->contains($contest);
	});
	
	// Search Engine Optimization
	$page_title       = "Social Post: Add";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<div role="form" class="title-bar-trim-combo" aria-label="Add Post Form">
					<div class="title-bar">
						<i class="fal fa-address-card"></i>
						<h2>Social Post: Add</h2>
					</div>
					
					<div id="add-post-form" class="form-wrap trim p-lg-4">
						<form>
							<div class="row justify-content-center">
								<div class="col-sm-12 col-md-8 col-lg-6 mb-2">
									<fieldset id="image-cropper-wrapper">
										<div id="image-cropper" class="form-group has-feedback mb-0">
											<p class="text-center">
												<b>Recommended Image Size:</b>
												<span class="nobr">Width: 900px - Height: 900px</span>
											</p>
											
											<div id="image-cropper-uploader" class="dropzone-qd mx-auto d-flex align-items-center justify-content-center embed-responsive embed-responsive-1by1">
												<div class="dz-message d-flex flex-column">
													<i class="fas fa-cloud-upload-alt text-muted"></i>
													Upload Image
												</div>
											</div>
											
											<p class="note text-center"><strong>Note:</strong> Only images ending in jpeg, jpg and png are supported.</p>
											
											<p class="text-primary text-center mx-auto d-flex align-items-center" style="max-width: 360px;">
												<i class="fal fa-exclamation-triangle fa-2x mr-3"></i>
												<span class="font-italic">
													Nudity is prohibited.
													<br>
													No sexually explicit photos.
													<br>
													No weapons, drugs, or paraphernalia.
												</span>
											</p>
										</div>
									</fieldset>
								</div>
								
								<div class="col-sm-12 col-md-8 col-lg-6 mb-2">
									<div class="form-group row <?php echo $member->isCouple() ? 'd-block' : 'd-none'; ?>">
										<div class="col-lg-12">
											<label for="add-post-form-select-posted-by">Posted By:</label>
											<div class="select-wrap form-control">
												<select id="add-post-form-select-posted-by" name="posted-by">
													<option value="member"><?php echo $member->getFirstName(); ?></option>
													<?php if($member->isCouple()): ?>
														<option value="partner"><?php echo $member->getPartnerFirstName(); ?></option>
													<?php endif; ?>
												</select>
												<div class="select-box"></div>
											</div>
										</div>
									</div>
									
									<div class="form-group row <?php echo !empty($contests) ? 'd-block' : 'd-none'; ?>">
										<div class="col-lg-12">
											<label for="add-post-form-select-visibility">Contest:</label>
											<div class="select-wrap form-control">
												<select id="add-post-form-select-contest" name="contest">
													<option value="">
														- Optional -
													</option>
													
													<?php foreach($contests as $contest): ?>
														<option value="<?php echo $contest->getId(); ?>">
															<?php echo $contest->getTitle(); ?>
														</option>
													<?php endforeach; ?>
												</select>
												<div class="select-box"></div>
											</div>
										</div>
									</div>
									
									<div class="form-group row">
										<div class="col-lg-12">
											<label for="add-post-form-select-visibility">Visibility:</label>
											<div class="select-wrap form-control">
												<select id="add-post-form-select-visibility" name="visibility">
													<?php foreach(Options\Visibility::options() as $value => $label): ?>
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
										<label for="add-post-form-input-heading">Heading:</label>
										<input id="add-post-form-input-heading" class="form-control" type="text" name="heading" maxlength="64">
									</div>
									
									<div class="form-group">
										<label for="add-post-form-input-dates">Date(s) Taken:</label>
										<input id="add-post-form-input-dates" class="form-control" type="text" name="dates">
									</div>
									
									<div class="form-group">
										<label for="add-post-form-textarea-content">Content:</label>
										<textarea id="add-post-form-textarea-content" class="form-control tinymce" name="content"></textarea>
									</div>
									
									<div class="row mt-4">
										<div class="col-md-6 my-2 order-md-last">
											<button class="btn btn-primary btn-block" type="submit">
												<i class="fas fa-save mr-2"></i>Save
											</button>
										</div>
										
										<div class="col-md-6 my-2 order-md-first">
											<a class="btn btn-outline btn-block" href="/members/posts/social/manage">
												<i class="fas fa-ban mr-2"></i>Cancel
											</a>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#add-post-form');
		
		// Init Member Scripts
		$.when(
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/flatpickr/dist/flatpickr.min.css' }).insertBefore(mainCSS),
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/flatpickr/dist/plugins/confirmDate/confirmDate.css' }).insertBefore(mainCSS),
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/dropzone/dist/min/basic.min.css' }).insertBefore(mainCSS),
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/dropzone/dist/min/dropzone.min.css' }).insertBefore(mainCSS),
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/cropperjs/dist/cropper.min.css' }).insertBefore(mainCSS),
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/quickdeploy/cropper/jquery.cropper.css' }).insertBefore(mainCSS),
			
			// Load Scripts
			$.getScript('/library/packages/flatpickr/dist/flatpickr.min.js'),
			$.getScript('/library/packages/flatpickr/dist/plugins/confirmDate/confirmDate.js'),
			$.getScript('/library/packages/short-and-sweet/dist/short-and-sweet.min.js'),
			$.getScript('/library/packages/dropzone/dist/min/dropzone.min.js'),
			$.getScript('/library/packages/cropperjs/dist/cropper.min.js'),
			$.getScript('/js/quickdeploy/cropper/jquery.cropper.min.js'),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).done(function() {
			// Init TinyMCE
			tinymce.init({
				selector: '.tinymce',
				theme: 'silver',
				cache_suffix: '?v=6.1.2',
				base_url: '/library/packages/tinymce',
				browser_spellcheck: true,
				document_base_url: '/',
				element_format: 'html',
				forced_root_block: 'p',
				formats: {
					bold: { inline: 'strong' },
					italic: { inline: 'em' },
					underline: { inline: 'u' }
				},
				height: 362,
				keep_styles: false,
				menubar: false,
				mobile: { toolbar_mode: 'scrolling' },
				plugins: 'emoticons',
				protect: [/<div class="clear"><\/div>/g],
				relative_urls: false,
				toolbar: 'bold italic underline emoticons',
				valid_elements: 'p,br,strong/b,em/i,u',
				verify_html: true,
				statusbar: false
			});
			
			// Init Flatpickr
			var fp = flatpickr('#add-post-form-input-dates', {
				mode: 'range',
				altInput: true,
				altFormat: 'M j, Y',
				dateFormat: 'Y-m-d',
				defaultDate: [new Date(), new Date()],
				maxDate: new Date(),
				plugins: [new confirmDatePlugin({
					confirmIcon: '<i class="fas fa-check-circle ml-1"></i>',
					confirmText: 'Okay!',
					showAlways: true,
					theme: 'light'
				})]
			});
			
			// Init Short and Sweet
			ajaxForm.find(':input[maxlength]').each(function() {
				shortAndSweet(this, {
					counterClassName: 'short-and-sweet-counter d-block mt-2',
					counterLabel: '{remaining} characters left', // {remaining}, {maxlength}, {length}
					assistDelay: 2000,
					append: function(element, counter) {
						element.parentNode.appendChild(counter);
					}
				});
			});
			
			// Init Image Handler
			$('#image-cropper-wrapper').cropper({
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
				dateCallback: function(datetime) {
					fp.setDate([datetime, datetime]);
				}
			});
			
			// Handle Submission
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();
				
				// Handle Ajax
				$.ajax('/ajax/members/posts/social/add', {
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
								// Show Success Message
								ajaxForm.html(response.html);
								
								// Scroll to Top
								$('html, body').animate({
									scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 30
								}, 1000);
								
								// Redirect User
								(function(redirect, countdown) {
									// Check Redirect
									if(redirect.length) {
										// Countdown Redirect
										var countdownInterval = setInterval(function() {
											// Update Text
											redirect.text(countdown);
											
											// Check Countdown
											if(--countdown <= 0) {
												location.href = redirect.data('redirect');
												
												clearInterval(countdownInterval);
											}
										}, 1000);
									}
								})($('span[data-redirect]').first(), 5);
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
	});
</script>

<?php include('includes/body-close.php'); ?>
