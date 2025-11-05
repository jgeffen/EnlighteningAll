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
	
	// Imports
	use Items\Enums\Options;
	use Items\Enums\Sizes;
	
	// Search Engine Optimization
	$page_title       = $member->getTitle('Settings');
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-4">Settings</h1>
				
				<div role="form" class="title-bar-trim-combo mt-5">
					<div class="title-bar">
						<i class="fal fa-cogs"></i>
						<h2>Settings</h2>
					</div>
					
					<div id="settings-form" class="form-wrap trim p-lg-4">
						<form class="mt-lg-2">
							<div class="row justify-content-center">
								<div class="col-lg-6">
									<?php
										Render::Component('form-units/input.field', array(
											'form'       => 'settings-form',
											'label'      => 'Username',
											'column'     => 'username',
											'type'       => 'text',
											'validate'   => 'general',
											'max_length' => 20,
											'horizontal' => TRUE,
											'readonly'   => TRUE,
											'value'      => $member->getUsername()
										));
										
										Render::Component('form-units/input.field', array(
											'form'       => 'settings-form',
											'label'      => 'First Name',
											'column'     => 'first_name',
											'type'       => 'text',
											'validate'   => 'general',
											'max_length' => 50,
											'horizontal' => TRUE,
											'value'      => $member->getFirstName()
										));
										
										Render::Component('form-units/input.field', array(
											'form'       => 'settings-form',
											'label'      => 'Last Name',
											'column'     => 'last_name',
											'type'       => 'text',
											'validate'   => 'general',
											'max_length' => 50,
											'horizontal' => TRUE,
											'value'      => $member->getLastName()
										));
									?>
								</div>
								
								<div class="col-lg-6">
									<?php
										Render::Component('form-units/input.field', array(
											'form'       => 'settings-form',
											'label'      => 'Phone',
											'column'     => 'phone',
											'type'       => 'text',
											'mask'       => 'phone',
											'max_length' => 14,
											'horizontal' => TRUE,
											'value'      => $member->getPhone()
										));
										
										Render::Component('form-units/select.field', array(
											'form'       => 'settings-form',
											'label'      => 'Country',
											'column'     => 'country',
											'horizontal' => TRUE,
											'options'    => Locations\Country::Options(Database::Action("SELECT * FROM `location_countries` ORDER BY `name`")),
											'default'    => 'US',
											'value'      => $member->getAddressCountry()
										));
										
										Render::Component('form-units/input.field', array(
											'form'       => 'settings-form',
											'label'      => 'Postal Code',
											'column'     => 'postal_code',
											'type'       => 'text',
											'max_length' => 50,
											'horizontal' => TRUE,
											'value'      => $member->getAddressZipCode()
										));
									?>
								</div>
							</div>
							
							<hr class="mt-4 mb-5">
							
							<div class="row justify-content-center">
								<div class="col-lg-6">
									<?php
										Render::Component('form-units/select.field', array(
											'form'       => 'settings-form',
											'label'      => 'RSVP Display',
											'column'     => 'display_rsvps',
											'horizontal' => TRUE,
											'options'    => $member->subscription()?->isPaid()
												? array(1 => 'Public', 0 => 'Private')
												: array(1 => 'Public'),
											'value'      => (int)$member->isDisplayRsvps()
										));
									?>
								</div>
								
								<div class="col-lg-6"></div>
							</div>
							
							<hr class="mt-4 mb-5">
							
							<div class="row justify-content-center">
								<div class="col-lg-6">
									<?php
										Render::Component('form-units/textarea.field', array(
											'form'       => 'settings-form',
											'label'      => 'Bio',
											'column'     => 'bio',
											'rows'       => 6,
											'horizontal' => FALSE,
											'value'      => $member->getBio()
										));
									?>
								</div>
								
								<div class="col-lg-6 mt-4 mt-lg-0">
									<div id="image-cropper-wrapper" class="col-mb">
										<?php if($member->getAvatar()): ?>
											<div class="<?php echo $member->getAvatar()->isApproved() ? 'not-pending' : 'pending'; ?>">
												<img class="img-fluid d-block mx-auto mb-0 rounded-top border border-bottom-0"
													src="<?php echo $member->getAvatar()->getImage(Sizes\Avatar::LG, TRUE); ?>"
													data-cropper-aspect="1"
													data-cropper-id="<?php echo $member->getAvatar()->getId(); ?>"
													data-cropper-source="<?php echo $member->getAvatar()->getImageSource(); ?>">
											</div>
											
											<div class="toolbar-footer rounded-bottom mx-auto" style="max-width: 450px;">
												<button class="toolbar__btn" type="button" data-cropper-action="view">
													<i class="far fa-search-plus"></i>
												</button>
												
												<div class="toolbar__separator"></div>
												
												<button class="toolbar__btn" type="button" data-cropper-action="delete">
													<i class="far fa-trash"></i>
												</button>
												
												<div class="toolbar__separator"></div>
												
												<button class="toolbar__btn active" type="button" data-cropper-action="crop">
													<i class="far fa-crop"></i>
												</button>
											</div>
										<?php else: ?>
											<div id="image-cropper" class="form-group has-feedback mb-0">
												<p class="text-center">
													<b>Recommended Photo Size:</b>
													<span class="nobr">Width: 450px - Height: 450px</span>
												</p>
												
												<div id="image-cropper-uploader" class="dropzone-qd mx-auto d-flex align-items-center justify-content-center embed-responsive embed-responsive-1by1">
													<div class="dz-message d-flex flex-column">
														<i class="fas fa-cloud-upload-alt text-muted"></i>
														Upload Main Profile Avatar Photo
													</div>
												</div>
												
												<p class="note text-center"><strong>Note:</strong> Only images ending in jpeg, jpg and png are supported.</p>
											</div>
										<?php endif; ?>
									</div>
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
							</div>
							
							<?php if($member->isCouple()): ?>
								<hr class="mt-4 mb-4">
								
								<h2 class="mb-4">Partner Information</h2>
								
								<div class="row justify-content-center">
									<div class="col-lg-6">
										<?php
											Render::Component('form-units/input.field', array(
												'form'       => 'settings-form',
												'label'      => 'First Name',
												'column'     => 'partner_first_name',
												'type'       => 'text',
												'validate'   => 'general',
												'max_length' => 50,
												'horizontal' => TRUE,
												'value'      => $member->getPartnerFirstName()
											));
											
											Render::Component('form-units/select.field', array(
												'form'       => 'settings-form',
												'label'      => 'Lifestyle',
												'column'     => 'partner_necklace_color',
												'horizontal' => TRUE,
												'options'    => Options\NecklaceColors::options(),
												'value'      => $member->getPartnerNecklaceColor()->getValue()
											));
										?>
									</div>
									<div class="col-lg-6">
										<?php
											Render::Component('form-units/checkbox.field', array(
												'form'       => 'settings-form',
												'label'      => 'Preferences',
												'column'     => 'partner_bead_colors',
												'horizontal' => TRUE,
												'options'    => Options\BeadColors::options(),
												'values'     => array_map(fn(?Options\BeadColors $bead_color) => $bead_color?->getValue(), $member->getPartnerBeadColors())
											));
										?>
									</div>
								</div>
							<?php endif; ?>
							
							<hr class="mt-4 mb-5">
							
							<div class="form-group row justify-content-center">
								<div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
									<button type="submit" class="btn btn-block btn-primary submit-btn">
										<i class="fas fa-save mr-2"></i>Save
									</button>
									
									<div class="text-center my-2">
										<small class="text-muted">
											Need to change your password?
											<a href="/members/change-password">
												Click Here
											</a>
										</small>
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
		var ajaxForm = $('#settings-form');

		// Defer Scripts
		$.when(
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/dropzone/dist/min/basic.min.css' }).insertBefore(mainCSS),
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/dropzone/dist/min/dropzone.min.css' }).insertBefore(mainCSS),
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/library/packages/cropperjs/dist/cropper.min.css' }).insertBefore(mainCSS),
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/quickdeploy/cropper/jquery.cropper.css' }).insertBefore(mainCSS),

			// Load Scripts
			$.getScript('/library/packages/jquery-mask-plugin/dist/jquery.mask.min.js'),
			$.getScript('/library/packages/dropzone/dist/min/dropzone.min.js'),
			$.getScript('/library/packages/cropperjs/dist/cropper.min.js'),
			$.getScript('/js/quickdeploy/cropper/jquery.cropper.min.js'),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).done(function() {
			// Init TinyMCE
			tinymce.init({
				selector: '#settings-form-textarea-bio',
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

			// Init Masked Input
			$('[data-type="date"]').mask('99/99/9999', { placeholder: ' ' });
			$('[data-type="phone"]').mask('(999) 999-9999', { placeholder: ' ' });
			$('[data-type="zip"]').mask('99999', { placeholder: ' ' });

			// Init Image Handler
			$('#image-cropper-wrapper').cropper({
				acceptedFiles: 'image/png,image/jpeg',
				backgroundColor: '#FFF',
				template: { width: 450, height: 450 },
				cropperModalUrl: '/modals/members/settings/avatar/cropper',
				cropUrl: '/ajax/members/settings/avatar/crop',
				deleteUrl: '/ajax/members/settings/avatar/delete',
				maxFilesize: settings.maxFilesize.MB,
				progressBarUrl: '/modals/members/settings/avatar/progress-bar',
				stageUrl: null,
				uploadUrl: '/ajax/members/settings/avatar/upload',
				onView: function(cropper, instance) {
					// Variable Defaults
					var data = ($(instance).find('[data-cropper-source]').length ? $(instance).find('[data-cropper-source]') : $(instance)).data();

					// Show Fancybox
					$.fancybox.open([{ src: data.cropperSource }]);
				}
			});

			// Handle Submission
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();

				// Handle Ajax
				$.ajax('/ajax/members/settings', {
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
								ajaxForm.replaceWith(response.html);

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
					},
					error: function(xhr) {
						displayMessage(xhr.status + ': ' + xhr.statusText + ' - ' + this.url, 'alert');
					}
				});
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
