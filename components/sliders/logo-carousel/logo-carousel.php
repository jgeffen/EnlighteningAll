<?php
	
	// LOOPS THROUGH THE DATA SOURCE AND ADDS ADDITIONAL REQUIRED FIELDS
	$data_source['data'] = array_map(function($slide) use ($data_source) {
		return array_merge($slide, array(
			'image' => !empty($slide['filename']) ? $data_source['image_path'] . $slide['filename'] : '',
			'alt'   => htmlentities(!empty($slide['filename_alt']) ? $slide['filename_alt'] : '', ENT_QUOTES)
		));
	}, $data_source['data']);

?>

<?php if(!empty($data_source['data'])): ?>
	<div class="logo-carousel-wrap" role="region" aria-label="Logo Carousel">
		<div class="swiper-button-prev"><i class="fa-light fa-chevron-left"></i></div>
		<div class="swiper logo-carousel">
			<div class="swiper-wrapper">
				<?php foreach($data_source['data'] as $slide): ?>
					<div class="swiper-slide" role="region" aria-label="Slide <?php echo $slide['alt']; ?>">
						<a <?php echo !empty($slide['sponsor_link']) ? 'href="' . $slide['sponsor_link'] . '" target="_blank"' : ''; ?>>
							<img src="<?php echo $slide['image']; ?>" alt="<?php echo $slide['alt']; ?>">
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="swiper-button-next"><i class="fa-light fa-chevron-right"></i></div>
	</div>
<?php endif; ?>