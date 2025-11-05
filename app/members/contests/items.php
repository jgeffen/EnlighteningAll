<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Items\Contest[]   $items
	 * @var Membership        $member
	 */
	
	try {
		// Init Pagination
		$pagination = new Pagination();
		$pagination->setQuery("SELECT * FROM `contests` WHERE `published` IS TRUE ORDER BY CURDATE() BETWEEN `date_start` AND `date_end` DESC, `date_start` DESC, `date_end`");
		$pagination->setPaginator(30, $dispatcher->getOption('page', 1));
		$pagination->setOriginalPageUrl('/members/contests');
		
		// Variable Defaults
		$paginator = $pagination->getPaginator();
		$buttons   = $pagination->getButtons();
		$items     = $pagination->getItems(Items\Contest::class);
	} catch(Exception $exception) {
		echo Debug::Exception($exception);
		exit;
	}
	
	// Check Page
	if($dispatcher->getOption('page') > $paginator->getPageCount()) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $pagination->formatPageString("Enlightening All™ Contests");
	$page_description = $pagination->formatPageString("Enlightening All™ Contests");
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mt-0 mb-3 mb-md-4 d-inline-block w-100">
					<?php echo $pagination->formatPageString("Enlightening All™ Contests"); ?>
				</h1>
				
				<?php
					Render::Component('articles/image-card-article/image-card-article', array(
						'items'             => $items,
						'info'              => TRUE,
						'info_desktop_only' => FALSE,
						'button'            => 'Read More',
						'date_format'       => 'm/d/Y',
						'details'           => array(
							'Start Date: '        => 'date_start',
							'End Date: '          => 'date_end',
							'Number of Winners: ' => 'number_of_winners'
						)
					));
				?>
				
				<?php if($paginator->getPageCount() > 1): ?>
					<hr>
					
					<nav aria-label="Contests page navigation.">
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

