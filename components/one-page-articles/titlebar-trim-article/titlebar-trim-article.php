<?php
	/**
	 * @var array  $qd_items
	 * @var string $qd_table_name
	 * @var string $qd_title
	 * @var string $qd_icon
	 * @var string $qd_text
	 * @var int    $qd_cols
	 * @var bool   $qd_html
	 * @var string $qd_btn_link
	 * @var string $qd_btn_text
	 * @var bool   $qd_modal
	 * @var int    $qd_truncate
	 * @var string $qd_message
	 */
	
	// Reset Items
	$qd_items      ??= array();
	$qd_table_name ??= '';
	$qd_title      ??= 'title';
	$qd_icon       ??= '<i class="fal fa-question-circle"></i>';
	$qd_text       ??= 'text';
	$qd_cols       ??= 1;
	$qd_html       ??= FALSE;
	$qd_btn_link   ??= '';
	$qd_btn_text   ??= 'Read More';
	$qd_modal      ??= FALSE;
	$qd_truncate   ??= 0;
	$qd_message    ??= '';
	
	// Component Data
	$component = array(
		'table_name'    => $qd_table_name,
		'table_id'      => NULL,
		'title_field'   => $qd_title,
		'content_field' => $qd_text,
		'icon'          => $qd_icon,
		'html'          => $qd_html
	);
	
	//Switch Statement for Column Selection
	$qd_cols = match ($qd_cols) {
		1       => 'col-12',
		2       => 'col-md-6',
		3       => 'col-md-6 col-xl-4',
		4       => 'col-md-6 col-lg-4 col-xl-3',
		default => 'col-12',
	};
?>

<?php if(!empty($qd_items)): ?>
	<div class="titlebar-trim-article row clear">
		<?php foreach($qd_items as $item): ?>
			<div class="<?php echo $qd_cols; ?>" data-component="<?php echo htmlentities(json_encode(array_merge($component, array('table_id' => $item['id']))), ENT_QUOTES); ?>">
				<article class="title-bar-trim-combo" aria-label="<?php echo $item[$qd_title]; ?>">
					<div class="title-bar equal-title">
						<?php echo $qd_icon; ?>
						<h2><?php echo $item[$qd_title]; ?></h2>
					</div>
					<div class="trim">
						<div class="<?php echo $qd_cols !== 'col-12' ? 'article-content' : ''; ?>">
							<?php if(!empty($item[$qd_text])): ?>
								<?php if(!empty($qd_html)): ?>
									<?php echo !empty($qd_truncate) ? '<p>' . shortdesc($item[$qd_text], $qd_truncate) . '</p>' : $item[$qd_text]; ?>
								<?php else: ?>
									<p><?php echo nl2br(!empty($qd_truncate) ? shortdesc($item[$qd_text], $qd_truncate) : $item[$qd_text]); ?></p>
								<?php endif; ?>
							<?php endif; ?>
							<?php if(!empty($qd_btn_text)): ?>
								<?php if(!empty($qd_modal)): ?>
									<a href="#" class="btn btn-outline <?php echo $qd_cols !== 'col-12' ? '' : 'mt-2 mb-3'; ?>" data-component-action="modal">
										<?php echo $qd_btn_text; ?>
									</a>
								<?php elseif(!empty($options['pdf'])): ?>
									<a href="/js/pdf.js/web/viewer.html?file=<?php echo $item['link']; ?>" class="btn btn-outline <?php echo $qd_cols !== 'col-12' ? '' : 'mt-2 mb-3'; ?>" data-fancybox data-type="pdf">
										<?php echo $item['link_text'] ?? $qd_btn_text; ?>
									</a>
								<?php elseif(!empty($qd_btn_link)): ?>
									<a href="<?php echo $qd_btn_link; ?>" class="btn btn-outline <?php echo $qd_cols !== 'col-12' ? '' : 'mt-2 mb-3'; ?>">
										<?php echo $qd_btn_text; ?>
									</a>
								<?php elseif(!empty($item['link'])): ?>
									<a href="<?php echo $item['link']; ?>" class="btn btn-outline <?php echo $qd_cols !== 'col-12' ? '' : 'mt-2 mb-3'; ?>" target="<?php echo $qd_target ?? '_self'; ?>">
										<?php echo $item['link_text'] ?? $qd_btn_text; ?>
									</a>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
				</article>
			</div>
		<?php endforeach; ?>
	</div>
<?php elseif(!empty($qd_message)): ?>
	<p><?php echo $qd_message; ?></p>
<?php endif; ?>
