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
	
	// Search Engine Optimization
	$page_title       = "";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-4">Enlightening All™ Podcast - Ad Space Tiers</h1>
				
				<?php /**
				<div id="sponsorship__wrapper-platinum" class="title-bar-trim-combo">
					<div class="title-bar">
						<i class="fa-solid fa-crown"></i>
						<h2>Platinum Sponsor</h2>
					</div>
					
					<div class="form-wrap trim p-lg-4">
						<div class="mt-lg-2">
							<div class="row justify-content-center">
								<div class="col-12">
									<h2 class="title-underlined mb-2">Maximum Visibility & Lifestyle Brand Dominance</h2>
									
									<div class="row">
										<div class="col-md-6">
											<ul class="fa-ul">
												<li><span class="fa-li"><i class="fa-solid fa-microphone"></i></span> <b>Premium Ad Placement</b> - 60-second Host-Read Ad at both start and mid-roll of each episode</li>
												<li><span class="fa-li"><i class="fa-solid fa-video"></i></span> <b>Video Branding</b> - Logo + Call-to-Action Overlay on video version</li>
												<li><span class="fa-li"><i class="fa-brands fa-instagram"></i></span> <b>Social Media Dominance</b> - Weekly Social Media Mentions (4x/month)</li>
												<li><span class="fa-li"><i class="fa-solid fa-link"></i></span> <b>Show Notes Feature</b> - Feature in show notes with custom URL or promo code</li>
											</ul>
										</div>
										<div class="col-md-6">
											<ul class="fa-ul">
												<li><span class="fa-li"><i class="fa-solid fa-envelope"></i></span> <b>Newsletter Placement</b> - Placement in Resort Newsletter (20K+ reach)</li>
												<li><span class="fa-li"><i class="fa-solid fa-ticket"></i></span> <b>VIP Access</b> - 4 VIP Weekend Passes/month to Enlightening All™ events</li>
												<li><span class="fa-li"><i class="fa-solid fa-gift"></i></span> <b>Co-Branding Opportunities</b> - Option to co-brand giveaways or sponsor exclusive podcast episodes</li>
												<li><span class="fa-li"><i class="fa-solid fa-users"></i></span> <b>Guest Segment</b> - Guest Segment Opportunity (1x every 2 months)</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> -->
				
				<!-- Gold Sponsor -->
				<div id="sponsorship__wrapper-gold" class="title-bar-trim-combo">
					<div class="title-bar">
						<i class="fa-solid fa-medal"></i>
						<h2>Gold Sponsor</h2>
					</div>
					
					<div class="form-wrap trim p-lg-4">
						<div class="mt-lg-2">
							<div class="row justify-content-center">
								<div class="col-12">
									<h2 class="title-underlined mb-2">Strong Monthly Visibility with Broad Reach</h2>
									
									<div class="row">
										<div class="col-md-6">
											<ul class="fa-ul">
												<li><span class="fa-li"><i class="fa-solid fa-microphone"></i></span> <b>Mid-Roll Ad Placement</b> - 60-second Host-Read Ad at mid-roll</li>
												<li><span class="fa-li"><i class="fa-solid fa-video"></i></span> <b>Video Logo Display</b> - Logo Display on podcast video version (lower third branding)</li>
												<li><span class="fa-li"><i class="fa-brands fa-instagram"></i></span> <b>Social Media Presence</b> - Biweekly Social Media Shoutouts (2x/month)</li>
											</ul>
										</div>
										<div class="col-md-6">
											<ul class="fa-ul">
												<li><span class="fa-li"><i class="fa-solid fa-link"></i></span> <b>Show Notes Link</b> - Link and offer code in show notes</li>
												<li><span class="fa-li"><i class="fa-solid fa-ticket"></i></span> <b>VIP Event Access</b> - 2 VIP Event Passes/month</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Silver Sponsor -->
				<div id="sponsorship__wrapper-silver" class="title-bar-trim-combo">
					<div class="title-bar">
						<i class="fa-solid fa-award"></i>
						<h2>Silver Sponsor</h2>
					</div>
					
					<div class="form-wrap trim p-lg-4">
						<div class="mt-lg-2">
							<div class="row justify-content-center">
								<div class="col-12">
									<h2 class="title-underlined mb-2">Solid Entry Point for Boutique Brands & Local Business</h2>
									
									<ul class="fa-ul">
										<li><span class="fa-li"><i class="fa-solid fa-microphone"></i></span> <b>End-Roll Ad</b> - 30-second Host-Read Ad at episode end</li>
										<li><span class="fa-li"><i class="fa-brands fa-instagram"></i></span> <b>Monthly Social Mention</b> - 1 Social Media Mention/month</li>
										<li><span class="fa-li"><i class="fa-solid fa-link"></i></span> <b>Show Notes Inclusion</b> - Link and logo in show notes</li>
										<li><span class="fa-li"><i class="fa-solid fa-handshake"></i></span> <b>Friends of Secrets</b> - Inclusion in 'Friends of Secrets Podcast' sponsor roll</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Episode-Specific Sponsor -->
				<div id="sponsorship__wrapper-episode" class="title-bar-trim-combo">
					<div class="title-bar">
						<i class="fa-solid fa-bolt"></i>
						<h2>Episode-Specific Sponsor</h2>
					</div>
					
					<div class="form-wrap trim p-lg-4">
						<div class="mt-lg-2">
							<div class="row justify-content-center">
								<div class="col-12">
									<h2 class="title-underlined mb-2">One-Time, High-Impact Exposure</h2>
									
									<ul class="fa-ul">
										<li><span class="fa-li"><i class="fa-solid fa-microphone"></i></span> <b>Opening Ad</b> - 30-second Host-Read Ad at episode start</li>
										<li><span class="fa-li"><i class="fa-solid fa-file-text"></i></span> <b>Episode Description</b> - Dedicated sponsor mention in episode description</li>
										<li><span class="fa-li"><i class="fa-brands fa-instagram"></i></span> <b>Launch Week Feature</b> - 1 Instagram Story Shoutout during launch week</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Add-On Options -->
				<div id="sponsorship__wrapper-addons" class="title-bar-trim-combo">
					<div class="title-bar">
						<i class="fa-solid fa-plus"></i>
						<h2>Add-On Options</h2>
					</div>
					
					<div class="form-wrap trim p-lg-4">
						<div class="mt-lg-2">
							<div class="row justify-content-center">
								<div class="col-12">
									<h2 class="title-underlined mb-2">Enhanced Exposure Options</h2>
									
									<div class="row">
										<div class="col-md-6">
											<ul class="fa-ul">
												<li><span class="fa-li"><i class="fa-solid fa-user-tie"></i></span> <b>Guest Feature or Interview</b> - Showcase your brand with a dedicated guest segment</li>
												<li><span class="fa-li"><i class="fa-solid fa-video"></i></span> <b>Custom Video Ad Segment</b> - Professional video advertisement integration</li>
												<li><span class="fa-li"><i class="fa-brands fa-instagram"></i></span> <b>Branded Social Media Post</b> - Dedicated branded content on social platforms</li>
											</ul>
										</div>
										<div class="col-md-6">
											<ul class="fa-ul">
												<li><span class="fa-li"><i class="fa-solid fa-envelope"></i></span> <b>Email Newsletter Mention</b> - Feature in our email newsletter distribution</li>
												<li><span class="fa-li"><i class="fa-solid fa-gift"></i></span> <b>Giveaway Sponsorship</b> - Partner with us for branded giveaway campaigns</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Terms & Highlights -->
				<div id="sponsorship__wrapper-terms" class="title-bar-trim-combo mt-5">
					<div class="title-bar">
						<i class="fa-solid fa-file-contract"></i>
						<h2>Terms & Highlights</h2>
					</div>
					
					<div class="form-wrap trim p-lg-4">
						<div class="mt-lg-2">
							<div class="row justify-content-center">
								<div class="col-12">
									<h2 class="title-underlined mb-2">Important Information</h2>
									
									<ul class="fa-ul">
										<li><span class="fa-li"><i class="fa-solid fa-calendar-days"></i></span> <b>Minimum Commitment</b> - 2 months (excluding episode-specific tier)</li>
										<li><span class="fa-li"><i class="fa-solid fa-heart"></i></span> <b>Content Alignment</b> - All content must align with Enlightening All™'s adult-lifestyle branding</li>
										<li><span class="fa-li"><i class="fa-solid fa-percentage"></i></span> <b>Bundle Discounts</b> - Bundle discounts available for 3+ month commitments</li>
										<li><span class="fa-li"><i class="fa-solid fa-pen-fancy"></i></span> <b>Ad Scripts</b> - Ad scripts can be provided by sponsor or written in-house for a creative fee</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="title-bar-trim-combo" aria-label="Sponsorship Form" role="form">
					<div class="title-bar">
						<i class="fal fa-clipboard-list-check"></i>
						<h2>Sponsorship Form</h2>
					</div>
					
					<div id="sponsorship-form" class="form-wrap trim p-lg-4">
						<form class="mt-lg-2">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="sponsorship-form-input-name">Name:</label>
										<div class="col-lg-9">
											<input id="sponsorship-form-input-name" class="form-control" type="text" name="name" placeholder="* Required" maxlength="50">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="sponsorship-form-input-phone">Phone:</label>
										<div class="col-lg-9">
											<input id="sponsorship-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="sponsorship-form-input-email">Email:</label>
										<div class="col-lg-9">
											<input id="sponsorship-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="sponsorship-form-input-address">Address:</label>
										<div class="col-lg-9">
											<input id="sponsorship-form-input-address" class="form-control" type="text" name="address" maxlength="255">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="sponsorship-form-input-city">City:</label>
										<div class="col-lg-9">
											<input id="sponsorship-form-input-city" class="form-control" type="text" name="city" maxlength="255">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="sponsorship-form-input-state">State:</label>
										<div class="col-lg-9">
											<input id="sponsorship-form-input-state" class="form-control" type="text" name="state" maxlength="255">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="sponsorship-form-input-zip-code">Zip Code:</label>
										<div class="col-lg-9">
											<input id="sponsorship-form-input-zip-code" class="form-control" type="text" name="zip_code" placeholder="* Required" maxlength="255" data-format="zip">
										</div>
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group">
										<label class="col-form-label" for="sponsorship-form-textarea-comments">Questions / Comments:</label>
										<textarea id="sponsorship-form-textarea-comments" class="form-control" name="comments" placeholder="* Required" rows="4"></textarea>
									</div>
									
									<div class="form-group">
										<div class="cap-wrap text-center">
											<fieldset>
												<label class="col-form-label" for="sponsorship-form-captcha">Enter the Characters Shown Below</label>
												<input id="sponsorship-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
											</fieldset>
											
											<noscript><p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p></noscript>
										</div>
									</div>
									
									<div class="form-group row justify-content-end">
										<div class="col-sm-7">
											<button id="sponsorship-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
												Submit
											</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div> **/ ; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#sponsorship-form');
		var captcha  = $('#sponsorship-form-captcha');

		// Init Scripts
		$.when(
			$.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
			$.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Load Styles
			$('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/realperson/jquery.realperson.ada.css' }).insertBefore(mainCSS);

			// Init Captcha
			captcha.realperson();

			// Handle Submission
			ajaxForm.on('submit', 'form', function(event) {
				// Prevent Default
				event.preventDefault();

				// Handle Ajax
				$.ajax('/ajax/contact', {
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
