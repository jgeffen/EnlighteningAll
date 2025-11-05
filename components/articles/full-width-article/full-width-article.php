<?php
	$items = !empty($options['items'])
		? $options['items']
		: FALSE;
?>

<?php if(!empty($items)): ?>
	<?php foreach($items as $item): ?>
	    <article class="full-width-article trim full-width-article__style-<?php echo !empty($options['style']) ? $options['style'] : ''; ?>" aria-label="Preview of <?php echo $item->getHeading(); ?>">
		    <div class="row align-items-center justify-content-between">
			    <div class="col-lg-auto">
					<?php if(!empty($item['thumb'])): ?>
						<a href="<?php echo $item->getLink(); ?>" title="Read more on <?php echo $item->getHeading();?>">
							<img src="/images/layout/default-landscape.jpg" data-src="<?php echo $item['thumb']; ?>" class="full-width-article__image border lazy" alt="<?php echo $item->getAlt(); ?>">
						</a>
					<?php endif; ?>
			    </div>
			    <div class="col <?php echo empty($item['thumb']) ? 'col-xl-10' : 'col-xl-6'; ?>">
					<div class="full-width-article__content text-center">
						<h2 class="title-super-sm"><a href="<?php echo $item->getLink(); ?>"><?php echo $item->getHeading(); ?></a></h2>
						<?php if(!empty($options['details'])): ?>
							<?php foreach($options['details'] as $key => $value): ?>
								<p><b><?php echo $key; ?></b><?php echo $item[$value]; ?></p>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if(!empty($item['text'])): ?>
							<p>
								<?php echo empty($item['thumb']) ? $item['text'] : shortdesc($item['text'], 250); ?>
								<a href="<?php echo $item->getLink(); ?>" title="Read more on <?php echo $item->getHeading(); ?>"><span class="nobr">Read More</span></a>
							</p>
						<?php endif; ?>
					</div>
			    </div>
			    <div class="col-auto d-none d-lg-block">
				    <a href="<?php echo $item->getLink(); ?>" class="full-width-article__btn" title="Read More on <?php echo $item->getHeading(); ?>"><i class="fa-light fa-chevron-right"></i></a>
			    </div>
			</div>
	    </article>
	    <?php if(!empty($options['style']) && $options['style'] == 'minimal'): ?>
	    	<hr class="my-5">
	    <?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<p>Sorry, there is nothing to show at this time. Please check back soon!</p>
<?php endif; ?>