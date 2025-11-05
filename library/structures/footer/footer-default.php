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

<footer class="container-fluid" id="footer-wrapper" role="contentinfo">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-auto text-center">
				<?php if(isset($settings['social_link']) && is_array($settings['social_link']) && !empty(array_filter($settings['social_link']))): ?>
					<div class="social-links mb-4">
						<?php if(!empty($settings['social_link']['facebook'])): ?>
							<a href="<?php echo $settings['social_link']['facebook']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Facebook">
								<i class="fab fa-facebook-f" aria-hidden="true"></i>
							</a>
						<?php endif; ?>
						<?php if(!empty($settings['social_link']['twitter'])): ?>
							<a href="<?php echo $settings['social_link']['twitter']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On X">
								<i class="fab fa-x-twitter" aria-hidden="true"></i>
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
				<p>
					<a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a> &nbsp;-&nbsp;
					<a class="email-link"></a> &nbsp;-&nbsp;
					<span class="nobr"><?php echo SITE_ADDRESS; ?>,</span> <span class="nobr"><?php echo SITE_CITY; ?>, <?php echo SITE_STATE; ?> <?php echo SITE_ZIP; ?></span>
				</p>
				<hr>
				<p>Copyright &copy; <?php echo date('Y'); ?> <a href="<?php curSiteURL()?>/"><span class="nobr"><?php echo SITE_COMPANY_DBA; ?></span></a> &nbsp;-&nbsp;
					<span class="nobr">
						<a href="/terms-privacy">Terms & Privacy</a>
					</span> &nbsp;-&nbsp;
					<span class="nobr">
						<a href="/accessibility">Accessibility</a>
					</span>
				</p>
			</div>
		</div>
	</div>
</footer>