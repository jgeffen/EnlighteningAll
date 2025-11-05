<?php
	/** @noinspection ALL */
	
	// Variable Defaults
	$table_name    = filter_input(INPUT_POST, 'table_name');
	$table_id      = filter_input(INPUT_POST, 'table_id', FILTER_VALIDATE_INT);
	$title_field   = filter_input(INPUT_POST, 'title_field');
	$content_field = filter_input(INPUT_POST, 'content_field');
	
	// Query Table
	$tableCheck = Database::Action("SELECT * FROM `information_schema`.`columns` WHERE `table_schema` = :table_schema AND `table_name` = :table_name AND `column_name` IN (:title_field, :content_field)", array(
		'table_schema'  => Database::Action("SELECT IFNULL(DATABASE(), '_') AS `table_schema`")->fetch(PDO::FETCH_COLUMN),
		'table_name'    => $table_name,
		'title_field'   => $title_field,
		'content_field' => $content_field
	))->fetchAll(PDO::FETCH_ASSOC);
	
	// Check Table
	if(empty($tableCheck) || count($tableCheck) != 2) {
		error_log(sprintf("Error Resolving Table Name: \"%s\" on line %s in file %s", $table_name, __LINE__, __FILE__));
		http_response_code(500);
		exit;
	}
	
	// Set Item
	$item = Database::Action("SELECT `$title_field` AS `title`, `$content_field` AS `content` FROM `$table_name` WHERE `id` = :table_id", array(
		'table_id' => $table_id
	))->fetch(PDO::FETCH_ASSOC);
	
	// Check Item
	if(empty($item)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
?>

<div id="titlebar-trim-article-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<?php echo filter_input(INPUT_POST, 'icon'); ?>
				<h3 class="modal-title"><?php echo $item['title']; ?></h3>
			</div>
			
			<div class="modal-body">
				<?php if(filter_input(INPUT_POST, 'html', FILTER_VALIDATE_BOOLEAN)): ?>
					<?php echo $item->getContent(); ?>
				<?php else: ?>
					<p><?php echo nl2br($item->getContent()); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>