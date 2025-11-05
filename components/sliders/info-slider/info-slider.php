<?php
	/**
	 * @var string $qd_background
	 * @var string $qd_background_color
	 * @var string $qd_button
	 * @var string $qd_content_position
	 * @var string $qd_image_path
	 * @var string $qd_position
	 * @var string $qd_set_table
	 * @var array  $qd_slides
	 * @var string $qd_table
	 * @var string $qd_text_color
	 */
	
	// Reset Fields
	$qd_slides = array_filter(array_map(function($slide) use ($qd_image_path) {
		// Check Expiration
		if(!empty($slide['expiration'])) {
			// Check Expiration Date
			if($slide['expiration_date'] <= date('Y-m-d')) {
				// Check Deletion
				if(!empty($slide['delete_on_expiration'])) {
					try {
						// Remove File
						System::RemoveFile($slide['filename'], 'sliders', $slide['id']);
						
						// Update Database
						System::Action("DELETE FROM `sliders` WHERE `id` = :id", array('id' => $slide['id']));
						
						// Log Action
						System::LogAction('Delete', 'sliders', $slide['id']);
					} catch(Exception $exception) {
						// Email Error to Developers
						mail(DEV_EMAIL, DEV_SUBJ, $exception->getMessage(), DEV_FROM);
					}
				}
				
				return array();
			}
		}
		
		return array_merge($slide, array(
			'image' => Render::Images(sprintf("%s/%s", rtrim($qd_image_path, '/'), $slide['filename'])),
			'alt'   => htmlentities(!empty($slide['filename_alt']) ? $slide['filename_alt'] : $slide['heading'], ENT_QUOTES)
		));
	}, $qd_slides ?? array()));
?>

<?php if(!empty($qd_slides)): ?>
	<div class="swiper info-slider" role="region" aria-label="Photo Slider">
		<div class="swiper-wrapper">
			<?php foreach($qd_slides as $slide): ?>
				<div class="swiper-slide slide-content-<?php echo $qd_content_position ?: 'right'; ?> <?php echo !empty($qd_background) && (!empty($slide['title']) || !empty($slide['content'])) ? $qd_background : 'colored-box'; ?>" role="region" aria-label="Slide <?php echo $slide['alt']; ?>">
					<?php if(!empty($slide['title']) || !empty($slide['content'])): ?>
						<img src="<?php echo $slide['image']; ?>?v=2023-05-03" alt="<?php echo $slide['alt']; ?>">
						
						<div class="slider-content equal-slide background-color-<?php echo $qd_background_color ?: 'primary'; ?>">
							<div class="slider-content-wrap text-color-<?php echo $qd_text_color ?: 'white'; ?>">
								<?php if(!empty($slide['title'])): ?>
									<h2><?php echo $slide['title']; ?></h2>
								<?php endif; ?>
								
								<?php if(!empty($slide['content'])): ?>
									<p><?php echo $slide['content']; ?></p>
								<?php endif; ?>
								
								<?php if(!empty($slide['link_text'])): ?>
									<a href="<?php echo $slide['link']; ?>" class="btn btn-<?php echo $qd_button ?: 'accent'; ?>" tabindex="0" aria-hidden="true"><?php echo $slide['link_text']; ?></a>
								<?php endif; ?>
							</div>
						</div>
					<?php elseif(!empty($slide['link'])): ?>
						<?php if(!empty($slide['analytics'])): ?>
							<a href="<?php echo $slide['link']; ?>" rel="nofollow" target="_blank" title="<?php echo $slide['alt']; ?>" data-analytics-action="click">
								<img src="<?php echo $slide['image']; ?>?v=2023-05-03" alt="<?php echo $slide['alt']; ?>">
							</a>
						<?php else: ?>
							<a href="<?php echo $slide['link']; ?>" rel="nofollow" target="_blank" title="<?php echo $slide['alt']; ?>">
								<img src="<?php echo $slide['image']; ?>?v=2023-05-03" alt="<?php echo $slide['alt']; ?>">
							</a>
						<?php endif; ?>
					<?php else: ?>
						<img src="<?php echo $slide['image']; ?>?v=2023-05-03" alt="<?php echo $slide['alt']; ?>">
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		
		<div class="swiper-pagination"></div>
	</div>
<?php endif; ?>