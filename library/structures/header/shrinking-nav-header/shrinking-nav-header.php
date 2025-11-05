<div id="header-spacer">
	<header class="container-fluid" id="header-wrapper" role="banner">
		<div class="container" id="header">
			<div class="row align-items-center">
				<div class="col-9 col-xl-3">
					<a href="<?php echo curSiteURL(); ?>/"><img src="/images/layout/main-logo.png" id="main-nav-logo" alt="<?php echo SITE_COMPANY; ?> Logo"></a>
				</div>
				<div class="col-3 col-xl-7">
					<nav id="nav-bar-wrap" role="navigation">
						<div class="nav-content nav-bar-lg d-none d-xl-block">
							<?php include('includes/nav-links.php'); ?>
						</div>
						<div class="nav-content nav-bar-sm d-xl-none">
							<ul>
								<li><a href="tel: <?php echo SITE_PHONE; ?>"><i class="fal fa-phone" style="font-size: 23px;" title="Call <?php echo SITE_COMPANY; ?>"></i></a></li>						
								<li><a href="#" class="open-menu" title="Click Enter to Open Main Menu"><i class="fal fa-bars" aria-label="Main Menu"></i></a></li>
							</ul>
						</div>
					</nav>
				</div>
				<div class="d-none d-xl-block col-xl-2">
					<a href="tel: <?php echo SITE_PHONE; ?>" class="btn btn-block btn-outline"><?php echo SITE_PHONE; ?></a>
				</div>
			</div>
		</div>
	</header>
</div>