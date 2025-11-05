<?php
	/**
	 * @var string   $qd_button
	 * @var array    $qd_details
	 * @var bool     $qd_info
	 * @var bool     $qd_info_desktop_only
	 * @var PageType $qd_item
	 */
	
	// Imports
	use Items\Abstracts\PageType;
?>

<?php if(!is_null($qd_item)): ?>
	<div class="image-card-featured">
		<?php if($qd_item->hasImage()): ?>
			<article class="trim" aria-label="<?php echo $qd_item->getAlt(); ?>">
				<div class="row no-gutters align-items-stretch">
					<div class="col-md-6">
						<a href="<?php echo $qd_item->getLink(); ?>" title="<?php echo $qd_item->getAlt(); ?>">
							<div class="featured-img-wrap" style="background-image: url('<?php echo $qd_item->getLandscapeImage(); ?>')" role="img" aria-label="<?php echo $qd_item->getAlt(); ?>"></div>
						</a>
					</div>
					
					<div class="col-md-6">
						<?php if(!empty($qd_details)): ?>
							<div class="article-details">
								<?php foreach($qd_details as $key => $value): ?>
									<p><b><?php echo $key; ?></b><?php echo $qd_item->getValue($value); ?></p>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						
						<div class="article-content">
							<h2 class="article-title"><a href="<?php echo $qd_item->getLink(); ?>"><?php echo $qd_item->getHeading(); ?></a></h2>
							
							<?php if($qd_info): ?>
								<div class="<?php echo $qd_info_desktop_only ? 'd-none d-md-block' : ''; ?>">
									<p><?php echo $qd_item->getContentPreview(); ?></p>
									
									<?php if(empty($qd_button)): ?>
										<a href="<?php echo $qd_item->getLink(); ?>" title="<?php echo $qd_item->getAlt(); ?>"><span class="nobr">Read More</span></a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							
							<?php if(!empty($qd_button)): ?>
								<a href="<?php echo $qd_item->getLink(); ?>" class="btn btn-block-sm btn-outline mt-4" title="<?php echo $qd_item->getAlt(); ?>">
									<?php echo $qd_button; ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</article>
		<?php else: ?>
			<article class="trim no-article-img" aria-label="<?php echo $qd_item->getAlt(); ?>">
				<div class="row no-gutters align-items-stretch">
					<div class="col-12">
						<?php if(!empty($qd_details)): ?>
							<div class="article-details">
								<?php foreach($qd_details as $key => $value): ?>
									<p><b><?php echo $key; ?></b><?php echo $qd_item->getValue($value); ?></p>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						
						<div class="article-content">
							<h2 class="article-title"><a href="<?php echo $qd_item->getLink(); ?>"><?php echo $qd_item->getHeading(); ?></a></h2>
							
							<?php if($qd_info): ?>
								<div class="<?php echo $qd_info_desktop_only ? 'd-none d-md-block' : ''; ?>">
									<p><?php echo $qd_item->getContentPreview(); ?></p>
									
									<?php if(empty($qd_button)): ?>
										<a href="<?php echo $qd_item->getLink(); ?>" title="<?php echo $qd_item->getAlt(); ?>"><span class="nobr">Read More</span></a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							
							<?php if(!empty($qd_button)): ?>
								<a href="<?php echo $qd_item->getLink(); ?>" class="btn btn-block-sm btn-outline mt-4" title="<?php echo $qd_item->getAlt(); ?>">
									<?php echo $qd_button; ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</article>
		<?php endif; ?>
	</div>
<?php endif; ?>