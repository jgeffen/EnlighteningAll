<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Items\Category    $category
	 * @var Items\Service     $item
	 */
	/**
	 * @var Router\Dispatcher $dispatcher
	 */
	echo '✅ LOADED category.php' . PHP_EOL;
	echo 'URL: ' . $_SERVER['REQUEST_URI'] . PHP_EOL;
	echo 'Intent: ';
	print_r($dispatcher->getIntent());
	die();
	try {
		$category_url = $dispatcher->getOption('category_url');
		
		$stmt = Database::Action("SELECT * FROM `categories` WHERE `page_url` = :page_url", array(
			'page_url' => $category_url
		));
		
		if(!$stmt || !$category = $stmt->fetchObject(Items\Category::class)) {
			Render::ErrorDocument(404);
			exit;
		}
		
		$category_id = $dispatcher->getOption('child_id');
		
		// Then you can fetch the category data
		$category = Database::Action("SELECT * FROM `categories` WHERE `id` = :id", array(
			'id' => $category_id
		))->fetchObject(Items\Category::class);
	} catch(Exception $exception) {
		Debug::Exception($exception);
		Render::ErrorDocument(501);
	}
	
	// Check Page
	if($dispatcher->getOption('page') > $paginator->getPageCount()) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $pagination->formatPageString($category->getTitle());
	$page_description = $pagination->formatPageString($category->getDescription());
	
	$top_image = $category->getLandscapeImage();
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mt-0 mb-3 mb-md-4 d-inline-block w-100">
					<?php echo $pagination->formatPageString($category->getHeading()); ?>dfasdf
				</h1>
				
				<?php $category->renderGallery(); ?>
				
				<?php echo $category->getContent(); ?>
				
				<?php if($category->getYoutubeId()): ?>
					<hr class="clear mb-5">
					
					<div class="row justify-content-center">
						<div class="col-md-8 col-lg-6">
							<?php echo $category->getYoutubeEmbed(); ?>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if($category->getContent() || $category->getYoutubeId()): ?>
					<hr class="clear my-5">
				<?php endif; ?>
				
				<?php
					Render::Component('articles/image-card-article/image-card-article', array(
						'items'  => $items,
						'info'   => TRUE,
						'button' => 'Learn More',
					));
				?>
				
				<?php if($paginator->getPageCount() > 1): ?>
					<hr>
					
					<nav aria-label="Products Page Navigation">
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

