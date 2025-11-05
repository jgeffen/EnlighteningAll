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
	
	// Search Engine Optimization
	$page_title       = "Intake Form " . SITE_COMPANY;
	$page_description = "Do you have any feedback? Want to make a suggestion?.";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1>ENLIGHTENING ALL – Intake Survey</h1>
				
				<p>Tell us more about your interests!</p>
				
				<div class="title-bar-trim-combo" aria-label="Intake Form" role="form">
					<div class="title-bar">
						<i class="fal fa-clipboard-list-check"></i>
						<h2>Intake Form</h2>
					</div>
					
					<div id="intake-form" class="form-wrap trim p-lg-4">
						<form class="mt-lg-2">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="intake-form-input-first-name">First Name:</label>
										<div class="col-lg-9">
											<input id="intake-form-input-first-name" class="form-control" type="text" name="first_name" placeholder="* Required" maxlength="50" value="<?php echo $member->getFirstName(); ?>">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="intake-form-input-last-name">Last Name:</label>
										<div class="col-lg-9">
											<input id="intake-form-input-last-name" class="form-control" type="text" name="last_name" placeholder="* Required" maxlength="50" value="<?php echo $member->getLastName(); ?>">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="intake-form-select-edu">Education & Business:</label>
										<div class="col-lg-9">
											<select id="intake-form-select-edu" class="form-control" name="education_business">
												<?php foreach(Items\Forms\Intake::Options('education_business') as $value => $label): ?>
													<option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
								
								</div>
								
								<div class="col-lg-6">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="intake-form-input-phone">Phone:</label>
										<div class="col-lg-9">
											<input id="intake-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone" value="<?php echo $member?->getPhone(); ?>">
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="intake-form-input-email">Email:</label>
										<div class="col-lg-9">
											<input id="intake-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255" value="<?php echo $member?->getEmail(); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="intake-form-select-teacher">Are You A Teacher?</label>
										<div class="col-lg-9">
											<select id="intake-form-select-teacher" class="form-control" name="teacher">
												<?php foreach(Items\Forms\Intake::Options('teacher') as $value => $label): ?>
													<option value="<?php echo $value; ?>"><?php echo $label; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
								</div>
								
								<div class="col-lg-12">
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-center pt-0 pt-lg-1">Teacher Roles:</label>
										<div class="col-lg-9">
											<div class="row">
												<?php foreach(Items\Forms\Intake::Options('teacher_roles') as $value => $label): ?>
													<div class="col-md-6 col-lg-4 mb-2">
														<div class="form-check">
															<input class="form-check-input" type="checkbox" id="teacher-roles-<?php echo $value; ?>" name="teacher_roles[]" value="<?php echo htmlspecialchars($value); ?>">
															<label class="form-check-label" for="teacher-roles-<?php echo $value; ?>"><?php echo htmlspecialchars($label); ?></label>
														</div>
													</div>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<hr>
							
							<div class="row">
								<div class="col-lg-6">
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">Yoga Styles:</label>
										<div class="col-lg-9" id="rank-group-yoga">
											<p class="small text-muted mt-2 mb-0">Pick unique ranks 1–10 (1 = top interest). Leave “--” if not interested.</p>
											<?php foreach(Items\Forms\Intake::Options('yoga_styles') as $value => $label): ?>
												<div class="form-check d-flex justify-content-end mb-2">
													<label class="form-check-label mr-3" for="yoga-<?php echo $value; ?>">
														<?php echo htmlspecialchars($label); ?>
													</label>
													<select name="yoga[<?php echo $value; ?>]" class="form-control yoga-rank-select" data-group="yoga" style="max-width:110px;">
														<option value="">--</option>
														<?php for($i = 1; $i <= 10; $i++): ?>
															<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
														<?php endfor; ?>
													</select>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
									
									<!-- Music Preferences -->
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">Music Preferences:</label>
										<div class="col-lg-9" id="rank-group-music">
											<p class="small text-muted mt-2 mb-0">Pick unique ranks 1–10 (1 = top interest). Leave “--” if not interested.</p>
											<?php foreach(Items\Forms\Intake::Options('music_genres') as $value => $label): ?>
												<div class="form-check d-flex justify-content-end mb-2">
													<label class="form-check-label mr-3" for="music-<?php echo $value; ?>">
														<?php echo htmlspecialchars($label); ?>
													</label>
													<select id="music-<?php echo $value; ?>" class="form-control music-genre-select" data-group="music" name="music[<?php echo htmlspecialchars($value); ?>]" style="max-width:110px;">
														<option value="">--</option>
														<?php for($i = 1; $i <= 10; $i++): ?>
															<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
														<?php endfor; ?>
													</select>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
								
								<div class="col-lg-6">
									<!-- Dance / Movement -->
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">Dance/Movement:</label>
										<div class="col-lg-9" id="rank-group-dance">
											<p class="small text-muted mt-2 mb-0">Pick unique ranks 1–10 (1 = top interest). Leave “--” if not interested.</p>
											<?php foreach(Items\Forms\Intake::Options('dance_movement') as $value => $label): ?>
												<div class="form-check d-flex justify-content-end mb-2">
													<label class="form-check-label mr-3" for="dance-movement-<?php echo $value; ?>">
														<?php echo htmlspecialchars($label); ?>
													</label>
													<select id="music-<?php echo $value; ?>" class="form-control group-dance-select" data-group="dance" name="dance_movement[<?php echo htmlspecialchars($value); ?>]" style="max-width:110px;">
														<option value="">--</option>
														<?php for($i = 1; $i <= 10; $i++): ?>
															<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
														<?php endfor; ?>
													</select>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">Community Interests:</label>
										<div class="col-lg-9" id="rank-community-interest">
											<p class="small text-muted mt-2 mb-0">Pick unique ranks 1–10 (1 = top interest). Leave “--” if not interested.</p>
											<?php foreach(Items\Forms\Intake::Options('community') as $value => $label): ?>
												<div class="form-check d-flex justify-content-end mb-2">
													<label class="form-check-label mr-3" for="community-interests-<?php echo $value; ?>">
														<?php echo htmlspecialchars($label); ?>
													</label>
													<select id="music-<?php echo $value; ?>" class="form-control community-interest-select" data-group="community" name="community_interests[<?php echo htmlspecialchars($value); ?>]" style="max-width:110px;">
														<option value="">--</option>
														<?php for($i = 1; $i <= 10; $i++): ?>
															<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
														<?php endfor; ?>
													</select>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
								
								<!-- Influencer Goals -->
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">Influencer Goals:</label>
									<div class="col-lg-9" id="rank-influencer-goals">
										<p class="small text-muted mt-2 mb-0">Pick unique ranks 1–10 (1 = top interest). Leave “--” if not interested.</p>
										<?php foreach(Items\Forms\Intake::Options('influencers') as $value => $label): ?>
											<div class="form-check d-flex justify-content-end mb-2">
												<label class="form-check-label mr-3" for="influencer-goals-<?php echo $value; ?>">
													<?php echo htmlspecialchars($label); ?>
												</label>
												<select id="music-<?php echo $value; ?>" class="form-control influencer-goal-select" data-group="influencer" name="influencer_goals[<?php echo htmlspecialchars($value); ?>]" style="max-width:110px;">
													<option value="">--</option>
													<?php for($i = 1; $i <= 10; $i++): ?>
														<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
													<?php endfor; ?>
												</select>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							
								<div class="form-group row">
									<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">Core Practices:</label>
									<div class="col-lg-9" id="rank-core-practices">
										<p class="small text-muted mt-2 mb-0">Pick unique ranks 1–10 (1 = top interest). Leave “--” if not interested.</p>
										<?php foreach(Items\Forms\Intake::Options('core_practices') as $value => $label): ?>
											<div class="form-check d-flex justify-content-end mb-2">
												<label class="form-check-label mr-3" for="core-practices-<?php echo $value; ?>">
													<?php echo htmlspecialchars($label); ?>
												</label>
												<select id="music-<?php echo $value; ?>" class="form-control core-practice-select" data-group="core" name="core_practices[<?php echo htmlspecialchars($value); ?>]" style="max-width:110px;">
													<option value="">--</option>
													<?php for($i = 1; $i <= 10; $i++): ?>
														<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
													<?php endfor; ?>
												</select>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
							
							<hr>
							
							<div class="row align-items-center justify-content-center">
								<div class="col-lg-6 justify-content-center">
									<div class="form-group">
										<div class="cap-wrap text-center">
											<fieldset>
												<label class="col-form-label" for="intake-form-captcha">Enter the Characters Shown Below</label>
												<input id="intake-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
											</fieldset>
											
											<noscript>
												<p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p>
											</noscript>
										</div>
									</div>
									
									<div class="form-group row justify-content-center">
										<div class="col-sm-7">
											<button id="intake-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
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
		</div>
	</div>
