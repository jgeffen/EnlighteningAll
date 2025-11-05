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
	use Items\Enums\Sizes;
	use Items\Enums\Statuses;
	use Items\Members;
	
	// Variable Defaults
	$page_title = 'Review Member Reports: Profiles';
	
	// Set Item
	$item = Members\Report::Init($dispatcher->getTableId());
	
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
				<label>Report Type:</label>
				
				<div class="jumbotron py-1">
					<pre class="mb-0"><code><?php echo $item->getType()?->getLabel(); ?></code></pre>
				</div>
			</div>
			
			<div class="form-group">
				<label>Profile:</label>
				
				<div class="jumbotron py-1">
					<div class="background-none">
						<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 justify-content-center h-100">
							<div class="col align-self-center">
								<div class="card border-0">
									<img class="card-img-top" src="<?php echo $item->getProfile()?->getAvatar()?->getImage(Sizes\Avatar::XL, TRUE) ?? Items\Defaults::AVATAR_XL; ?>">
									
									<div class="card-body">
										<h5 class="card-title"><?php echo $item->getProfile()?->getFirstNames(); ?></h5>
										
										<p class="card-text">
											<?php if($item->getProfile()?->getBio()): ?>
												<?php echo $item->getProfile()->getBio(); ?>
											<?php else: ?>
												<small class="text-muted">No Bio Set</small>
											<?php endif; ?>
										</p>
										
										<hr>
										
										<h5 class="card-title">Member Details</h5>
										
										<p class="card-text"><strong>Bead Colors:</strong> <?php echo implode(', ', array_map(fn(?Options\BeadColors $bead_color) => $bead_color?->getLabel(), $item->getProfile()?->getBeadColors() ?? array())); ?></p>
										<p class="card-text"><strong>Necklace Color:</strong> <?php echo $item->getProfile()?->getNecklaceColor()?->getLabel(); ?></p>
										
										<?php if($item->getProfile()?->isCouple()): ?>
											<hr>
											
											<h5 class="card-title">Partner Details</h5>
											
											<p class="card-text"><strong>Bead Colors:</strong> <?php echo implode(', ', array_map(fn(?Options\BeadColors $bead_color) => $bead_color?->getLabel(), $item->getProfile()?->getPartnerBeadColors() ?? array())); ?></p>
											<p class="card-text"><strong>Necklace Color:</strong> <?php echo $item->getProfile()?->getPartnerNecklaceColor()?->getLabel(); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label>Username:</label>
				
				<div class="jumbotron py-1">
					<pre class="mb-0"><code><?php echo $item->getProfile()?->getUsername(); ?></code></pre>
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
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/member-reports/profiles">
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
							location.href = '/user/view/member-reports/profiles';
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

