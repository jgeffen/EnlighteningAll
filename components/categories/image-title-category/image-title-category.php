<?php
	$items = !empty($options)
		? $options
		: FALSE;
?>

<?php if(!empty($items)): ?>
	<div class="image-title-category">
		<div class="row align-items-stretch">
			<?php foreach($items as $item): ?>
			    <div class="col-lg-6">
					<section class="trim" aria-label="Preview of <?php echo $item['title']; ?>">
						<div class="row no-gutters align-items-stretch h-100">
							<?php if(!empty($item['images'])): ?>
								<div class="col-sm-4 col-md-3 col-lg-4">
									<a href="<?php echo $item->getLink(); ?>" title="<?php echo $item['title']; ?>">
										<div class="category-img-wrap" style="background-image: url(<?php echo $item['images']['landscape_image']['thumb']; ?>)" role="img" aria-label="<?php echo $item->getAlt(); ?>"></div>
									</a>
								</div>
							<?php endif; ?>
							<div class="<?php echo (!empty($item['images'])) ? 'col-sm-8 col-md-9 col-lg-8' : 'col'; ?>">
								<h2 class="category-title"><a href="<?php echo $item->getLink(); ?>"><?php echo $item['title']; ?></a></h2>
							</div>
						</div>
					</section>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php else: ?>
	<hr>
	<p>Sorry, no articles to show at this time. Please check back soon!</p>
<?php endif; ?>