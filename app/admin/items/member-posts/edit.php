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
	
	// Imports
	use Items\Enums\Options;
	use Items\Members\Posts\Types as Posts;
	
	// Variable Defaults
	$page_title = 'Edit Member Post';
	
	// Set Item
	$item = Posts\Social::Init($dispatcher->getTableId());
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(404);
	
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
				<label for="visibility">Visibility</label>
				<div class="select-wrap form-control">
					<select id="visibility" name="visibility" data-value="<?php echo $item->getVisibility()->getValue(); ?>">
						<?php foreach(Options\Visibility::options() as $value => $label): ?>
							<option value="<?php echo $value; ?>">
								<?php echo $label; ?>
							</option>
						<?php endforeach; ?>
					</select>
					<div class="select-box"></div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="heading">Heading:</label>
				<input id="heading" class="form-control" type="text" name="heading" value="<?php echo $item->getEncoded('heading'); ?>" maxlength="64">
			</div>
			
			<div class="form-group">
				<label for="dates">Date(s) Taken:</label>
				<input id="dates" class="form-control" type="text" name="dates" data-value="<?php echo $item->getDateJson(JSON_HEX_QUOT); ?>">
			</div>
			
			<div class="form-group">
				<label for="content">Content:</label>
				<textarea id="content" class="form-control member-post-mce disable-mce" name="content"><?php echo $item->getContent(); ?></textarea>
			</div>
			
			<div class="row justify-content-center">
				<div class="col-lg-6 col-xl-4">
					<img class="img-thumbnail" src="<?php echo $item->getImageSource(Items\Defaults::SQUARE); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="notes">Notes:</label>
				<textarea id="notes" class="form-control disable-mce" name="notes" rows="5"></textarea>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/member-posts">
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
		var item     = null || <?php echo $item->toJson(); ?>;
		
		// Init Flatpickr
		flatpickr('#dates', {
			mode: 'range',
			altInput: true,
			altFormat: 'M j, Y',
			dateFormat: 'Y-m-d',
			maxDate: new Date(),
			onReady: function() {
				this.setDate(JSON.parse(this.element.dataset.value));
			},
			plugins: [new confirmDatePlugin({
				confirmIcon: '<i class="fas fa-check-circle ml-1"></i>',
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
							location.reload();
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

