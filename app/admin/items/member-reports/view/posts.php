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
	$page_title = 'View Member Reports: Posts';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>
		
		<span class="d-block mb-3">
			<strong class="text-muted">Legend:</strong>
			<span class="badge badge-success">ALLOWED</span>
			<span class="badge badge-info">PENDING</span>
		</span>
		
		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered nowrap" style="width:100%;">
					<thead>
						<tr>
							<th>Status</th>
							<th>Type</th>
							<th>Post ID</th>
							<th>Posted By</th>
							<th>Reported By</th>
							<th>Timestamp</th>
							<th>Options</th>
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
			order: [],
			pageLength: 250,
			paging: true,
			searching: true,
			stateSave: true,
			info: true,
			ordering: true,
			columns: [
				{ data: 'status', className: 'all' },
				{ data: 'type', className: 'all' },
				{ data: 'post_id', className: 'all' },
				{ data: 'posted_by', className: 'all' },
				{ data: 'reported_by', className: 'all' },
				{ data: { _: 'item.timestamp', display: 'timestamp' }, className: 'all' },
				{ data: 'options', className: 'all no-drag' }
			],
			rowCallback: function(row) {
				// Variable Defaults
				var tableRow     = $(row);
				var currentTable = $(this);
				var dataTable    = currentTable.DataTable();
				
				// Append ID
				tableRow.prop('id', dataTable.row(row).data().id);
				
				// Render Row States
				switch(dataTable.row(row).data().status) {
					case 'ALLOWED':
						tableRow.addClass('table-success');
						break;
					
					case 'PENDING':
						tableRow.addClass('table-info');
						break;
				}
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

