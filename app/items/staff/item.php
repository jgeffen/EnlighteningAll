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
	$item = Items\Staff::Init($dispatcher->getRoute()?->getTableId());
	
	// Check Item
	if(is_null($item)) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $item->getTitle();
	$page_description = $item->getDescription();
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $item->getHeading(); ?> | <?php echo $item->getJobTitle(); ?></h1>
				
				<hr class="mb-3">
				
				<div class="row">
					<div class="col-lg-7">
						<?php echo $item->getContent(); ?>
					</div>
					
					<div class="col-lg-5">
						<?php if($item->getGallery()): ?>
							<section class="gallery-carousel-wrap right inset border mt-0 mt-sm-0 mt-md-1" aria-label="Photo Gallery Carousel">
								<div class="swiper gallery-top">
									<div class="swiper-wrapper lightbox">
										<?php if($item->hasImage()): ?>
											<div class="swiper-slide">
												<a href="<?php echo $item->getImage(); ?>" title="<?php echo $item->getFilenameAlt(); ?>">
													<img src="<?php echo $item->getSquareImage(); ?>" alt="<?php echo $item->getFilenameAlt(); ?>">
												</a>
											</div>
										<?php endif; ?>
										
										<?php if($item->getStaffGallery()): ?>
											<?php foreach($item->getStaffGallery() as $image): ?>
												<div class="swiper-slide">
													<a href="<?php echo $image['source']; ?>">
														<img src="<?php echo $image['thumb']; ?>" alt="<?php echo $image['alt']; ?>">
													</a>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
									<div class="swiper-button-prev"><i class="fa-light fa-chevron-left"></i></div>
									<div class="swiper-button-next"><i class="fa-light fa-chevron-right"></i></div>
								</div>
								
								<div class="swiper gallery-thumbs" aria-hidden="true">
									<div class="swiper-wrapper">
										<?php if(!empty($item->hasImage())): ?>
											<div class="swiper-slide">
												<img src="<?php echo $item->getSquareThumb(); ?>" alt="<?php echo $item->getFilenameAlt(); ?>">
											</div>
										<?php endif; ?>
										
										<?php if($item->getStaffGallery()): ?>
											<?php foreach($item->getStaffGallery() as $image): ?>
												<div class="swiper-slide">
													<img src="<?php echo $image['thumb']; ?>" alt="<?php echo $image['alt']; ?>">
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								</div>
							</section>
						<?php elseif($item->hasImage()): ?>
							<div class="lightbox">
								<a href="<?php echo $item->getImage(); ?>" class="right inset border mt-0 mt-sm-0 mt-md-1">
									<img src="/images/layout/default-square.jpg" data-src="<?php echo $item->getSquareImage(); ?>" class="lazy" alt="<?php echo $item->getFilenameAlt(); ?>">
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
				
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
					Render::component('one-page-articles/titlebar-trim-article/titlebar-trim-article', array(
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
