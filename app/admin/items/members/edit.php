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
	 * @var Admin\User        $admin
	 */
	
	// Imports
	use Items\Enums\Options;
	
	// Variable Defaults
	$page_title = 'Edit Member';
	$member     = Membership::Init($dispatcher->getTableId());
	
	// Check Member
	if(is_null($member)) Admin\Render::ErrorDocument(404);
	
	// Start Header
	include('includes/header.php');
?>

<main class="content">
	<div id="title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<div class="card mb-4">
				<div class="card-header"><h2 class="mb-0">Account Information</h2></div>
				
				<div class="card-body">
					<div class="form-group">
						<label for="username">Username</label>
						<div class="feedback-wrap">
							<input id="username" class="form-control" type="text" name="username" maxlength="16" value="<?php echo $member->getEncoded('username'); ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label for="email">Email</label>
						<div class="feedback-wrap">
							<input id="email" class="form-control" type="email" name="email" maxlength="64" value="<?php echo $member->getEncoded('email'); ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label for="phone">Phone</label>
						<div class="feedback-wrap">
							<input id="phone" class="form-control" type="text" name="phone" maxlength="14" value="<?php echo $member->getEncoded('phone'); ?>" data-format="phone">
						</div>
					</div>
					
					<div class="form-group">
						<label for="approved">Approved</label>
						<div class="select-wrap form-control">
							<select id="approved" name="approved" data-value="<?php echo (int)$member->isApproved(); ?>">
								<?php foreach(Options\YesNo::options() as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="banned">Banned</label>
						<div class="select-wrap form-control">
							<select id="banned" name="banned" data-value="<?php echo (int)$member->isBanned(); ?>">
								<?php foreach(Options\YesNo::options() as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="verified">Verified</label>
						<div class="select-wrap form-control">
							<select id="verified" name="verified" data-value="<?php echo (int)$member->isVerified(); ?>">
								<?php foreach(Options\YesNo::options() as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="teacher">Teacher Approved</label>
						<div class="select-wrap form-control">
							<select id="teacher" name="teacher" data-value="<?php echo (int)$member->isTeacherApproved(); ?>">
								<?php foreach(Options\YesNo::options() as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="card mb-4">
				<div class="card-header"><h2 class="mb-0">Address Information:</h2></div>
				
				<div class="card-body">
					<div class="form-group">
						<label for="address_line_1">Line 1</label>
						<div class="feedback-wrap">
							<input id="address_line_1" class="form-control" type="text" name="address_line_1" maxlength="64" value="<?php echo $member->getEncoded('address_line_1'); ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label for="address_line_2">Line 2</label>
						<div class="feedback-wrap">
							<input id="address_line_2" class="form-control" type="text" name="address_line_2" maxlength="16" value="<?php echo $member->getEncoded('address_line_2'); ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label for="address_city">City</label>
						<div class="feedback-wrap">
							<input id="address_city" class="form-control" type="text" name="address_city" maxlength="32" value="<?php echo $member->getEncoded('address_city'); ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label for="address_country">Country</label>
						<div class="feedback-wrap">
							<input id="address_country" class="form-control" type="text" name="address_country" maxlength="2" value="<?php echo $member->getEncoded('address_country'); ?>">
						</div>
						
						<p class="note"><strong>Note:</strong> Please only use <a href="https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes" target="_blank">Alpha-2 ISO 3166-1 Codes</a></p>
					</div>
					
					<div class="form-group">
						<label for="address_state">State</label>
						<div class="feedback-wrap">
							<input id="address_state" class="form-control" type="text" name="address_state" maxlength="2" value="<?php echo $member->getEncoded('address_state'); ?>">
						</div>
						
						<p class="note"><strong>Note:</strong> Please only use <a href="https://en.wikipedia.org/wiki/ISO_3166-2:US" target="_blank">2-Digit ISO 3166-2 Codes</a></p>
					</div>
					
					<div class="form-group">
						<label for="address_zip_code">Zip Code</label>
						<div class="feedback-wrap">
							<input id="address_zip_code" class="form-control" type="text" name="address_zip_code" maxlength="5" value="<?php echo $member->getEncoded('address_zip_code'); ?>" data-format="zip">
						</div>
					</div>
				</div>
			</div>
			
			<div class="card mb-4">
				<div class="card-header"><h2 class="mb-0">Profile Information:</h2></div>
				
				<div class="card-body">
					<div class="form-group">
						<label for="bio">Bio</label>
						<div class="feedback-wrap">
							<textarea id="bio" class="form-control disable-mce" name="bio" rows="6"><?php echo $member->getBio(); ?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label for="first_name">First Name</label>
						<div class="feedback-wrap">
							<input id="first_name" class="form-control" type="text" name="first_name" maxlength="16" value="<?php echo $member->getEncoded('first_name'); ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label for="last_name">Last Name</label>
						<div class="feedback-wrap">
							<input id="last_name" class="form-control" type="text" name="last_name" maxlength="16" value="<?php echo $member->getEncoded('last_name'); ?>">
						</div>
					</div>
				</div>
			</div>
			
			<hr>

			<div class="card mb-4">
				<div class="card-header"><h2 class="mb-0">ID Verification</h2></div>
				
				<div class="card-body">
					
					<div class="form-group">
						<label for="is_id_verified">ID Verified</label>
						<div class="select-wrap form-control">
							<select id="is_id_verified" name="is_id_verified" data-value="<?php echo (int)$member->isIdVerified(); ?>">
								<?php foreach(Options\YesNo::options() as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>

					<div class="form-group">
						<label for="id_verified_admin_approval">ID Verified Admin Approval</label>
						<div class="feedback-wrap">
							<input id="id_verified_admin_approval" class="form-control" type="text" name="id_verified_admin_approval" maxlength="64" value="<?php echo $member->getEncoded('id_verified_admin_approval'); ?>">
						</div>
					</div>


				</div>
			</div>

			<hr>
			
			<div class="form-group">
				<label for="notes">Notes</label>
				<div class="feedback-wrap">
					<textarea id="notes" class="form-control disable-mce" name="notes" rows="4"></textarea>
				</div>
			</div>
			
			<hr>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/members">
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
		var item     = null || <?php echo $member->toJson(); ?>;
		
		//Init TinyMCE
		// tinymce.init({
		// 	selector: '#bio',
		// 	theme: 'silver',
		// 	cache_suffix: '?v=6.1.2',
		// 	base_url: '/library/packages/tinymce',
		// 	browser_spellcheck: true,
		// 	document_base_url: '/',
		// 	element_format: 'html',
		// 	forced_root_block: 'p',
		// 	formats: {
		// 		bold: { inline: 'strong' },
		// 		italic: { inline: 'em' },
		// 		underline: { inline: 'u' }
		// 	},
		// 	height: 362,
		// 	keep_styles: false,
		// 	menubar: false,
		// 	mobile: { toolbar_mode: 'scrolling' },
		// 	plugins: 'emoticons',
		// 	protect: [/<div class="clear"><\/div>/g],
		// 	relative_urls: false,
		// 	toolbar: 'bold italic underline emoticons',
		// 	valid_elements: 'p,br,strong/b,em/i,u',
		// 	verify_html: true,
		// 	statusbar: false
		// });
		
		// Add Placeholder to Inputs
		//$(':input').attr('placeholder', '(null)');
		
		// Handle Submission
		ajaxForm.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Handle Ajax
			$.ajax({
				data: Object.assign($(this).serializeObject(), { item: item }),
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							location.href = '/user/view/members';
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

