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
	
	// Check Access Level
	if(!Admin\Privilege(2)) Admin\Render::ErrorDocument(401);
	
	// Variable Defaults
	$page_title = 'Add User';
	
	// Set User Types
	$user_types = Admin\UserType::Options(Database::Action("SELECT * FROM `user_types` WHERE `user_type` >= :user_type ORDER BY `user_type`", array(
		'user_type' => $admin->getUserType()?->getUserType()
	)));
	
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
				<label for="user-type">User Type</label>
				
				<div class="select-wrap form-control">
					<select id="user-type" name="user-type">
						<?php foreach($user_types as $value => $label): ?>
							<option value="<?php echo $value; ?>">
								<?php echo $label; ?>
							</option>
						<?php endforeach; ?>
					</select>
					<div class="select-box"></div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="first-name">First Name</label>
				
				<div class="feedback-wrap">
					<input id="first-name" class="form-control" type="text" name="first-name" maxlength="32">
				</div>
			</div>
			
			<div class="form-group">
				<label for="last-name">Last Name</label>
				
				<div class="feedback-wrap">
					<input id="last-name" class="form-control" type="text" name="last-name" maxlength="32">
				</div>
			</div>
			
			<div class="form-group">
				<label for="email">Email</label>
				
				<div class="feedback-wrap">
					<input id="email" class="form-control" type="email" name="email" maxlength="64">
				</div>
			</div>
			
			<div class="form-group">
				<label for="password">Password</label>
				
				<div class="feedback-wrap">
					<input id="password" class="form-control" type="text" name="password">
				</div>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/users">
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
							location.href = '/user/view/users';
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

