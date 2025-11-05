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
	
	// Fetch/Set Item
	$item = Members\Faq::Init($dispatcher->getPageUrl());
	
	// Check Item
	if(is_null($item)) Render::ErrorDocument(404);
	
	// Category Check
	if($item->getCategory()?->getPageUrl() != $dispatcher->getCategoryUrl()) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $item->getQuestion();
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $item->getQuestion(); ?></h1>
				
				<?php if(!is_null($item->getCategory())): ?>
					<a class="btn btn-link mb-3" href="<?php echo $item->getCategory()->getLink(); ?>" title="<?php echo sprintf("Back to %s", $item->getCategory()->getAlt()); ?>">
						<i class="fa-solid fa-arrow-left-long-to-line"></i> Back to <?php echo $item->getCategory()->getName(); ?>
					</a>
				<?php endif; ?>
				
				<?php echo $item->getAnswer(); ?>
				
				<p><b>Last Updated: </b><?php echo $item->getLastTimestamp()->format('F jS, Y'); ?></p>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>

