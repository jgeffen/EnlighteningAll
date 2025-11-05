<?php
	$items = !empty($options['items'])
		? $options['items']
		: FALSE;
?>

<?php if(!empty($items)): ?>
	<div class="circle-articles">
		<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
			<?php foreach($items as $item): ?>
			    <div class="col">
				    <article aria-label="Preview of <?php echo $item->getHeading(); ?>">
						<a href="<?php echo $item->getLink(); ?>" title="Read more on <?php echo $item->getHeading();?>" class="circle-articles__img" style="background-image: url(<?php echo !empty($item['square']) ? $item['square'] : '/images/layout/default-square.jpg'; ?>)"></a>
						<h2 class="circle-articles__title"><a href="<?php echo $item->getLink(); ?>"><?php echo $item->getHeading(); ?></a></h2>
						<?php if(!empty($options['sub_title'])): ?>
							<h3 class="circle-articles__sub-title"><?php echo $item[$options['sub_title']]; ?></h3>
						<?php endif; ?>
				    </article>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php else: ?>
	<p>Sorry, there is nothing to show at this time. Please check back soon!</p>
<?php endif; ?>