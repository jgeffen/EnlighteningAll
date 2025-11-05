<?php
	/**
	 * @var array $settings
	 */
?>

<nav class="container-fluid" id="footer-nav">
	<div class="container">
		<div class="row">
			<div class="col-lg-4" aria-hidden="true">
				<div class="footer-logo">
					<div>
						<a href="<?php echo curSiteURL(); ?>/">
							<img src="/images/layout/main-logo-white.png" alt="<?php echo SITE_COMPANY; ?> Logo">
							<?php if(isset($settings['social_link']) && !empty(array_filter($settings['social_link']))): ?>
								<div class="social-links">
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
						</a>
					</div>
				</div>
			</div>
			<?php include('includes/nav-links.php'); ?>
		</div>
	</div>
</nav>
<footer class="container-fluid" id="footer-wrapper" role="contentinfo">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-auto text-center">
				<p>
					<a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a> &nbsp;-&nbsp;
					<a class="email-link"></a> &nbsp;-&nbsp;
					<span class="nobr"><?php echo SITE_ADDRESS; ?>,</span> <span class="nobr"><?php echo SITE_CITY; ?>, <?php echo SITE_STATE; ?> <?php echo SITE_ZIP; ?></span>
				</p>
				<hr>
				<p>Copyright &copy; <?php echo date('Y'); ?> <a href="<?php echo curSiteURL() ?>/"><span class="nobr"><?php echo SITE_COMPANY; ?></span></a> &nbsp;-&nbsp;
					<span class="nobr">
						Created by: <a href="http://www.fenclwebdesign.com/">Fencl Web Design</a>
					</span> &nbsp;-&nbsp;
					<span class="nobr">
						<a href="/terms-privacy.html">Terms & Privacy</a>
					</span> &nbsp;-&nbsp;
					<span class="nobr">
						<a href="/accessibility.html">Accessibility</a>
					</span>
				</p>
			</div>
		</div>
	</div>
</footer>