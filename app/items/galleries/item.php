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
	
	// TODO: Convert gallery images to class
	
	// Fetch/Set Item
	$item = Items\Gallery::Init($dispatcher->getRoute()?->getTableId());
	
	// Check Item
	if(is_null($item)) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $item->getTitle();
	$page_description = $item->getDescription();
	
	// Page Variables
	$top_image = $item->getLandscapeImage();
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $item->getHeading(); ?></h1>
				
				<?php if($item->hasImage() || $item->getGallery()): ?>
					<div class="row lightbox gallery">
						<?php if($item->hasImage()): ?>
							<div class="col-6 col-md-4 col-lg-3">
								<a class="border" href="<?php echo $item->getImage(); ?>">
									<img class="lazy" src="<?php echo Items\Defaults::LANDSCAPE; ?>" alt="<?php echo $item->getImageAlt(); ?>" data-src="<?php echo $item->getLandscapeImage(); ?>">
								</a>
							</div>
						<?php endif; ?>
						
						<?php if($item->getGallery()): ?>
							<?php foreach($item->getGallery() as $image): ?>
								<?php if(!empty($image['youtube_id'])): ?>
									<div class="video-wrap col-6 col-md-4 col-lg-3">
										<a class="video-img-wrap border" href="<?php echo $image['source']; ?>" title="<?php echo $image['alt']; ?>" data-youtube-id="<?php echo $image['youtube_id']; ?>">
											<img class="lazy" src="<?php echo Items\Defaults::LANDSCAPE; ?>" alt="<?php echo $image['alt']; ?>" data-src="<?php echo $image['thumb']; ?>">
										</a>
									</div>
								<?php else: ?>
									<div class="col-6 col-md-4 col-lg-3">
										<a class="border" href="<?php echo $image['source']; ?>" title="<?php echo $image['alt']; ?>">
											<img class="lazy" src="<?php echo Items\Defaults::LANDSCAPE; ?>" alt="<?php echo $image['alt']; ?>" data-src="<?php echo $image['thumb']; ?>">
										</a>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				
				<p><b>Posted: </b><?php echo $item->getPublishedDate()->format('F jS, Y'); ?></p>
				
				<?php echo $item->getContent(); ?>
				
				<?php if($item->getYoutubeId()): ?>
					<hr class="clear mb-5">
					
					<div class="row justify-content-center">
						<div class="col-md-8 col-lg-6">
							<div class="fitvid">
								<?php echo $item->getYoutubeEmbed(); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if($item->getPDFs()): ?>
					<hr class="clear my-5">
					
					<?php
					Render::Component('one-page-articles/titlebar-trim-article/titlebar-trim-article', array(
						'items'    => $item->getPDFs(),
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

