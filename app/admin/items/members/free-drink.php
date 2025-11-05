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
	
	// Set Title
	$page_title = 'Issue Free Drink';
	
	// Start Header
	include('includes/header.php');
?>

<style>
	#free-drink-form textarea {
		border-radius: unset;
		resize: none;
		height: 600px;
		border: 2px solid #dc3545;
		box-shadow: unset;
		background: white;
		color: white;
	}

	#free-drink-form textarea::placeholder {
		text-align: center;
		line-height: 600px;
		font-size: 25px;
		text-transform: uppercase;
	}

	#free-drink-form textarea:focus {
		outline: none !important;
		border: 2px solid #28a745;
		box-shadow: unset;
	}
</style>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>
		
		<div id="free-drink-form" class="row">
			<div class="col-12">
				<form class="form-horizontal">
					<div class="form-group">
						<div class="feedback-wrap">
							<textarea class="form-control disable-mce" name="data"></textarea>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var ajaxWrapper = $('#view-table');
		var wrapper     = $('#free-drink-form');
		var form        = wrapper.find('form');
		var input       = form.find('textarea[name="data"]');

		// Bind QR Functionality to Textarea
		input.on('focusin', function() {
			input.attr('placeholder', 'QR Scanner Ready');
		}).on('focusout', function() {
			input.attr('placeholder', 'QR Scanner NOT Ready: Click Here');
		}).on('keypress', function(event) {
			// Trigger on "Enter"
			(event.which === 13) && form.trigger('submit');
		}).trigger('focus');

		// Bind Submit Functionality to Form
		form.on('submit', function(event) {
			// Prevent Default
			event.preventDefault();

			// Handle Ajax Request
			$.ajax('/ajax/admin/items/members/free-drink/fetch-account', {
				data: $(this).serializeArray(),
				dataType: 'json',
				async: false,
				method: 'post',
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							// Replace Content
							ajaxWrapper.html(response.html).on('click', '[data-free-drink-action]', function(event) {
								// Prevent Default
								event.preventDefault();

								// Variable Defaults
								var button    = $(this);
								var dataset   = button.data();
								var action    = dataset.freeDrinkAction;
								var wrapper   = button.parents('[data-member-id]');
								var member_id = wrapper.data('member-id');

								// Switch Action
								switch(action) {
									case 'cancel':
										location.reload();
										break;

									case 'confirm':
										// Handle Ajax Request
										$.ajax('/ajax/admin/items/members/free-drink/confirm/' + member_id, {
											dataType: 'json',
											async: false,
											method: 'post',
											beforeSend: showLoader,
											complete: hideLoader,
											success: function(response) {
												// Switch Status
												switch(response.status) {
													case 'success':
														displayMessage(response.message, 'success', function() {
															$(this).on('hide.bs.modal', function() {
																location.reload();
															});
														});
														break;
													case 'error':
														displayMessage(response.message || Object.keys(response.errors).map(function(key) {
															return response.errors[key];
														}).join('<br>'), 'alert', function() {
															$(this).on('hide.bs.modal', function() {
																location.reload();
															});
														});
														break;
													default:
														displayMessage(response.message || 'Something went wrong.', 'alert', function() {
															$(this).on('hide.bs.modal', function() {
																location.reload();
															});
														});
												}
											}
										});
										break;

									default:
										console.log('Unknown Free Drink Action: ', action);
								}
							});

							// Trigger Select
							ajaxWrapper.find('select').trigger('change');
							break;
						case 'error':
							displayMessage(response.message || Object.keys(response.errors).map(function(key) {
								return response.errors[key];
							}).join('<br>'), 'alert', function() {
								$(this).on('hide.bs.modal', function() {
									location.reload();
								});
							});
							break;
						default:
							displayMessage(response.message || 'Something went wrong.', 'alert', function() {
								$(this).on('hide.bs.modal', function() {
									location.reload();
								});
							});
					}
				}
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

