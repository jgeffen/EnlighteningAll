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
	$category = Members\Category::Init($dispatcher->getCategoryUrl(), 'member_faqs');
	$category?->setItems(Members\Faq::FetchAll(Database::Action("SELECT * FROM `member_faqs` WHERE `category_id` = :category_id ORDER BY `position` DESC", array(
		'category_id' => $category?->getId()
	))));
	
	// Check Category
	if(is_null($category)) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $category->getTitle();
	$page_description = $category->getDescription();
	
	// Page Variables
	$top_image = $category->getLandscapeImage();
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $category->getHeading(); ?></h1>
				
				<a class="btn btn-link mb-3" href="/members/faqs" title="Back to Overview">
					<i class="fa-solid fa-arrow-left-long-to-line"></i> Back to Overview
				</a>
				
				<?php echo $category->getContent(); ?>
				
				<?php if($category->getYoutubeId()): ?>
					<hr class="clear mb-5">
					
					<div class="row justify-content-center">
						<div class="col-md-8 col-lg-6">
							<div class="fitvid">
								<?php echo $category->getYoutubeEmbed(); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<hr class="clear my-5">
				
				<?php
					Render::Component('faqs/titlebar-trim-faq/titlebar-trim-member-faq', array(
						'items'    => $category->getItems(),
						'icon'     => '<i class="fal fa-question-circle"></i>',
						'cols'     => 3,
						'btn_text' => 'Read More',
						'message'  => 'Sorry, no frequently asked questions to show at this time. Please check back soon!',
						'nl2br'    => FALSE,
						'truncate' => 300
					));
				?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>

