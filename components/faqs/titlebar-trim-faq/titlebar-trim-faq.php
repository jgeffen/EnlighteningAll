<?php
	/**
	 * @var Faq[]  $qd_items
	 * @var string $qd_icon
	 * @var int    $qd_cols
	 * @var string $qd_btn_text
	 * @var string $qd_message
	 * @var bool   $qd_modal
	 * @var bool   $qd_nl2br
	 * @var int    $qd_truncate
	 * @var string $qd_endpoint
	 */
	
	// Imports
	use Items\Faq;
	
	// Component Base
	$component_base = array(
		'table_id' => NULL,
		'icon'     => $qd_icon,
		'nl2br'    => $qd_nl2br
	);
	
	// Match Columns
	$cols = match ($qd_cols) {
		1       => 'col-12',
		2       => 'col-md-6',
		3       => 'col-md-6 col-xl-4',
		4       => 'col-md-6 col-lg-4 col-xl-3',
		default => 'col-12',
	};
?>

<?php if(!empty($qd_items)): ?>
	<div class="titlebar-trim-faq row clear" data-endpoint="<?php echo $qd_endpoint; ?>">
		<?php foreach($qd_items as $qd_item): ?>
			<?php $payload = htmlspecialchars(json_encode(array_merge($component_base, array('id' => $qd_item->getId()))), ENT_COMPAT); ?>
			<div class="<?php echo $cols; ?>" data-component="<?php echo $payload; ?>">
				<article class="title-bar-trim-combo" aria-label="<?php echo $qd_item->getAlt(); ?>">
					<div class="title-bar equal-title">
						<?php echo $qd_icon; ?>
						<h2><?php echo $qd_item->getQuestion(); ?></h2>
					</div>
					<div class="trim">
						<div class="<?php echo $cols !== 'col-12' ? 'article-content' : ''; ?>">
							<?php echo $qd_truncate ? Helpers::Truncate($qd_item->getAnswer($qd_nl2br), $qd_truncate) : $qd_item->getAnswer($qd_nl2br); ?>
							
							<?php if(!empty($qd_btn_text)): ?>
								<?php if($qd_modal): ?>
									<a href="#" class="btn btn-outline <?php echo $cols !== 'col-12' ? '' : 'mt-2 mb-3'; ?>" data-component-action="modal">
										<?php echo $qd_btn_text; ?>
									</a>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
				</article>
			</div>
		<?php endforeach; ?>
	</div>
<?php else: ?>
	<p><?php echo $qd_message ?? 'Sorry, nothing to show at this time. Please check back soon!'; ?></p>
<?php endif; ?>
