<div id="image-cropper-modal" class="modal" role="dialog" tabindex="-1">
	<h2 id="image-cropper-modal-title">Cropping Image</h2>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<img id="image-cropper-modal-image" src="<?php echo filter_input(INPUT_POST, 'cropper', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)['source']; ?>">
		</div>
	</div>
	<div id="image-cropper-modal-controls" class="text-center">
		<a id="image-cropper-modal-controls-submit" href="#">
			<i class="fal fa-check"></i>
		</a>
		<a id="image-cropper-modal-controls-cancel" href="#">
			<i class="fal fa-times"></i>
		</a>
		<a id="image-cropper-modal-controls-zoom-in" href="#">
			<i class="fal fa-plus"></i>
		</a>
		<a id="image-cropper-modal-controls-zoom-out" href="#">
			<i class="fal fa-minus"></i>
		</a>
		<a id="image-cropper-modal-controls-rotate" href="#">
			<i class="fal fa-undo"></i>
		</a>
	</div>
</div>