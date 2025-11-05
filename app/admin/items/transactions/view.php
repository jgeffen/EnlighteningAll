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
	
	// Check Table Name
	if(!empty($dispatcher->getTableName())) {
		// Set Transaction
		$transaction = Items\Transaction::Fetch(Database::Action("SELECT * FROM `transactions` WHERE `table_name` = :table_name AND `table_id` = :table_id AND `id` = :id", array(
			'table_name' => $dispatcher->getTableName(),
			'table_id'   => $dispatcher->getTableId(),
			'id'         => $dispatcher->getId()
		)));
	} else {
		// Set Transaction
		$transaction = Items\Transaction::Fetch(Database::Action("SELECT * FROM `transactions` WHERE `id` = :id", array(
			'id' => $dispatcher->getTableId()
		)));
	}
	
	// Check Transaction
	if(is_null($transaction)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
?>

<div id="view-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<i class="far fa-search" aria-hidden="true"></i>
				<h3 class="modal-title"><?php echo sprintf("Viewing Transaction ID #%s", $transaction->getId()); ?></h3>
			</div>
			
			<div class="modal-body">
				<?php foreach($transaction->toArray() as $label => $value) : ?>
					<?php if(!empty($value)) : ?>
						<p><strong><?php echo Helpers::PrettyTitle($label); ?>:</strong> <?php echo nl2br($value); ?></p>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>