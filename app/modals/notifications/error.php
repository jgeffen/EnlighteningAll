<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa-light fa-bug" aria-hidden="true"></i>
				<h3 class="modal-title">Error</h3>
			</div>
			<div class="modal-body"><p><?php echo filter_input(INPUT_POST, 'text'); ?></p></div>
		</div>
	</div>
</div>