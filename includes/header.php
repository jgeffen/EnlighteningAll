<?php
	// TODO: Handle non paginated pages better.
	if(in_array(http_response_code(), array(200)) && isset($options['page']) && empty($pagination)) Render::ErrorDocument(404);
?>
	<!DOCTYPE html>
	<html lang="en">
<?php include('includes/head.php'); ?>

<body <?php echo Membership::LoggedIn() ? 'data-user-online' : ''; ?>>
	<div class="preloader">
		<div class="preloader-inner">
			<div class="preloader-top">
				<div class="preloader-top-sun">
					<div class="preloader-top-sun-bg"></div>
					<div class="preloader-top-sun-line preloader-top-sun-line-0"></div>
					<div class="preloader-top-sun-line preloader-top-sun-line-45"></div>
					<div class="preloader-top-sun-line preloader-top-sun-line-90"></div>
					<div class="preloader-top-sun-line preloader-top-sun-line-135"></div>
					<div class="preloader-top-sun-line preloader-top-sun-line-180"></div>
					<div class="preloader-top-sun-line preloader-top-sun-line-225"></div>
					<div class="preloader-top-sun-line preloader-top-sun-line-270"></div>
					<div class="preloader-top-sun-line preloader-top-sun-line-315"></div>
				</div>
			</div>
			<div class="preloader-bottom">
				<div class="preloader-bottom-line preloader-bottom-line-lg"></div>
				<div class="preloader-bottom-line preloader-bottom-line-md"></div>
				<div class="preloader-bottom-line preloader-bottom-line-sm"></div>
				<div class="preloader-bottom-line preloader-bottom-line-xs"></div>
			</div>
		</div>
	</div>
	
	<div id="main-wrapper">
	

<?php Render::Structure('header/header-membership/header-membership'); ?>

<?php Render::Structure('nav-desktop/nav-desktop-default'); ?>
	
	<main id="content-area" role="main">
			<?php if(!empty($homepage)): ?>
				<?php include('includes/services.php'); ?>
			<?php endif; ?>