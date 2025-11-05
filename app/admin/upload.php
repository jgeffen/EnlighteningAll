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
	$page_title = 'Upload';
	
	// Set Files
	$files = glob(sprintf("%s/files/documents/*", dirname(__DIR__, 2)));
	
	// Sort Files
	usort($files, fn($a, $b) => is_dir($a) == is_dir($b) ? strnatcasecmp($a, $b) : (is_dir($a) ? -1 : 1));
	
	// Make Web Relative
	$files = array_map('Helpers::WebRelative', $files);
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title">
		<h1>Upload Files</h1>
	</div>
	
	<div class="row my-auto">
		<div class="col my-4">
			<div class="jumbotron">
				<form id="dropzone" class="dropzone dz-clickable bg-white" action="/ajax/admin/upload">
					<div class="dz-message d-flex flex-column">
						<i class="fa-light fa-upload text-muted m-1"></i>
						Drag &amp; Drop here or click
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<?php if(!empty($files)): ?>
		<h2>Index of Documents:</h2>
		
		<ul class="list-unstyled">
			<?php foreach($files as $file): ?>
				<li class="mb-3">
					<a class="text-danger" href="#" data-action="delete">
						<i class="fa-solid fa-trash-can"></i>
					</a>
					
					<div class="lightbox d-inline-block">
						<a href="<?php echo $file; ?>">
							<?php echo $file; ?>
						</a>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Bind Click Event to Delete
		$('a[data-action="delete"]').on('click', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Variable Defaults
			var button   = $(this);
			var wrapper  = button.parents('li');
			var filename = wrapper.find('.lightbox > a').prop('href').split(/[\\/]/).pop();
			
			// Confirm Request
			if(confirm('Are you sure you want to delete this upload?')) {
				$.post('/ajax/admin/upload', { delete: true, filename: filename }, function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							// Remove Wrapper
							wrapper.remove();
							break;
						case 'error':
							displayMessage(response.message, 'alert');
							break;
						case 'debug':
						default:
							displayMessage(response, 'alert');
					}
				});
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>



