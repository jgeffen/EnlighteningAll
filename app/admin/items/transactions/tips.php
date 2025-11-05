<?php
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	// Set Title
	$page_title = 'Manage Tips';
	include('includes/header.php');
?>
<style>
	.table-tips {
		background: #00ff80 !important;
	}
</style>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>
		
		<div class="row">
			<div class="col-12 table-responsive">
				<table id="data-table" class="table table-bordered" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
					<thead>
						<tr>
							<th class="text-nowrap">ID</th>
							<th class="text-nowrap">Type</th>
							<th class="text-nowrap">Payment Status</th>
							<th class="text-nowrap">Amount</th>
							<th class="text-nowrap">First Name</th>
							<th class="text-nowrap">Last Name</th>
							<th class="text-nowrap">Phone</th>
							<th class="text-nowrap">Email</th>
							<th class="text-nowrap">Paid Out</th>
							<th class="text-nowrap">IP Address</th>
							<th class="text-nowrap">Timestamp</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		let tableElement = $('#data-table');

		// Init DataTables
		tableElement.DataTable({
			order: [[0, 'desc']],
			serverSide: true,
			ajax: {
				url: window.location.href
			},
			pageLength: 250,
			columns: [
				{ data: 'id', className: 'all' },
				{ data: 'type', className: 'all' },
				{ data: 'payment_status', className: 'all' },
				{ data: 'amount', className: 'all' },
				{ data: 'billing_first_name', className: 'all' },
				{ data: 'billing_last_name', className: 'all' },
				{ data: 'billing_phone', className: 'all', orderable: false },
				{ data: 'email', className: 'all' },
				{ data: 'paid_out', className: 'all', orderable: false, searchable: false },
				{
					data: {
						_: 'ip_address.value',
						display: 'ip_address.label'
					},
					className: 'all text-nowrap'
				},
				{
					data: {
						_: 'timestamp.value',
						display: 'timestamp.label'
					},
					className: 'all text-nowrap'
				}
			],
			rowCallback: function(row, data) {
				let $row = $(row);
				$row.prop('id', data.id);

				// Always reset state
				$row.removeClass('table-tips');

				// Highlight row green if NOT paid_out
				if (!data.paid_out.includes('checked')) {
					$row.addClass('table-tips');
				}
			}
		});

		// Handle Paid Out toggle
		$(document).on('change', '.toggle-paid-out', function() {
			let id       = $(this).data('id');
			let paid_out = $(this).is(':checked') ? 1 : 0;
			let table    = $('#data-table').DataTable();

			$.post('/ajax/admin/items/transactions/toggle-paid-out', { id, paid_out }, function(response) {
				if (response.status === 'success') {
					// Refresh just this row
					let row = table.row('#' + id);
					row.invalidate().draw(false);
				} else {
					alert('Failed to update tip status');
				}
			}, 'json');
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
