<?php
	$item = !empty($options['item'])
		? $options['item']
		: FALSE;
?>

<?php if(!empty($item['gallery_images'])): ?>
	<hr class="clear mb-5">
	<div class="row lightbox gallery">
		<?php foreach($item['gallery_images'] as $image): ?>
			<div class="col-6 col-md-4 col-lg-3">
				<a href="<?php echo $image['image']; ?>" class="border">
					<img src="/images/layout/default-landscape.jpg" data-src="<?php echo $image['thumb']; ?>" class="lazy" alt="<?php echo $image['filename_alt']; ?>">
				</a>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>