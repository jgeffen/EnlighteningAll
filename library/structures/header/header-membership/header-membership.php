<?php
	
	$settings = array(
		'social_link' => array(
			'facebook'  => 'https://www.facebook.com/bretfencl',
			'twitter'   => 'https://x.com/bretfencl',
			'instagram' => 'https://www.instagram.com/bretfencl/',
			'linkedin'  => 'https://www.linkedin.com/bretfencl/',
			'youtube'   => 'https://www.youtube.com/@creatinglightinthedark/'
		)
	);
?>

<nav id="link-bar" class="container-fluid" aria-label="Link Bar">
	<div class="container">
		<div class="row justfy-content-end align-items-center">
			<div class="col-7 col-sm mr-auto" style="min-width: 0;">
				<?php if(Membership::LoggedIn()) : ?>
					<?php $membership_head = new Membership(); ?>
					<a href="<?php echo $membership_head->getLink(); ?>" class="d-block">
						<p class="text-truncate m-0">
							<img class="avatar profile-img-xs mr-1" src="<?php echo Items\Defaults::AVATAR_XS; ?>">
							@<?php echo $membership_head->getUsername(); ?> <?php echo $membership_head->isIdVerified() ? '<i class="fa-solid fa-badge-check" alt="ID Verified" title="ID Verified"></i>' : ""; ?>
						</p>
					</a>
				<?php else : ?>
					<a href="/members/login"><i class="fas fa-lock"></i> Login</a>
				<?php endif; ?>
			</div>
			
			<div class="col-auto d-none d-sm-block">
				<a href="/members/subscription"><i class="fas fa-usd-square"></i> Subscriptions</a>
			</div>
			<div class="col-5 col-sm-auto text-right text-sm-left">
				<a href="/contact"><i class="fas fa-envelope"></i> Contact</a>
			</div>
		</div>
	</div>
</nav>

<header class="container-fluid" id="header-wrapper" role="banner">
	<div class="container" id="header">
		<div class="row justify-content-center justify-content-md-between align-items-center">
			<div class="d-none d-lg-block col-lg-4">
				<div id="header-quote">
					<i class="fa-solid fa-code"></i>
					<p>
						<a style="color: #FFF;" href="https://www.fenclwebdesign.com/" target="_blank">Fencl Web Design's</a><br>
						New Location<br>
						ENLIGHTENING ALL™
					</p>
				</div>
			</div>
			
			<div class="col-10 col-sm-8 col-md-5 col-lg-4 col-xl-3">
				<a href="<?php echo curSiteURL(); ?>">
					&nbsp;
				</a>
			</div>
			
			<div class="d-none d-md-block col-md-5 col-lg-4">
				<div id="header-contact">
					<h3 class="mb-0"><a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a></h3>
					<p class="header-phone"><a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a></p>
					<?php if(isset($settings['social_link']) && is_array($settings['social_link']) && !empty(array_filter($settings['social_link']))) : ?>
						<div class="social-links mt-3 justify-content-md-end">
							<?php if(!empty($settings['social_link']['facebook'])) : ?>
								<a href="<?php echo $settings['social_link']['facebook']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Facebook">
									<i class="fab fa-facebook-f" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if(!empty($settings['social_link']['twitter'])) : ?>
								<a href="<?php echo $settings['social_link']['twitter']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Twitter">
									<i class="fab fa-x-twitter" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if(!empty($settings['social_link']['instagram'])) : ?>
								<a href="<?php echo $settings['social_link']['instagram']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Instagram">
									<i class="fab fa-instagram" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if(!empty($settings['social_link']['linkedin'])) : ?>
								<a href="<?php echo $settings['social_link']['linkedin']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On LinkedIn">
									<i class="fab fa-linkedin" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if(!empty($settings['social_link']['youtube'])) : ?>
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