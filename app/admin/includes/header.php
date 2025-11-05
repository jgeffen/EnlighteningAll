<?php
	/*
	Copyright (c) 2021, 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var null|Admin\User   $admin
	 */
?>

<!DOCTYPE html>
<html lang="en">
	<?php include('includes/head.php'); ?>
	
	<body id="<?php echo basename(filter_input(INPUT_SERVER, 'REQUEST_URI')); ?>">
		<div class="loading-anim" aria-hidden="true">
			<div class="icon-wrap">
				<i class="fad fa-spinner-third fa-spin"></i>
			</div>
		</div>
		
		<div id="site">
			<header class="container-fluid">
				<div class="row align-items-end">
					<div class="col-7 col-md-6">
						<div class="d-flex align-items-center">
							<a class="minimize-nav menu-btns mr-2 d-none d-md-none" href="#">
								<i class="fal fa-bars"></i>
							</a>
							
							<a id="logo-main" href="<?php echo Helpers::CurrentWebsite(); ?>">
								<em><?php echo SITE_COMPANY; ?></em>
								<span class="nobr">Admin Panel</span>
							</a>
						</div>
					</div>
					
					<?php if(Admin\LoggedIn()): ?>
						<div class="col-5 col-md-6">
							<div class="user-controls">
								<nav id="nav-user" class="dropdown-nav">
									<ul class="nav justify-content-end">
										<li>
											<span class="d-none d-sm-inline"><?php echo $admin->getFullName(); ?></span>
											<a class="menu-btns" href="#">
												<i class="fal fa-user"></i>
											</a>
											
											<ul>
												<li><a href="/user/settings"><i class="fas fa-cog"></i> User Settings</a></li>
												<li><a href="/user/logout"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
											</ul>
										</li>
									</ul>
								</nav>
								
								<a id="main-menu-open" class="open-menu menu-btns" href="#" aria-label="Click Enter to Open Main Menu">
									<i class="fal fa-bars"></i>
								</a>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</header>
			
			<div id="content-wrap">
				<div class="container-fluid main-container">
					<div class="row main-row">
						<?php if(Admin\LoggedIn()): ?>
							<div class="d-none d-md-block col-md-4 col-lg-3 col-xl-2">
								<?php require_once('includes/nav.php'); ?>
							</div>
						<?php endif; ?>
						
						<div class="<?php echo Admin\LoggedIn() ? 'col-md-8 col-lg-9 col-xl-10' : 'col-12'; ?>">
							<main id="ajax-wrapper_wrapper">