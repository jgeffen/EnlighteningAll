<?php
	/**
	 * @var string $qd_background_color
	 * @var string $qd_main_title
	 * @var array  $qd_sections
	 * @var string $qd_text_color
	 */
	
	// Imports
	use Items\Interfaces\PageType;
	
	// Reset Options
	$qd_background_color = !empty($qd_background_color) ? sprintf("component-background-%s", $qd_background_color) : '';
	$qd_main_title       = htmlentities($qd_main_title ?: 'Recent Articles', ENT_QUOTES);
	$qd_text_color       = !empty($qd_text_color) ? sprintf("component-text-%s", $qd_text_color) : '';
	$component_classes   = implode(' ', array('category-list-recent-articles', $qd_background_color, $qd_text_color));
?>

<?php if(!empty($qd_sections)): ?>
	<nav class="<?php echo $component_classes; ?>" aria-label="<?php echo $qd_main_title; ?>">
		<div class="container-fluid title-wrap">
			<div class="container">
				<h2 class="title-super-sm"><?php echo $qd_main_title; ?></h2>
			</div>
		</div>
		
		<div class="container-fluid">
			<div class="container">
				<div class="row">
					<?php foreach($qd_sections as $section): ?>
						<div class="col-md-6 col-lg-4 mb-0 mb-md-4">
							<section aria-label="<?php echo $section['alt']; ?>">
								<h3 class="title-underlined equal-title"><?php echo $section['title']; ?></h3>
								<ul>
									<?php /** @var PageType $item */ ?>
									<?php foreach($section['items'] as $item): ?>
										<li>
											<a href="<?php echo $item->getLink(); ?>" title="<?php echo $item->getAlt(); ?>">
												<?php echo $item->getHeading(); ?>
											</a>
										</li>
									<?php endforeach; ?>
									
									<li><a href="<?php echo $section['link']; ?>" title="<?php echo $section['alt']; ?>">View All</a></li>
								</ul>
							</section>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</nav>
<?php endif; ?>