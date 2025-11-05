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
	$page_title = 'Edit Subscription';
	
	// Set Item
	$item = Items\Subscription::Fetch(Database::Action("SELECT * FROM `subscriptions` WHERE `id` = :table_id", array(
		'table_id' => $dispatcher->getTableId()
	)));
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(404);
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper" data-item="<?php echo $item->toJson(JSON_HEX_QUOT); ?>">
		<form class="form-horizontal content-module">
			<div class="form-group">
				<label for="name">Name</label>
				
				<div class="feedback-wrap">
					<input id="name" class="form-control" type="text" name="name" value="<?php echo $item->getEncoded('name'); ?>" maxlength="32">
				</div>
			</div>
			
			<div class="form-group">
				<label for="benefits">Benefits</label>
				
				<div class="feedback-wrap">
					<textarea id="benefits" class="form-control" name="benefits" rows="20"><?php echo $item->getBenefits(); ?></textarea>
				</div>
			</div>
			
			<div class="form-group">
				<label for="content">Content</label>
				
				<div class="feedback-wrap">
					<textarea id="content" class="form-control" name="content" rows="20"><?php echo $item->getContent(); ?></textarea>
				</div>
			</div>
			
			<div class="form-group">
				<label for="icon">Icon</label>
				
				<div class="feedback-wrap">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<i id="icon-preview"></i>
							</span>
						</div>
						<input id="icon" class="form-control" type="text" name="icon" value="<?php echo $item->getEncoded('icon'); ?>" maxlength="32" aria-describedby="icon-text">
					</div>
					<small id="icon-text" class="form-text text-muted">You can find more icons on the <a href="https://fontawesome.com/icons/" target="_blank">Font Awesome</a> website.</small>
				</div>
			</div>
			
			<?php if($item->isDefault()): ?>
				<div class="form-group">
					<label for="price">Price</label>
					
					<div class="feedback-wrap">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">
									<i class="fa-light fa-dollar-sign"></i>
								</span>
							</div>
							<input id="price" class="form-control" type="text" value="FREE" readonly>
							<input type="hidden" name="price" value="0.00">
						</div>
					</div>
				</div>
			<?php else: ?>
				<div class="form-group">
					<label for="price">Price</label>
					
					<div class="feedback-wrap">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">
									<i class="fa-light fa-dollar-sign"></i>
								</span>
							</div>
							<input id="price" class="form-control" type="text" name="price" value="<?php echo $item->getPrice(TRUE); ?>" data-format="number">
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<div class="form-collapse">
				<div id="advanced-options" class="collapse">
					<div class="form-group">
						<label for="default">Default?</label>
						
						<div class="select-wrap form-control">
							<select id="default" name="default" data-value="<?php echo (int)$item->isDefault(); ?>">
								<?php foreach(array(1 => 'Yes', 0 => 'No') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
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
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/subscriptions">
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
		
		// Disable Default Options
		(function(select) {
			select.find('option').each(function() {
				$(this).prop('disabled', $(this).val() !== select.val());
			});
		})(ajaxForm.find(':input[name="default"]'));
		
		// Bind Change Event to Icon
		ajaxForm.find(':input[name="icon"]').on('change', function() {
			$('i#icon-preview').removeClass().addClass(['fa-light', $(this).val()]);
		}).trigger('change');
		
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
							location.href = '/user/view/subscriptions';
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

