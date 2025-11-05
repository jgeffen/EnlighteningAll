<?php
	/**
	 * @var Gallery $qd_item
	 * @var string  $qd_margin
	 * @var bool    $qd_inset
	 * @var string  $qd_inset_position
	 * @var string  $qd_single_img_classes
	 */
	
	// Imports
	use Items\Traits\Gallery;
	
	// Set Classes
	$classes = implode(' ', array(
		$qd_inset ? 'inset-carousel' : '',
		$qd_inset_position && $qd_inset ? $qd_inset_position : '',
		$qd_margin && !$qd_inset ? $qd_margin : ''
	));
?>

<?php if($qd_item->getGallery()): ?>
	<section class="gallery-carousel-wrap <?php echo $classes; ?>" aria-label="Photo Gallery Carousel">
		<div class="swiper gallery-top">
			<div class="swiper-wrapper lightbox">
				<?php if($qd_item->hasImage()): ?>
					<div class="swiper-slide">
						<a href="<?php echo $qd_item->getImage(); ?>" title="<?php echo $qd_item->getFilenameAlt(); ?>">
							<img src="<?php echo $qd_item->getLandscapeImage(); ?>" alt="<?php echo $qd_item->getFilenameAlt(); ?>">
						</a>
					</div>
				<?php endif; ?>
				
				<?php if($qd_item->getGallery()): ?>
					<?php foreach($qd_item->getGallery() as $image): ?>
						<div class="swiper-slide">
							<a href="<?php echo $image['source']; ?>">
								<img src="<?php echo $image['featured']; ?>" alt="<?php echo $image['alt']; ?>">
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
				<?php if(!empty($qd_item->hasImage())): ?>
					<div class="swiper-slide">
						<img src="<?php echo $qd_item->getLandscapeThumb(); ?>" alt="<?php echo $qd_item->getFilenameAlt(); ?>">
					</div>
				<?php endif; ?>
				
				<?php if($qd_item->getGallery()): ?>
					<?php foreach($qd_item->getGallery() as $image): ?>
						<div class="swiper-slide">
							<img src="<?php echo $image['thumb']; ?>" alt="<?php echo $image['alt']; ?>">
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php elseif($qd_item->hasImage()): ?>
	<div class="lightbox">
		<a href="<?php echo $qd_item->getImage(); ?>" class="<?php echo $qd_single_img_classes; ?>">
			<img src="/images/layout/default-landscape.jpg" data-src="<?php echo $qd_item->getLandscapeImage(); ?>" class="lazy" alt="<?php echo $qd_item->getFilenameAlt(); ?>">
		</a>
	</div>
<?php endif; ?>