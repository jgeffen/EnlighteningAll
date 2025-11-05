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
	 * @var Admin\User        $admin
	 */
	
	// Set Title
	$page_title = 'Admin Dashboard';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title">
		<h1>Welcome, <?php echo $admin->getFirstName(); ?>!</h1>
	</div>
	
	<p>For more information on using the admin panel, please visit the <a href="/user/guidelines">Guidelines & Tips</a> page.</p>
	
	<a href="/user/guidelines.html" class="btn btn-outline btn-block-xs mb-2 ml-sm-1">Guidelines & Tips</a>
</main>

<?php include('includes/footer.php'); ?>
<?php include('includes/body-close.php'); ?>



