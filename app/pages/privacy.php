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
	
	// Variable Defaults
	$page = Items\Page::FromPageUrl(pathinfo(__FILE__, PATHINFO_FILENAME));
	
	// Check Item
	if(is_null($page)) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $page->getTitle();
	$page_description = $page->getDescription();
	
	// Page Variables
	$top_image = $page->getLandscapeImage();
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1>
					<?php echo $page->getHeading(); ?>
					
					<br>
					
					<small><strong>Last updated</strong> [<?php echo $page->getPublishedDate()->format('M, Y'); ?>]</small>
				</h1>
				
				<?php $page->renderGallery(); ?>
				
				<?php echo $page->getContent(); ?>
				
				<?php if($page->getYoutubeId()): ?>
					<hr class="clear mb-5">
					
					<div class="row justify-content-center">
						<div class="col-md-8 col-lg-6">
							<div class="fitvid">
								<?php echo $page->getYoutubeEmbed(); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if($page->getPDFs()): ?>
					<hr class="clear my-5">
					<?php /* - RENDER ONE PAGE ARTICLE COMPONENT - */ ?>
					<?php
					Render::component('one-page-articles/titlebar-trim-article/titlebar-trim-article', array(
						'items'    => $page->getPDFs(),
						'icon'     => '<i class="fa-light fa-file-pdf"></i>',
						'cols'     => 3,
						'btn_text' => 'Download PDF'
					));
					?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/body-close.php'); ?>

