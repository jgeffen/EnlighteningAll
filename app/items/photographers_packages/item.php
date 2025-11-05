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
	
	// Variable Defaults
	$item = call_user_func(function($item) use ($options) {
		return !$item ? $item : array_merge($item, array(
			'alt'     => htmlentities($item['filename_alt'] ?: $item->getTitle(), ENT_QUOTES),
			'date'    => date('M d Y', strtotime($item['published_date'])),
			'gallery' => Render::Gallery('photographer_packages', $item['id'], $item['filename_alt'] ?: $item->getHeading()),
			'pdfs'    => Render::Files('photographer_packages', $item['id'], 'pdfs'),
			'image'   => Render::Images(array(
				'source'   => $item->getImage(),
				'thumb'    => sprintf("/files/photographer_packages/landscape/thumbs/%s", $item->getFilename()),
				'featured' => sprintf("/files/photographer_packages/landscape/%s", $item->getFilename())
			))
		));
	}, Database::Action("SELECT * FROM `photographer_packages` WHERE `page_url` = :child_url AND `photographer_id` IN (SELECT `id` FROM `photographers` WHERE `page_url` = :parent_url)", array(
		'child_url'  => $options['child_url'],
		'parent_url' => $options['parent_url']
	))->fetch(PDO::FETCH_ASSOC));
	
	// Check Item
	if(empty($item)) Render::ErrorDocument(404);
	
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
				
				<?php if(!empty($item['gallery'])): ?>
					<?php /* - RENDER GALLERY CAROUSEL COMPONENT - */ ?>
					<?php
					Render::Component('sliders/gallery-carousel/gallery-carousel', array(
						'main_image'     => $item,
						'gallery_images' => $item['gallery'],
						'inset'          => TRUE, //OPTIONS: TRUE, FALSE - Setting TRUE will float the carousel next to the content
						'inset_position' => 'right' //OPTIONS: right, left - Select which side the carousel will sit when inset
					));
					?>
				<?php elseif(!empty($item['image'])): ?>
					<div class="lightbox">
						<a href="<?php echo $item->getImage(); ?>" class="right inset border mt-0 mt-sm-0 mt-md-1">
							<img src="/images/layout/default-landscape.jpg" data-src="<?php echo $item->getLandscapeImage(); ?>" class="lazy" alt="<?php echo $item->getAlt(); ?>">
						</a>
					</div>
				<?php endif; ?>
				
				<p><b>Posted: </b><?php echo $item['date'] ?></p>
				
				<?php echo $item->getContent(); ?>
				
				<?php if($item->getYoutubeId()): ?>
					<hr class="clear mb-5">
					
					<div class="row justify-content-center">
						<div class="col-md-8 col-lg-6">
							<div class="fitvid">
								<iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $item['youtube_id']; ?>" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if($item->getPDFs()): ?>
					<hr class="clear my-5">
					<?php /* - RENDER ONE PAGE ARTICLE COMPONENT - */ ?>
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

