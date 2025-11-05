<?php
// Render Component
Render::Component('recent-articles/multi-section-list/multi-section-list', array(
	'main_title'       => 'Stay Up to Date',
	'background_color' => 'primary', //OPTIONS: primary, accent, neutral, black, white
	'text_color'       => 'white', //OPTIONS: primary, accent, neutral, black, white
	'sections'         => array(
		array(
			'alt'   => 'Recent Blogs',
			'title' => 'Recent Blogs',
			'link'  => '/blogs',
			'items' => Items\Blog::FetchAll(Database::Action("SELECT * FROM `blogs` WHERE `published` IS TRUE ORDER BY `position` DESC LIMIT 5"))
		),
		array(
			'alt'   => 'Upcoming Events',
			'title' => 'Upcoming Events',
			'link'  => '/events',
			'items' => Items\Event::FetchAll(Database::Action("SELECT * FROM `events` WHERE `published` IS TRUE AND `date_start` >= CURDATE() ORDER BY `date_start`, `date_end` LIMIT 5"))
		),
		array(
			'alt'   => 'Recent News',
			'title' => 'Recent News',
			'link'  => '/news',
			'items' => Items\News::FetchAll(Database::Action("SELECT * FROM `news` WHERE `published` IS TRUE ORDER BY `position` DESC LIMIT 5"))
		)
	)
));
?>

<nav class="container-fluid" id="footer-nav">
	<div class="container">
		<div class="row">
			<div class="col-lg-4" aria-hidden="true">
				<div class="footer-logo">
					<div>
						<a href="<?php echo curSiteURL(); ?>/">
							<img src="/images/layout/main-logo.png" alt="<?php echo SITE_COMPANY; ?> Logo">
						</a>
						<div>
							<br>
							<div class="text-center">
								<span class="d-inline-block">
									Follow Us:
									&nbsp;&nbsp;
									<a href="https://www.facebook.com/SecretsHideaway" class="social-btn" target="_blank" aria-label="Follow Us On Facebook"><i class="fa-brands fa-facebook-square fa-2x" aria-hidden="true"></i></a>
									&nbsp;&nbsp;
									<a href="https://www.instagram.com/secretshideaway/" class="social-btn" target="_blank" aria-label="Follow Us On Inst"><i class="fa-brands fa-instagram-square fa-2x" aria-hidden="true"></i></a>
									&nbsp;&nbsp;
									<a href="https://twitter.com/SecretsHideaway" class="social-btn" target="_blank" aria-label="Follow Us On Twitter"><i class="fa-brands fa-twitter-square fa-2x"></i></a>
									<a class="d-block mt-3" href="https://www.secretssociety.com/" target="_blank"><img src="/images/secrets-society-clothing.png" alt="Secrets Society Clothing Logo"></a>
								</span>
							</div>

							<?php if (isset($settings['social_link']) && !empty(array_filter($settings['social_link']))) : ?>
								<div class="social-links">
									<?php if (!empty($settings['social_link']['facebook'])) : ?>
										<a href="<?php echo $settings['social_link']['facebook']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Facebook">

										</a>
									<?php endif; ?>
									<?php if (!empty($settings['social_link']['twitter'])) : ?>
										<a href="<?php echo $settings['social_link']['twitter']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Twitter">
											<i class="fab fa-twitter" aria-hidden="true"></i>
										</a>
									<?php endif; ?>
									<?php if (!empty($settings['social_link']['instagram'])) : ?>
										<a href="<?php echo $settings['social_link']['instagram']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On Instagram">
											<i class="fab fa-instagram" aria-hidden="true"></i>
										</a>
									<?php endif; ?>
									<?php if (!empty($settings['social_link']['linkedin'])) : ?>
										<a href="<?php echo $settings['social_link']['linkedin']; ?>" class="social-btn" target="_blank" aria-label="Follow Us On LinkedIn">
											<i class="fab fa-linkedin" aria-hidden="true"></i>
										</a>
									<?php endif; ?>
									<?php if (!empty($settings['social_link']['youtube'])) : ?>
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
			<div class="col-sm-6 col-lg-4 equal-col">
				<ul>
					<li><a href="https://www.secretssociety.com/" target="_blank">Secrets Society Clothing</a></li>
					<li><a href="/">Home</a></li>
					<li><a href="/events" title="Swinger Events Orlando, FL">Events</a></li>
					<li><a href="/adult-resort-spa">About Secrets</a></li>
					<li><a href="/news">News</a></li>
					<li><a href="/blogs">Blogs</a></li>
					<li><a href="/club-swinkster">Club Swinkster</a></li>
					<li><a href="/questions" title="Frequently Asked Questions">Frequent Questions</a></li>
					<li><a href="/prices">Event Prices</a></li>
					<li><a href="/club-rules" title="Swingers Club">Event Rules</a></li>
					<li><a href="/book-now">Reservations</a></li>
				</ul>
			</div>
			<div class="col-sm-6 col-lg-4 equal-col">
				<ul>
					<li><a href="/florida-attractions">Nearby Florida Attractions</a></li>
					<li><a href="/galleries">Photo Galleries</a></li>
					<li><a href="/photographers">Photographers</a></li>
					<li><a href="/members" title="Member List">Event Members</a></li>
					<li><a href="/condos" title="Condos for Sale">Condos for Sale</a></li>
					<li><a href="/our-partners" title="Our Partners">Event Partners</a></li>
					<li><a href="/sponsors">Sponsors</a></li>
					<li><a href="/clubs">Clubs</a></li>
					<li><a href="/cancellation-policy">Cancellation Policy</a></li>
					<li><a href="/contact">Contact</a></li>
					<li><a href="/careers">Careers</a></li>
				</ul>
			</div>
		</div>
	</div>