</div>

<div class="container-fluid main-content p-0" style="line-height: 0;">
	<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3518.787092332972!2d-80.64312858727668!3d28.122517875846782!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88de0ff4f0105a0d%3A0xfcef3092e88962e5!2sEnlightening%20All!5e0!3m2!1sen!2sus!4v1754335980221!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

<?php include('includes/footer.php'); ?>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const allSelects = document.querySelectorAll('select[data-group]');

		allSelects.forEach(function(select) {
			select.addEventListener('change', function() {
				const group = select.dataset.group;
				updateRankOptions(group);
			});
		});

		function updateRankOptions(group) {
			const groupSelects = document.querySelectorAll(`select[data-group="${group}"]`);
			const usedValues = new Set();

			// Collect selected values
			groupSelects.forEach(function(select) {
				const val = select.value;
				if (val !== '') usedValues.add(val);
			});

			// Reset and rebuild each select with only unused + currently selected value
			groupSelects.forEach(function(select) {
				const current = select.value;
				select.innerHTML = ''; // Clear all options

				const option = document.createElement('option');
				option.value = '';
				option.textContent = '--';
				select.appendChild(option);

				for (let i = 1; i <= 10; i++) {
					if (!usedValues.has(i.toString()) || current === i.toString()) {
						const opt = document.createElement('option');
						opt.value = i;
						opt.textContent = i;
						if (current === i.toString()) {
							opt.selected = true;
						}
						select.appendChild(opt);
					}
				}
			});
		}
	});
</script>
<script>
	$(function() {
		// Variable Defaults
		var mainCSS  = $('link[href^="/css/styles-main.min.css"]');
		var ajaxForm = $('#intake-form');
		var captcha  = $('#intake-form-captcha');

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
				$.ajax('/ajax/intake', {
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