<div class="container-fluid pad">
	<div class="container">
		<div class="row">
			<div class="title-bar-trim-combo" aria-label="Photographer Package Purchase Form" role="form">
				<div class="title-bar">
					<i class="fal fa-clipboard-list-check"></i>
					<h2>Photographer Package Booking Form</h2>
				</div>
				
				<div id="photographer-package-booking-form" class="form-wrap trim p-lg-4">
					<form class="mt-lg-2">
						<input type="hidden" name="photographer_packages_id" value="<?php echo $item['id']; ?>">
						
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-amount">Amount:</label>
									<div class="col-lg-9">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text" id="photographer-package-booking-form-addon-amount">
													<i class="fas fa-fw fa-dollar-sign"></i>
												</span>
											</div>
											<input id="photographer-package-booking-form-input-amount" class="form-control" type="text" value="<?php echo sprintf('%01.2f', $item['price']); ?>" aria-describedby="photographer-package-booking-form-addon-amount" readonly>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-first-name">First Name:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-first-name" class="form-control" type="text" name="first_name" placeholder="* Required" maxlength="50">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-last-name">Last Name:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-last-name" class="form-control" type="text" name="last_name" placeholder="* Required" maxlength="50">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-phone">Phone:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-email">Email:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-address-line-1">Address Line 1:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-address-line-1" class="form-control" type="text" name="address_line_1" placeholder="* Required" maxlength="255">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-address-line-2">Address Line 2:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-address-line-2" class="form-control" type="text" name="address_line_2" maxlength="255">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-address-city">City:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-address-city" class="form-control" type="text" name="address_city" placeholder="* Required" maxlength="255">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-select-address-state">State</label>
									<div class="col-lg-9">
										<div class="select-wrap form-control">
											<select id="photographer-package-booking-form-select-address-state" name="address_state">
												<option value="">- Required -</option>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-select-address-country">Country</label>
									<div class="col-lg-9">
										<div class="select-wrap form-control">
											<select id="photographer-package-booking-form-select-address-country" name="address_country">
												<?php foreach(MobiusPay\Client::FormOptions('countries') as $value => $label): ?>
													<option value="<?php echo $value; ?>">
														<?php echo $label; ?>
													</option>
												<?php endforeach; ?>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-address-zip-code">Zip Code:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-address-zip-code" class="form-control" type="text" name="address_zip_code" placeholder="* Required" maxlength="255" data-format="zip">
									</div>
								</div>
							</div>
							
							<div class="col-lg-6">
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-select-cc-type">Card Type</label>
									<div class="col-lg-9">
										<div class="select-wrap form-control">
											<select id="photographer-package-booking-form-select-cc-type" name="cc_type">
												<option value="">- Required -</option>
												<?php foreach(MobiusPay\Client::FormOptions('credit_card_types') as $value => $label): ?>
													<option value="<?php echo $value; ?>">
														<?php echo $label; ?>
													</option>
												<?php endforeach; ?>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-cc-number">Credit Card #:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-cc-number" class="form-control" type="text" name="cc_number" placeholder="* Required" maxlength="255">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-select-cc-expiry-month">Expiration Month</label>
									<div class="col-lg-9">
										<div class="select-wrap form-control">
											<select id="photographer-package-booking-form-select-cc-expiry-month" name="cc_expiry_month">
												<option value="">- Required -</option>
												<?php foreach(MobiusPay\Client::FormOptions('expiration_months') as $value => $label): ?>
													<option value="<?php echo $value; ?>">
														<?php echo $label; ?>
													</option>
												<?php endforeach; ?>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-select-cc-expiry-year">Expiration Year</label>
									<div class="col-lg-9">
										<div class="select-wrap form-control">
											<select id="photographer-package-booking-form-select-cc-expiry-year" name="cc_expiry_year">
												<option value="">- Required -</option>
												<?php foreach(MobiusPay\Client::FormOptions('expiration_years') as $value => $label): ?>
													<option value="<?php echo $value; ?>">
														<?php echo $label; ?>
													</option>
												<?php endforeach; ?>
											</select>
											<div class="select-box"></div>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="photographer-package-booking-form-input-cc-cvv">CVV2:</label>
									<div class="col-lg-9">
										<input id="photographer-package-booking-form-input-cc-cvv" class="form-control" type="text" name="cc_cvv" placeholder="* Required" maxlength="4">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-form-label" for="photographer-package-booking-form-textarea-comments">Comments:</label>
									<textarea id="photographer-package-booking-form-textarea-comments" class="form-control" name="comments" rows="4"></textarea>
								</div>
								
								<div class="form-group">
									<div class="cap-wrap text-center">
										<fieldset>
											<label class="col-form-label" for="photographer-package-booking-form-captcha">Enter the Characters Shown Below</label>
											<input id="photographer-package-booking-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
										</fieldset>
										
										<noscript><p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p></noscript>
									</div>
								</div>
								
								<div class="form-group row justify-content-end">
									<div class="col-sm-7">
										<button id="photographer-package-booking-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
											Submit Booking
										</button>
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

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#photographer-package-booking-form');
		var captcha  = $('#photographer-package-booking-form-captcha');
		
		// Init Scripts
		$.when(
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/realperson/jquery.realperson.ada.css' }).insertBefore(mainCSS),
			
			// Load Scripts
			$.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
			$.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Init Captcha
			captcha.realperson();
			
			// Bind Change Event to Countries
			ajaxForm.on('change', 'select[name="address_country"]', function() {
				// Variable Defaults
				var stateSelect = ajaxForm.find('select[name="address_state"]');
				
				// Handle Ajax
				$.ajax('/ajax/options/mobiuspay', {
					data: { type: 'states', sub_type: this.value },
					dataType: 'json',
					method: 'post',
					async: true,
					success: function(response) {
						// Switch Status
						switch(response.status) {
							case 'success':
								// Empty Values
								stateSelect.children().not(':first').remove();
								
								// Append New Options
								Object.keys(response.options).forEach(function(value) {
									stateSelect.append($('<option/>', {
										value: value,
										text: response.options[value]
									}));
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
					},
					error: function(xhr) {
						displayMessage(xhr.status + ': ' + xhr.statusText + ' - ' + this.url, 'alert');
					}
				});
			}).find('select[name="address_country"]').trigger('change');
			
			// Handle Submission
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();
				
				// Handle Ajax
				$.ajax('/ajax/photographers/packages/booking', {
					data: $(this).serializeArray(),
					dataType: 'json',
					method: 'post',
					async: true,
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
		}, function(xhr) {
			displayMessage(xhr.status + ': ' + xhr.statusText + ' - ' + this.url, 'alert', function() {
				$(this).on('hide.bs.modal', function() {
					location.reload();
				});
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

