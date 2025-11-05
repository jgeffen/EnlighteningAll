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
	use Items\Enums\Statuses;
	use Members\Messages;
	
	// Variable Defaults
	$page_title = 'Review Member Reports: Messages';
	
	// Set Item
	$item = Messages\Report::Init($dispatcher->getTableId());
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(404);
	
	// Start Header
	include('includes/header.php');
?>

<style>
	pre {
		white-space: pre-wrap; /* Since CSS 2.1 */
		white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
		white-space: -pre-wrap; /* Opera 4-6 */
		white-space: -o-pre-wrap; /* Opera 7 */
		word-wrap: break-word; /* Internet Explorer 5.5+ */
	}
</style>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<div class="form-group">
				<label>Message Transcript:</label>
				
				<div class="jumbotron py-1">
					<?php foreach($item->getMember()?->messages($item->getMemberReported()) as $message): ?>
						<pre class="mb-0"><code><?php echo $message->getContact()?->getUsername(); ?>: <?php echo $message->getContent(); ?> (<?php echo $message->getTimestamp()->format('Y-m-d H:i:s'); ?>)</code></pre>
					<?php endforeach; ?>
				</div>
			</div>
			
			<div class="form-group">
				<label>Member:</label>
				
				<div class="jumbotron py-1">
					<pre class="mb-0"><code><?php echo $item->getMemberReported()?->getUsername(); ?></code></pre>
				</div>
			</div>
			
			<div class="form-group">
				<label>Reported By:</label>
				
				<div class="jumbotron py-1">
					<pre class="mb-0"><code><?php echo $item->getMember()?->getUsername(); ?></code></pre>
				</div>
			</div>
			
			<div class="form-group">
				<label for="status">Status</label>
				
				<div class="select-wrap form-control">
					<select id="status" name="status" data-value="<?php echo $item->getStatus()?->getValue(); ?>">
						<?php foreach(Statuses\Report::options() as $value => $label): ?>
							<option value="<?php echo $value; ?>">
								<?php echo $label; ?>
							</option>
						<?php endforeach; ?>
					</select>
					<div class="select-box"></div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="notes">Notes:</label>
				<textarea id="notes" class="form-control disable-mce" name="notes" rows="5"></textarea>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Submit
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/member-reports/messages">
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
							location.href = '/user/view/member-reports/messages';
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

