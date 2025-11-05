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
	 * @var Items\Service[]   $items
	 * @var Membership        $member
	 */
	
	try {
		// Init Pagination
		$pagination = new Pagination();
		$pagination->setQuery("SELECT * FROM `services` WHERE `published` IS TRUE ORDER BY `position` DESC");
		$pagination->setPaginator(30, $dispatcher->getOption('page', 1));
		$pagination->setOriginalPageUrl($dispatcher->getRoute()->getLink());
		
		// Variable Defaults
		$paginator = $pagination->getPaginator();
		$buttons   = $pagination->getButtons();
		$items     = $pagination->getItems(Items\Service::class);
	} catch(Exception $exception) {
		echo Debug::Exception($exception);
		exit;
	}
	
	// Check Page
	if($dispatcher->getOption('page') > $paginator->getPageCount()) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $pagination->formatPageString("Enlightening All™ Services");
	$page_description = $pagination->formatPageString("Enlightening All™ Services");
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mt-0 mb-3 mb-md-4 d-inline-block w-100">
					<?php echo $pagination->formatPageString("Our Services"); ?>
				</h1>
                <div class="song-announcement text-center my-4 p-4">
                    <div style="background: rgba(60, 60, 60, 1); border: 1px solid #ff66cc; border-radius: 10px; padding: 25px; margin: 30px auto; text-align: left; max-width: 700px;">
                        <h3 style="color: #00e6ff; margin-bottom: 10px;">🎟 Special Launch Offer</h3>
                        <ul style="list-style: none; padding-left: 0; margin: 0; font-size: 1rem; color:white;">
                            <li>• Sign up now and receive <b>2 FREE Tickets</b> at a wide variety of classes & events.</li>
                            <!-- <li>• Refer 2 friends → Get <b>2 BFF BOGO Classes</b> for only $4 each.</li>
                            <li><i>(No card or membership required to register.)</i></li> -->
                        </ul>

                        <p style="margin-top: 20px; font-size: 1rem; color:white;">
                            <a href="https://enlighteningall.com/events" target="_self" style="color:white;">➡️ <b>Pre-register here:</b></a><br>
                            Seats, mats, and dates are assigned in the order registrations come in.<br>
                            <b>No payment required,</b> Supplies are limited!<br>
                            Offer valid until <b>Dec 31, 2025</b>.
                        </p>

                        <p style="margin-top: 15px; font-size: 0.95rem; color: #ccc;">
                            ✨ Government ID required at the door. One-time free offer per person.<br>
                            Subject to availability — book early to lock your spot.
                        </p>
                    </div>
                </div>
				<?php
					Render::Component('articles/image-card-article/image-card-article', array(
						'items'             => $items,
						'info'              => TRUE,
						'info_desktop_only' => FALSE,
						'button'           => 'Read More',
						'details'           => array()
					));
				?>
				
				<?php if($paginator->getPageCount() > 1): ?>
					<hr>
					
					<nav aria-label="Blog page navigation.">
						<ul class="pagination justify-content-center">
							<?php if($paginator->isFirst()): ?>
								<li class="page-item disabled">
									<a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
								</li>
							<?php else: ?>
								<li class="page-item">
									<a class="page-link" href="<?php echo $pagination->formatPageLink($paginator->getPage() - 1); ?>">
										Previous
									</a>
								</li>
							<?php endif; ?>
							
							<?php foreach($buttons as $page): ?>
								<?php if(is_int($page)): ?>
									<?php if($page == $paginator->getPage()): ?>
										<li class="page-item active">
											<a class="page-link" href="<?php echo $pagination->formatPageLink($page); ?>">
												<?php echo $page; ?>
											</a>
										</li>
									<?php else: ?>
										<li class="page-item">
											<a class="page-link" href="<?php echo $pagination->formatPageLink($page); ?>">
												<?php echo $page; ?>
											</a>
										</li>
									<?php endif; ?>
								<?php else: ?>
									<li class="page-item disabled">
										<a class="page-link" href="#">
											<?php echo $page; ?>
										</a>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
							
							<?php if($paginator->isLast()): ?>
								<li class="page-item disabled">
									<a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
								</li>
							<?php else: ?>
								<li class="page-item">
									<a class="page-link" href="<?php echo $pagination->formatPageLink($paginator->getPage() + 1); ?>">
										Next
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>

