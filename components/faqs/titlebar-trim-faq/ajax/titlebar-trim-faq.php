<?php
	// Set Item
	$item = Items\Faq::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
	
	// Check Item
	if(is_null($item)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
?>

<div id="titlebar-trim-article-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<?php echo filter_input(INPUT_POST, 'icon'); ?>
				<h3 class="modal-title"><?php echo $item->getQuestion(); ?></h3>
			</div>
			
			<div class="modal-body">
				<?php echo $item->getAnswer(filter_input(INPUT_POST, 'nl2br', FILTER_VALIDATE_BOOLEAN)); ?>
			</div>
		</div>
	</div>
</div>