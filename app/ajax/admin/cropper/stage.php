<div class="col-lg-6 col-xl-4">
	<a class="image-cropper-crop" href="#" data-cropper-action="crop">
		<h3><?php echo filter_input(INPUT_POST, 'title'); ?></h3>
		
		<img class="img-fluid"
			src="<?php echo sprintf("%s?v=%d", filter_input(INPUT_POST, 'thumb'), time()); ?>"
			data-cropper-aspect="<?php echo filter_input(INPUT_POST, 'aspect'); ?>"
			data-cropper-format="<?php echo htmlentities(json_encode(filter_input(INPUT_POST, 'format', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)), ENT_QUOTES); ?>"
			data-cropper-source="<?php echo filter_input(INPUT_POST, 'source'); ?>"
			data-cropper-type="<?php echo filter_input(INPUT_POST, 'type'); ?>">
	</a>
</div>