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
	
	// Set Item
	$item = Secrets\Photographer::Fetch(Database::Action("SELECT * FROM `photographers` WHERE `id` = :table_id", array(
		'table_id' => $dispatcher->getTableId()
	)));
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(404);
?>

<div id="info-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<i class="fas fa-info" aria-hidden="true"></i>
				<h3 class="modal-title"><?php echo sprintf("Viewing Photographer ID #%s", $item->getId()); ?></h3>
			</div>
			
			<div class="modal-body">
				<p><strong>id:</strong> <?php echo $item->getId(); ?></p>
				<p><strong>category_id:</strong> <?php echo $item->getCategoryId(); ?></p>
				<p><strong>page_title:</strong> <?php echo $item->getTitle(); ?></p>
				<p><strong>page_description:</strong> <?php echo $item->getDescription(); ?></p>
				<p><strong>heading:</strong> <?php echo $item->getHeading(); ?></p>
				<p><strong>name:</strong> <?php echo $item->getName(); ?></p>
				<p><strong>email:</strong> <?php echo $item->getEmail(); ?></p>
				<p><strong>phone:</strong> <?php echo $item->getPhone(); ?></p>
				<p><strong>content:</strong> <?php echo $item->getContent(); ?></p>
				<p><strong>youtube_id:</strong> <?php echo $item->getYoutubeId(); ?></p>
				<p><strong>page_url:</strong> <?php echo $item->getPageUrl(); ?></p>
				<p><strong>filename:</strong> <?php echo $item->getFilename(); ?></p>
				<p><strong>filename_alt:</strong> <?php echo $item->getFilenameAlt(); ?></p>
				<p><strong>position:</strong> <?php echo $item->getPosition(); ?></p>
				<p><strong>published:</strong> <?php echo $item->isPublished(); ?></p>
				<p><strong>published_date:</strong> <?php echo $item->getPublishedDate()->format('Y-m-d'); ?></p>
				<p><strong>author:</strong> <?php echo $item->getAuthor(); ?></p>
				<p><strong>user_agent:</strong> <?php echo $item->getUserAgent(TRUE); ?></p>
				<p><strong>ip_address:</strong> <?php echo $item->getIpAddress(); ?></p>
				<p><strong>timestamp:</strong> <?php echo $item->getTimestamp()->format('Y-m-d H:i:s'); ?></p>
				<p><strong>last_timestamp:</strong> <?php echo $item->getLastTimestamp()->format('Y-m-d H:i:s'); ?></p>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
