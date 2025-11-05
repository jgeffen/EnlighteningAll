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
	 * @var Membership        $member
	 */
	
	// Fetch/Set Items
	$items = Items\Banner::FetchAll(Database::Action("SELECT * FROM `banners` ORDER BY `label`"));
	
	// Search Engine Optimization
	$page_title       = "Banners";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<style>
	pre {
		white-space: pre-wrap;       /* Since CSS 2.1 */
		white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
		white-space: -pre-wrap;      /* Opera 4-6 */
		white-space: -o-pre-wrap;    /* Opera 7 */
		word-wrap: break-word;       /* Internet Explorer 5.5+ */
		text-align: left;
	}
</style>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-5">Enlightening All™ Banners</h1>
				
				<div class="row">
					<?php if(!empty($items)): ?>
						<?php foreach($items as $item): ?>
							<div class="col-12 col-md-6">
								<div class="well well-sm post-preview fade-up">
									<div class="equal-preview text-center pb-3">
										<h3><?php echo $item->getLabel(); ?></h3>
										
										<p>
											<img class="img-fluid" src="<?php echo $item->getFilePath(); ?>">
										</p>
										
										<p>Embed Code:</p>
										
										<pre>&lt;a href="<?php echo $item->getLink(); ?>" alt="Enlightening All™"&gt;&lt;img src="<?php echo $item->getImage(); ?>" width="<?php echo $item->getImageWidth(); ?>" height="<?php echo $item->getImageHeight(); ?>"&gt;&lt;/a&gt;</pre>
										
										<p>
											<button class="copy-btn btn btn-success btn-sm"><i class="fa fa-clipboard"></i> Copy To Clipboard</button>
										</p>
										
										<p class="copy-helper"><span class="text-success">Text Copied!</span></p>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="col-xs-12">
							<p>Sorry, not Referral Links to show at this time. Please check back soon!</p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Copy the embed code from the <pre> element to clipboard
		$('.copy-btn').each(function() {
			$(this).on('click', function(event) {
				var copyHelper = $(this).parent('p').next('.copy-helper');
				// get embed code from <pre> element
				var embedCode  = $(this).parent('p').prev('pre').text();
				// set a temporary input
				var temp       = $('<input>');
				$('body').append(temp);
				// select the temporary input
				temp.val(embedCode).select();
				// copy the input value
				navigator.clipboard.writeText(embedCode);
				copyHelper.css('opacity', 1);
				// remove the temporary input
				temp.remove();
				setTimeout(function() {
					copyHelper.css('opacity', 0);
					console.log('should be hidden');
				}, 3000);
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

