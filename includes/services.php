<?php
	/**
	 * @var Items\Service $services
	 */
	
	// Set items
	$services = Items\Service::FetchAllString("SELECT * FROM `services` WHERE `published` IS TRUE ORDER BY `position` DESC");
?>

<div class="container-fluid photo-title-boxes">
	<div class="row g-0">
		<?php /** @var Items\Service $service */ ?>
		<?php foreach($services as $service): ?>
			<div class="col-6 col-sm-6 col-md-3 position-relative p-0">
				<a href="<?php echo $service->getLink(); ?>" class="btn-wrap" aria-label="<?php echo $service->getHeading(); ?>">
					<img src="<?php echo $service->getSquareImage(); ?>" alt="<?php echo $service->getHeading(); ?>">
					<h3 class="btn-title equal-title icon" style="height: 65.7344px;">
						<?php echo $service->getIcon(); ?><?php echo $service->getLabel(); ?>
					</h3>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>
