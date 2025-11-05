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
	
	// TODO: Use setting to allow manage pdfs
	
	// Variable Defaults
	$page_title = 'Edit PDF';
	
	// Set Item
	$item = Items\PDF::Fetch(Database::Action("SELECT * FROM `pdfs` WHERE `id` = :id AND `table_name` = :table_name AND `table_id` = :table_id", array(
		'id'         => $dispatcher->getId(),
		'table_name' => $dispatcher->getTableName(),
		'table_id'   => $dispatcher->getTableId()
	)));
	
	// Check Item
	if(empty($item)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
?>

<div id="edit-pdfs-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<i class="fal fa-edit" aria-hidden="true"></i>
				<h3 class="modal-title"><?php echo $page_title ?></h3>
			</div>
			
			<div class="modal-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label for="title-input">Title</label>
						
						<div class="feedback-wrap">
							<input id="title-input" class="form-control" type="text" name="title" maxlength="50" value="<?php echo $item->getEncoded('title'); ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label for="description">Description</label>
						
						<div class="feedback-wrap">
							<input id="description" class="form-control" type="text" name="description" maxlength="150" value="<?php echo $item->getEncoded('description'); ?>">
						</div>
					</div>
					
					<div class="form-collapse mb-4">
						<div id="advanced-options" class="collapse">
							<div class="form-group">
								<label for="published">Published?</label>
								
								<div class="select-wrap form-control">
									<select id="published" name="published" data-value="<?php echo (int)$item->isPublished(); ?>">
										<?php foreach(array(1 => 'Yes', 0 => 'No') as $value => $label): ?>
											<option value="<?php echo $value; ?>">
												<?php echo $label; ?>
											</option>
										<?php endforeach; ?>
									</select>
									<div class="select-box"></div>
								</div>
							</div>
							
							<div class="form-group">
								<label for="published-date">Published Date:</label>
								
								<div class="feedback-wrap">
									<input id="published-date" class="form-control" type="text" name="published_date" value="<?php echo $item->getEncoded('published_date'); ?>">
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
								<i class="fal fa-save"></i> Save
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
