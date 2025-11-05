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
	 * @var Membership        $member
	 */
	
	// Search Engine Optimization
	$page_title       = $member->getTitle('Dashboard');
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1>Dashboard</h1>
				
				<?php Debug::ShowData($member->getContacts()); ?>
				
				<?php foreach($member->getContacts() as $contact): ?>
					<?php echo $contact->contactListItem($member); ?>
				<?php endforeach; ?>
				
				<?php Debug::ShowData($member->toArray()); ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>
