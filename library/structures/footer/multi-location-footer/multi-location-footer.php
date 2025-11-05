<footer class="container-fluid" id="footer-wrapper" role="contentinfo">
	<div class="container">
		<div class="row justify-content-sm-center">
			<div class="col-12 col-sm-6 col-md-4 col-lg-3">
				<p>
					<b>Location One</b><br>
					<a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a><br>
					<a class="email-link"></a><br>
					<?php echo SITE_ADDRESS; ?><br>
					<?php echo SITE_CITY; ?>, <?php echo SITE_STATE; ?> <?php echo SITE_ZIP; ?>
				</p>
			</div>
			<div class="col-12 d-sm-none">
				<hr class="mt-2 mb-4">
			</div>
			<div class="col-12 col-sm-6 col-md-4 col-lg-3">
				<p>
					<b>Location Two</b><br>
					<a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a><br>
					<a class="email-link"></a><br>
					<?php echo SITE_ADDRESS; ?><br>
					<?php echo SITE_CITY; ?>, <?php echo SITE_STATE; ?> <?php echo SITE_ZIP; ?>
				</p>
			</div>
			<div class="d-none d-md-block col-md-4 col-lg-3">
				<p>
					<b>Licensed & Insured</b><br>
						CCC#2453434<br>
						CBC#347898
				</p>
			</div>
			<div class="d-none d-lg-block col-lg-3 col-xl-3 align-self-center">
				<a href="<?php echo curSiteURL(); ?>/"><img src="/images/layout/main-logo-white.png" id="footer-logo" class="ml-lg-auto" alt="Poe Roofing Logo"></a>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col text-center">
				<hr>
				<p>Copyright &copy; <?php echo date('Y'); ?> <a href="<?php curSiteURL()?>/"><span class="nobr"><?php echo SITE_COMPANY; ?></span></a> &nbsp;-&nbsp;
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