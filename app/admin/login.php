<?php
	// Set Title
	$page_title = 'Admin Login';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div class="container px-0 h-100">
		<div class="row align-items-center h-100">
			<div class="col-12 col-md-6 col-lg-5 col-xl-4 mx-auto">
				<div id="ajax-wrapper" class="content-module content-module-border">
					<form id="login-form">
						<div id="page-title" class="justify-content-center">
							<h1 class="text-center">User Login</h1>
						</div>
						
						<div class="form-group">
							<label for="login-form-input-email">
								<span>Email</span>&nbsp;:
							</label>
							
							<input id="login-form-input-email" class="form-control" type="email" name="email" maxlength="255">
						</div>
						
						<div class="form-group">
							<label for="login-form-input-password">
								<span>Password</span>&nbsp;:
							</label>
							
							<input id="login-form-input-password" class="form-control" type="password" name="password" maxlength="255">
						</div>
						
						<button class="btn btn-primary btn-block mt-5">Sign in</button>
					</form>
					
					<div class="content-module-footer">
						<a href="#">I Forgot My Password</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var ajaxWrapper = $('#ajax-wrapper');
		
		// Handle Submission
		ajaxWrapper.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Process Submission
			$.ajax('/ajax/admin/login', {
				data: Object.assign(ajaxWrapper.find('form').serializeObject(), {}),
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							location.href = '/user';
							break;
						case 'error':
							displayMessage(response.message, 'alert');
							break;
						case 'debug':
							console.log(response);
							break;
						default:
							displayMessage('Something went wrong.', 'alert');
					}
				}
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

