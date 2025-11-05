<header class="container-fluid" id="header-wrapper" role="banner">
	<div class="container" id="header">
		<div class="row justify-content-center justify-content-md-between align-items-center">
			<div class="d-none d-lg-block col-lg-4">
				<div id="header-quote">
					<i class="fal fa-map-marker-check"></i>
					
					<p></p>
				</div>
			</div>
			<div class="col-10 col-sm-8 col-md-5 col-lg-4 col-xl-3">
				<a href="<?php echo curSiteURL(); ?>/">
					<img src="/images/layout/main-logo.png" id="main-logo" alt="<?php echo SITE_COMPANY; ?> Logo">
				</a>
			</div>
			<div class="d-none d-md-block col-md-5 col-lg-4">
				<div id="header-contact">
					<p class="header-phone"><a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a></p>
					<p class="header-email"><a class="email-link"></a></p>
					<?php if(isset($settings['social_link']) && is_array($settings['social_link']) && !empty(array_filter($settings['social_link']))): ?>
						<div class="social-links mt-3 justify-content-md-end">
							<?php if(!empty($settings['social_link']['facebook'])): ?>
								<a href="<?php echo $settings['social_link']['facebook']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Facebook">
									<i class="fab fa-facebook-f" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if(!empty($settings['social_link']['twitter'])): ?>
								<a href="<?php echo $settings['social_link']['twitter']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Twitter">
									<i class="fab fa-twitter" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if(!empty($settings['social_link']['instagram'])): ?>
								<a href="<?php echo $settings['social_link']['instagram']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Instagram">
									<i class="fab fa-instagram" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if(!empty($settings['social_link']['linkedin'])): ?>
								<a href="<?php echo $settings['social_link']['linkedin']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On LinkedIn">
									<i class="fab fa-linkedin" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if(!empty($settings['social_link']['youtube'])): ?>
								<a href="<?php echo $settings['social_link']['youtube']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On YouTube">
									<i class="fab fa-youtube" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</header>