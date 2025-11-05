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
	
	// Variable Defaults
	$page_title = 'Add FAQ';
	
	// Set Categories
	if(Admin\Categories('faqs')) {
		$categories = Database::Action("SELECT `id`, `name` FROM `categories` WHERE `table_name` = :table_name", array(
			'table_name' => 'faqs'
		))->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
	}
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<?php if(!empty($categories)): ?>
				<div class="form-group">
					<label for="category-id">Category</label>
					
					<div class="select-wrap form-control">
						<select id="category-id" name="category_id">
							<?php foreach($categories as $value => $label): ?>
								<option value="<?php echo $value; ?>">
									<?php echo $label; ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div class="select-box"></div>
					</div>
				</div>
			<?php endif; ?>
			
			<h2>Page Content:</h2>
			
			<div class="form-group">
				<label for="question">Question</label>
				
				<div class="feedback-wrap">
					<textarea id="question" class="form-control disable-mce" name="question" rows="5"></textarea>
				</div>
			</div>
			
			<div class="form-group">
				<label for="answer">Answer</label>
				
				<div class="feedback-wrap">
					<textarea id="answer" class="form-control" name="answer" rows="20"></textarea>
				</div>
			</div>
			
			<div class="form-collapse">
				<div id="advanced-options" class="collapse">
					<div class="form-group">
						<label for="published">Published?</label>
						
						<div class="select-wrap form-control">
							<select id="published" name="published">
								<?php foreach(array(1 => 'Yes', 0 => 'No') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="published-date">Published Date:</label>
						
						<div class="feedback-wrap">
							<input id="published-date" class="form-control" type="text" name="published_date">
						</div>
					</div>
				</div>
				
				<hr>
				
				<div class="text-center">
					<a class="btn btn-outline" href="#" aria-controls="advanced-options" aria-expanded="false" data-target="#advanced-options" data-toggle="collapse" role="button">
						Advanced Options
					</a>
				</div>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/faqs">
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
		
		// Init Flatpickr
		flatpickr('#published-date', {
			mode: 'single',
			altInput: true,
			altFormat: 'M j, Y',
			dateFormat: 'Y-m-d',
			defaultDate: new Date(),
			plugins: [new confirmDatePlugin({
				confirmIcon: '<i class="fa-solid fa-circle-check ml-1"></i>',
				confirmText: 'Okay!',
				showAlways: true,
				theme: 'light'
			})]
		});
		
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
							location.href = '/user/view/faqs';
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

