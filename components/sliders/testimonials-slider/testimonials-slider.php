<?php if(!empty($options['data'])): ?>
	<div class="swiper testimonials-slider component-background-<?php echo !empty($options['background_color']) ? $options['background_color'] : 'primary'; ?> component-text-<?php echo !empty($options['text_color']) ? $options['text_color'] : 'white'; ?>" role="region" aria-label="Testimonials Slider">
		<div class="swiper-button-prev"><i class="fa-light fa-chevron-left"></i></div>
		<div class="swiper-wrapper">
			<?php foreach($options['data'] as $slide): ?>
				<div class="swiper-slide" role="region" aria-label="Slide <?php echo $slide['full_name']; ?>">
					<div class="slide-content">
						<div class="container">
							<div class="row justify-content-center align-items-center">
								<div class="col-lg-9 col-xl-8">
									<?php if(!empty($slide['testimonial'])): ?>
										<p class="slide-content__testimonial">"<?php echo shortdesc($slide['testimonial'], 450); ?>"</p>
									<?php endif; ?>
									<?php if(!empty($slide['full_name'])): ?>
										<h2><?php echo $slide['full_name']; ?></h2>
									<?php endif; ?>
									<p><em><?php echo date('F jS, Y', strtotime($slide['timestamp'])); ?></em></p>
									<a href="/testimonials.html" class="btn btn-<?php echo !empty($options['button']) ? $options['button'] : 'outline-white'; ?> mt-4" title="View All Testimonials">View All Testimonials</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="swiper-button-next"><i class="fa-light fa-chevron-right"></i></div>
		
		<div class="swiper-pagination"></div>
	</div>
<?php endif; ?>