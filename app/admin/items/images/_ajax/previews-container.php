<div id="bulk-file-uploader-previews-container-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<i class="fas fa-image" aria-hidden="true"></i>
				<h3 class="modal-title">Upload Image(s)</h3>
			</div>
			
			<div class="modal-body">
				<div id="bulk-file-uploader" class="dropzone-qd mx-auto mb-3 d-flex align-items-center justify-content-center embed-responsive embed-responsive-16by9">
					<div class="dz-message d-flex flex-column">
						<i class="fas fa-cloud-upload-alt text-muted"></i>
						Select Image(s)
					</div>
				</div>
				
				<div id="bulk-file-uploader-previews-container" class="files"></div>
			</div>
			
			<div class="modal-footer border-top justify-content-center pt-3 pb-4" style="display:none;">
				<p class="text-center">Total Uploaded:</p>
				<div class="progress w-100 h-auto">
					<div class="progress-bar progress-bar__primary py-3" style="font-weight: bold;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar">0%</div>
				</div>
			</div>
		</div>
	</div>
</div>

