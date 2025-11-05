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
	
	// TODO: Use setting to place image sizes
	// TODO: Use setting to allow manage images
	
	// Variable Defaults
	$page_title = 'Edit Image';
	
	// Set Item
	$item = Items\Image::Fetch(Database::Action("SELECT * FROM `images` WHERE `id` = :id AND `table_name` = :table_name AND `table_id` = :table_id", array(
		'id'         => $dispatcher->getId(),
		'table_name' => $dispatcher->getTableName(),
		'table_id'   => $dispatcher->getTableId()
	)));
	
	// Check Item
	if(empty($item)) {
		http_response_code(404);
		exit;
	}
?>

<div id="edit-images-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1">
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
						<img class="img-fluid" src="<?php echo $item->getLandscapeImage(); ?>">
					</div>
					
					<div class="form-group">
						<label for="filename-alt">Image Alt:</label>
						
						<div class="feedback-wrap">
							<input id="filename-alt" class="form-control" type="text" name="filename_alt" value="<?php echo $item->getEncoded('filename_alt'); ?>">
						</div>
						
						<p class="note">
							<strong>Note:</strong> Image alt text is used for ADA compliance. Provide an accurate description of the image provided.
						</p>
					</div>
					
					<div class="form-collapse mb-4">
						<div id="advanced-options" class="collapse">
							<div class="form-group">
								<label for="youtube-id">YouTube ID</label>
								
								<div class="feedback-wrap">
									<input id="youtube-id" class="form-control" type="text" name="youtube_id" value="<?php echo $item->getEncoded('youtube_id'); ?>">
								</div>
							</div>
							
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
