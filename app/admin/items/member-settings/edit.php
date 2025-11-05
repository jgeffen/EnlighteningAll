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
	
	// Imprts
	use Items\Enums\Options;
	use Items\Enums\Types;
	use Items\Members;
	
	// Variable Defaults
	$page_title = 'Edit Member Setting';
	
	// Set Item
	$item = Members\Setting::Init($dispatcher->getTableId());
	
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
				<label for="label">Label</label>
				<div class="feedback-wrap">
					<input id="label" class="form-control" type="text" value="<?php echo $item->getEncoded('label'); ?>" readonly>
				</div>
			</div>
			
			<div class="form-group">
				<label for="label-text">Label Text</label>
				
				<div class="feedback-wrap">
					<textarea id="label-text" class="form-control disable-mce" rows="5" readonly><?php echo $item->getLabelText(); ?></textarea>
				</div>
			</div>
			
			<div class="form-group">
				<label for="value">Value</label>
				
				<?php if($item->getType() == Types\Setting::BOOLEAN): ?>
					<div class="select-wrap form-control">
						<select id="value" name="value" data-value="<?php echo (int)$item->getValue(); ?>">
							<?php foreach(Options\OnOff::options() as $value => $label): ?>
								<option value="<?php echo $value; ?>">
									<?php echo $label; ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div class="select-box"></div>
					</div>
				<?php elseif($item->getType() == Types\Setting::INTEGER): ?>
					<div class="feedback-wrap">
						<input id="value" class="form-control" type="number" name="value" value="<?php echo $item->getValue(); ?>">
					</div>
				<?php elseif($item->getType() == Types\Setting::JSON): ?>
					<div class="feedback-wrap">
						<textarea id="value" class="form-control disable-mce" name="value" rows="20"><?php echo implode(PHP_EOL, $item->getValue()); ?></textarea>
					</div>
				<?php elseif($item->getType() == Types\Setting::STRING): ?>
					<div class="feedback-wrap">
						<input id="value" class="form-control" type="text" name="value" value="<?php echo $item->getEncoded('value'); ?>">
					</div>
				<?php endif; ?>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/member-settings">
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
							location.href = '/user/view/member-settings';
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

