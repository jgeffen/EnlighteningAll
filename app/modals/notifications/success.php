<div class="modal fade" id="success-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa-light fa-check-circle" aria-hidden="true"></i>
				<h3 class="modal-title">Success</h3>
			</div>
			<div class="modal-body"><?php echo filter_input(INPUT_POST, 'text'); ?></p></div>
		</div>
	</div>
</div>