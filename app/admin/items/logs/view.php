<?php
	/*
	Copyright (c) 2021, 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	// Set Title
	$page_title = 'User Logs';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>
		
		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered nowrap w-100" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
					<thead>
						<tr>
							<th>ID</th>
							<th>Action</th>
							<th>Table Name</th>
							<th>Table ID</th>
							<th>Filename</th>
							<th>Notes</th>
							<th>User</th>
							<th>User Agent</th>
							<th>IP Address</th>
							<th>Timestamp</th>
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
		// Variable Defaults
		var tableElement = $('#data-table');
		var tableOptions = tableElement.data('tableOptions');
		
		// Init Data Tables
		tableElement.DataTable({
			order: [[0, 'desc']],
			pageLength: 250,
			paging: true,
			searching: true,
			stateSave: true,
			info: true,
			ordering: true,
			rowReorder: false,
			columns: [
				{ data: { _: 'item.id', display: 'id' }, className: 'all', orderable: true },
				{ data: { _: 'action', display: 'action' }, className: 'all', orderable: true },
				{ data: { _: 'item.table_name', display: 'item.table_name' }, className: 'all', orderable: true },
				{ data: { _: 'item.table_id', display: 'item.table_id' }, className: 'all', orderable: true },
				{ data: { _: 'item.filename', display: 'item.filename' }, className: 'all', orderable: true },
				{ data: { _: 'item.notes', display: 'item.notes' }, className: 'all', orderable: true },
				{ data: { _: 'full_name', display: 'full_name' }, className: 'all', orderable: true },
				{ data: { _: 'user_agent.value', display: 'user_agent.label' }, className: 'all text-nowrap', orderable: true },
				{ data: { _: 'ip_address.value', display: 'ip_address.label' }, className: 'all text-nowrap', orderable: true },
				{ data: { _: 'timestamp.value', display: 'timestamp.label' }, className: 'all text-nowrap', orderable: true }
			],
			responsive: {
				details: {
					display: $.fn.dataTable.Responsive.display.modal({
						header: function(row) {
							var data = row.data();
							
							return 'Details for Log Entry ID #' + data.id;
						}
					}),
					renderer: $.fn.dataTable.Responsive.renderer.tableAll({
						tableClass: 'table'
					})
				}
			},
			rowCallback: function(row) {
				// Variable Defaults
				var tableRow     = $(row);
				var currentTable = $(this);
				var dataTable    = currentTable.DataTable();
				
				// Append ID
				tableRow.prop('id', dataTable.row(row).data().id);
				
				// Bind Click Event to Action
				tableRow.off().on('click', '[data-action]', function(event) {
					// Check Event
					if(event) {
						// Prevent Default
						event.preventDefault();
						
						// Variable Defaults
						var currentRow = event.delegateTarget;
						var action     = $(this).data('action');
						var data       = dataTable.row(currentRow).data();
						
						// Switch Action
						switch(action) {
							default:
								console.error('Unknown Action:', action, data);
						}
					}
				});
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

