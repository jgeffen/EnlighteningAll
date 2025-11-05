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
	$items = Items\Sponsor::FetchAll(Database::Action("SELECT * FROM `sponsors` WHERE `published` IS TRUE ORDER BY `position` DESC"));
	
	// Search Engine Optimization
	$page_title       = "Our Sponsors";
	$page_description = "Enlightening All™ Sponsors";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1>Enlightening All™ Sponsors</h1>
				
				<div class="text-center">
					<?php if(!empty($items)): ?>
						<?php foreach($items as $item): ?>
							<?php if($item->getScript()): ?>
								<?php echo $item->getScript(); ?>
							<?php else: ?>
								<?php if($item->hasImage()): ?>
									<?php if($item->hasAnalytics()): ?>
										<p>
											<a href="<?php echo $item->getLink(); ?>" rel="nofollow" target="_blank" title="<?php echo $item->getImageAlt(); ?>" data-analytics-action="click">
												<img src="<?php echo $item->getImage(); ?>" alt="<?php echo $item->getImageAlt(); ?>" class="img-thumbnail">
											</a>
										</p>
									<?php else: ?>
										<p>
											<a href="<?php echo $item->getLink(); ?>" rel="nofollow" target="_blank" title="<?php echo $item->getImageAlt(); ?>">
												<img src="<?php echo $item->getImage(); ?>" alt="<?php echo $item->getImageAlt(); ?>" class="img-thumbnail">
											</a>
										</p>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
							
							<h3><?php echo $item->getHeading(); ?></h3>
							
							<?php echo $item->getContent(); ?>
							
							<hr>
						<?php endforeach; ?>
					<?php else: ?>
						<h3>Sorry, no sponsors to display. Please check back soon!</h3>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/body-close.php'); ?>

