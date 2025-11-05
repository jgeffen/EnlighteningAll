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
	 * @var Admin\User        $admin
	 */
	
	// Variable Defaults
	$page_title = 'Edit Form Submissions: Intake Survey';
	
	// Set Item
	$item = Items\Forms\Intake::Init($dispatcher->getTableId());
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(404);
	
	// Start Header
	include('includes/header.php');
	
	use Items\Forms\Intake as Intake;
?>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<?php if($item->getResume()): ?>
				<div class="form-group">
					<label for="resume">Resume</label>
					<div class="input-group">
						<input type="text" class="form-control" id="resume" value="<?php echo $item->getFilename(); ?>" readonly>
						<a class="input-group-text btn btn-outline-secondary" href="<?php echo $item->getResume(); ?>" target="_blank">
							<i class="fa-solid fa-download"></i>
						</a>
					</div>
				</div>
			<?php endif; ?>
			
			<div class="form-group">
				<label for="intake-name">First Name</label>
				<div class="feedback-wrap">
					<input id="intake-name" class="form-control" type="text" name="first_name" value="<?php echo $item->getEncoded('first_name'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="intake-name">Last Name</label>
				<div class="feedback-wrap">
					<input id="intake-name" class="form-control" type="text" name="last_name" value="<?php echo $item->getEncoded('last_name'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="intake-email">Email</label>
				<div class="feedback-wrap">
					<input id="intake-email" class="form-control" type="text" name="email" value="<?php echo $item->getEncoded('email'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="intake-phone">Phone</label>
				<div class="feedback-wrap">
					<input id="intake-phone" class="form-control" type="text" name="phone" value="<?php echo $item->getEncoded('phone'); ?>" data-format="phone">
				</div>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Education & Business Interests</label>
				<div class="feedback-wrap">
					<p class="form-control-plaintext"><?php
							echo Intake::Options('education_business', $item->getEducationBusiness());
						?></p>
				</div>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Teacher</label>
				<div class="feedback-wrap">
					<p class="form-control-plaintext"><?php
							echo ((int)$item->getTeacher() === 1) ? 'Yes' : 'No';
						?></p>
				</div>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Teacher Roles</label>
				<div class="feedback-wrap">
					<p class="form-control-plaintext"><?php $roles = explode(',', $item->getTeacherRoles()); echo Intake::FormatCheckboxInputFromArray($roles, Intake::Options('teacher_roles')); ?></p>
				</div>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Yoga Styles</label>
				<div class="feedback-wrap">
					<p class="form-control-plaintext"><?php
							echo Intake::FormatRankedOptionsTable(json_decode($item->getYoga(), TRUE), Intake::Options('yoga_styles'));
						?></p>
				</div>
			</div>
			
			<div class="form-group">
				<label>Music Preferences</label>
				<div class="feedback-wrap">
					<p class="form-control-plaintext"><?php
							echo Intake::FormatRankedOptionsTable(json_decode($item->getMusic(), TRUE), Intake::Options('music_genres'));
						?></p>
				</div>
			</div>
			
			<div class="form-group">
				<label>Core Practices</label>
				<div class="feedback-wrap">
					<p class="form-control-plaintext"><?php
							echo Intake::FormatRankedOptionsTable(json_decode($item->getCorePractices(), TRUE), Intake::Options('core_practices'));
						?></p>
				</div>
			</div>
			
			<div class="form-group">
				<label>Dance / Movement</label>
				<div class="feedback-wrap">
					<p class="form-control-plaintext"><?php
							echo Intake::FormatRankedOptionsTable(json_decode($item->getDanceMovement(), TRUE), Intake::Options('dance_movement'));
						?></p>
				</div>
			</div>
			
			<div class="form-group">
				<label>Community Interests</label>
				<div class="feedback-wrap">
					<p class="form-control-plaintext"><?php
							echo Intake::FormatRankedOptionsTable(json_decode($item->getCommunityInterests(), TRUE), Intake::Options('community'));
						?></p>
				</div>
			</div>
			
			<div class="form-group">
				<label>Influencer Goals</label>
				<div class="feedback-wrap">
					<?php
						$goals = json_decode($item->getInfluencerGoals(), TRUE);
						echo Intake::FormatRankedOptionsTable($goals, Intake::Options('influencers'));
					?>
				</div>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/forms-intake">
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
		var item     = ajaxForm.data('item');
		var table    = ajaxForm.data('table');
		var links    = ajaxForm.data('links');

		// Handle Submission
		ajaxForm.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();

			// Handle Ajax
			$.ajax({
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
							location.href = links.manage_link;
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

