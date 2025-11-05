<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Imports
	use Items\Enums\Sizes;
	
	if(is_null($member)) Render::ErrorDocument(404);
	
	// Variable Defaults
	$items = Items\Event::FetchAll(Database::Action("SELECT * FROM `events` WHERE `teacher_id` = :teacher_id AND `published` IS TRUE ORDER BY `date_end` >= CURDATE() DESC, `date_start`, `date_end`, `page_title`", array(
		'teacher_id' => $member?->getId()
	)));
	
	// Check Item
	if(is_null($items)) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = 'See Who is Attending Your Class';
	$page_description = 'See Who is Attending Your Class';
	
	// Page Variables
	$no_index = TRUE;
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="lightbox">
					<a class="inset border mt-0 mt-sm-0 mt-md-1 mx-auto mb-2" href="/images/seating-chart/seating-chart.png">
						<img class="lazy" src="/images/seating-chart/seating-chart.png" data-src="/images/seating-chart/seating-chart.png" alt="Seating Chart Enlightening All">
					</a>
				</div>
				<?php foreach($items as $item): ?>
					<div class="card mb-4 shadow-sm border-0">
						<div class="card-body">
							<h2 class="h5 mb-2 text-primary"><?php echo $item->getHeading(); ?></h2>
							
							<?php foreach($item->getPackages() as $package): ?>
								Package: <?php echo $package->getName(); ?>
							<?php endforeach; ?>
							
							<p class="text-muted mb-2">
								<i class="fal fa-calendar-alt mr-1"></i>
								<?php echo $item->getDate(); ?>
								<br>
								<i class="fa-solid fa-clock mr-1"></i>
								<?php echo $item->getEventTimes(); ?>
							</p>
							
							<?php if($item->getReservations()): ?>
								<h3 class="h6 mt-3 text-success">
									<sup>*</sup> Members Attending: <?php echo $item->getTotalReservations(); ?>
								</h3>
								
								<hr class="my-4">
								
								<div class="row row-cols-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">
									<?php foreach($item->getReservations() as $reservation): ?>
										<?php if($reservation->getMember()): ?>
											<?php $member = $reservation->getMember(); ?>
											<div class="col text-center" data-profile-id="<?php echo $member->getId(); ?>">
												<?php if($reservation->getSeatSelected()): ?>
													#<?php echo $reservation->getSeatSelected(); ?>
												<?php endif; ?>
												
												<a class="card border-0" href="<?php echo $member->getLink(); ?>" title="@<?php echo $member->getUsername(); ?>">
													<img class="card-img-top rounded-circle mb-1" src="<?php echo Items\Defaults::AVATAR_XS; ?>" data-src="<?php echo $member->getAvatar()?->getImage(Sizes\Avatar::XS); ?>" alt="@<?php echo $member->getUsername(); ?>" loading="lazy">
													
													<div class="card-footer p-1 bg-transparent border-0">
														<p class="text-truncate mb-0 small"><?php echo $member->getFullName(); ?></p>
														<hr>
														<small><?php echo $reservation->getComments(); ?></small>
													</div>
												</a>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>

