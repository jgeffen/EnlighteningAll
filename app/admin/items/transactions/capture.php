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
	
	// Variable Defaults
	$page_title = 'Capture Transaction';
	
	// Set Transaction
	$transaction = Items\Transaction::Fetch(Database::Action("SELECT * FROM `transactions` WHERE `table_name` = :table_name AND `table_id` = :table_id AND `id` = :id", array(
		'table_name' => $dispatcher->getTableName(),
		'table_id'   => $dispatcher->getTableId(),
		'id'         => $dispatcher->getId()
	)));
	
	// Check Transaction
	if(is_null($transaction)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
	
	// Set Item
	$item = match ($transaction->getTableName()) {
		'photographer_packages' => Secrets\Photographers\Package::Init($transaction->getTableId()),
		default                 => NULL
	};
	
	// Check Item
	if(is_null($item)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
?>

<div id="capture-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<i class="fas fa-check-square" aria-hidden="true"></i>
				<h3 class="modal-title"><?php echo $page_title ?></h3>
			</div>
			
			<div class="modal-body">
				<form class="form-horizontal">
					<?php if($item instanceof Secrets\Photographers\Package): ?>
						<?php if(!is_null($item->getPhotographer())): ?>
							<div class="form-group">
								<label for="photographer">Photographer</label>
								
								<div class="feedback-wrap">
									<input id="photographer" class="form-control" type="text" value="<?php echo $item->getPhotographer()->getEncoded('name'); ?>" readonly>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					
					<div class="form-group">
						<label for="heading">Heading</label>
						
						<div class="feedback-wrap">
							<input id="heading" class="form-control" type="text" value="<?php echo $item->getEncoded('heading'); ?>" readonly>
						</div>
					</div>
					
					<div class="form-group">
						<label for="amount">Amount</label>
						
						<div class="feedback-wrap">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">
										<i class="fal fa-dollar-sign"></i>
									</span>
								</div>
								<input id="amount" class="form-control" type="text" name="amount" data-format="number" value="<?php echo $transaction->getAmount(); ?>">
							</div>
						</div>
						
						<p class="note">
							<strong>Note:</strong> The amount cannot be greater than the authorized amount of <u><?php echo $transaction->getAmount(TRUE); ?></u>.
						</p>
					</div>
					
					<div class="form-collapse mb-4">
						<div id="advanced-options" class="collapse">
							<div class="form-group">
								<label for="email-receipt">Email Receipt?</label>
								
								<div class="select-wrap form-control">
									<select id="email-receipt" name="email_receipt">
										<?php foreach(array(1 => 'Yes', 0 => 'No') as $value => $label): ?>
											<option value="<?php echo $value; ?>">
												<?php echo $label; ?>
											</option>
										<?php endforeach; ?>
									</select>
									<div class="select-box"></div>
								</div>
							</div>
						</div>
						
						<hr>
						
						<div class="text-center">
							<a class="btn btn-outline" href="#" aria-controls="advanced-options" aria-expanded="false" data-target="#advanced-options" data-toggle="collapse" role="button">
								Advanced Options
							</a>
						</div>
					</div>
					
					<div class="form-btns text-right">
						<div class="float-lg-right">
							<button class="btn btn-success btn-block-md mb-2" type="submit">
								<i class="fal fa-save"></i> Process
							</button>
							
							<button class="btn btn-danger btn-block-md ml-lg-1 mb-2" data-dismiss="modal">
								<i class="fal fa-ban"></i> Cancel
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
