<?php
	/**
	 * @var string     $qd_button
	 * @var string     $qd_date_format
	 * @var array      $qd_details
	 * @var bool       $qd_hide_image
	 * @var bool       $qd_info
	 * @var bool       $qd_info_desktop_only
	 * @var PageType[] $qd_items
	 */
	
	// Imports
	use Items\Abstracts\PageType;
?>

<?php if(!empty($qd_items)): ?>
	<div class="image-card-article">
		<div class="row align-items-stretch">
			<?php foreach($qd_items as $qd_item): ?>
				<div class="col-md-6 col-xl-4">
					<article class="trim" aria-label="<?php echo $qd_item->getAlt(); ?>">
						<?php if(!isset($qd_hide_image) || !$qd_hide_image): ?>
							<a href="<?php echo $qd_item->getLink(); ?>" title="<?php echo $qd_item->getAlt(); ?>">
								<img class="article-img lazy" src="<?php echo $qd_item->getLandscapeImage(); ?>" data-src="<?php echo $qd_item->getLandscapeImage(); ?>" alt="<?php echo $qd_item->getAlt(); ?>">
							</a>
						<?php endif; ?>
						
						<?php if(!empty($qd_details)): ?>
							<div class="article-details">
								<?php foreach($qd_details as $key => $value): ?>
									<?php if(str_starts_with($value, 'date_') && !empty($qd_date_format)): ?>
										<p><b><?php echo $key; ?></b><?php echo date_create($qd_item->getValue($value))->format($qd_date_format); ?></p>
									<?php else: ?>
										<p><b><?php echo $key; ?></b><?php echo $qd_item->getValue($value); ?></p>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						
						<div class="article-content">
							<h2 class="article-title">
								<a href="<?php echo $qd_item->getLink(); ?>">
									<?php echo $qd_item->getHeading(); ?>
								</a>
							</h2>
							
							<?php if($qd_info): ?>
								<p><?php echo $qd_item->getContentPreview(200); ?></p>
								
								<?php if(empty($qd_button)): ?>
									<a href="<?php echo $qd_item->getLink(); ?>" title="<?php echo $qd_item->getAlt(); ?>"><span class="nobr">Read More</span></a>
								<?php endif; ?>
							<?php endif; ?>
							
							<?php if(!empty($qd_button)): ?>
								<a href="<?php echo $qd_item->getLink(); ?>" class="btn btn-block btn-outline" title="<?php echo $qd_item->getAlt(); ?>">
									<?php echo $qd_button; ?>
								</a>
							<?php endif; ?>
						</div>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php else: ?>
	<p>Sorry, there is nothing to show at this time. Please check back soon!</p>
<?php endif; ?>