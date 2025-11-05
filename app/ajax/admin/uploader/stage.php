<div class="col-lg-6 col-xl-4">
	<div class="image-uploader__uploaded" style="z-index: 9999;">
		<?php if(filter_input(INPUT_POST, 'title')): ?>
			<h3 class="text-center"><?php echo filter_input(INPUT_POST, 'title'); ?></h3>
		<?php endif; ?>
		
		<img class="img-fluid" src="<?php echo sprintf("%s?v=%d", filter_input(INPUT_POST, 'source'), time()); ?>">
	</div>
</div>