</nav>

<footer class="container-fluid" id="footer-wrapper" role="contentinfo">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-auto text-center">
				<style>
					.grid-to-block {
						display: grid;
					}

					@media (min-width: 992px) {
						.grid-to-block {
							display: block;
						}
					}
				</style>
				<p class="grid-to-block">

					<i class="fa-solid fa-square-phone"></i> TEXT or CALL <a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a> &nbsp;-&nbsp;
					<a class="email-link"></a> &nbsp;-&nbsp;
					<span class="nobr"><?php echo SITE_ADDRESS; ?>,</span>&nbsp;<span class="nobr"><?php echo SITE_CITY; ?>,&nbsp;<?php echo SITE_STATE; ?>&nbsp;<?php echo SITE_ZIP; ?></span> &nbsp;-&nbsp;
					<a href="https://www.google.com/maps/dir//Secrets+Hideaway+Resort+%26+Spa+%2F+Club+Secret,+2145+E+Irlo+Bronson+Memorial+Hwy,+Kissimmee,+FL+34744/@28.289299,-81.3544,15z/data=!4m15!1m6!3m5!1s0x0:0x4df3c9c0d6d59996!2sSecrets+Hideaway+Resort+%26+Spa+%2F+Club+Secret!8m2!3d28.288883!4d-81.356771!4m7!1m0!1m5!1m1!1s0x88dd85e9522b31b9:0x4df3c9c0d6d59996!2m2!1d-81.356771!2d28.288883?hl=en-US" target="_blank">
						Get&nbsp;Directions
					</a>
				</p>
				<hr>
				<p>Copyright &copy; <?php echo date('Y'); ?> <a href="<?php echo curSiteURL('/'); ?>"><span class="nobr"><?php echo SITE_COMPANY; ?></span></a> &nbsp;-&nbsp;
					<span class="nobr">
						<a href="/terms-privacy">Terms & Privacy</a>
					</span> &nbsp;-&nbsp;
					<span class="nobr">
						<a href="/privacy">GDPR Privacy Policy </a>
					</span> &nbsp;-&nbsp;
					<span class="nobr">
						<a href="/accessibility">Accessibility</a>
					</span>
				</p>
			</div>
		</div>
	</div>
</footer>