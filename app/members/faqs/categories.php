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
	
	// Fetch/Set Items
	$items = Members\Category::FetchAll(Database::Action("SELECT * FROM `categories` WHERE `table_name` = :table_name ORDER BY `position` DESC", array(
		'table_name' => 'member_faqs'
	)));
	
	// Search Engine Optimization
	$page_title       = "";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-5">Frequently Asked Questions</h1>
				
				<?php
					Render::Component('articles/image-card-article/image-card-article', array(
						'items'             => $items,
						'info'              => TRUE,
						'info_desktop_only' => FALSE,
						'hide_image'        => TRUE,
						'button'            => 'Read More'
					));
				?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>

