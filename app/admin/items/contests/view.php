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
	
	// Variable Defaults
	$page_title = 'View Contest';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
			
			<a class="btn btn-primary" href="/user/add/contests">
				<i class="fa fa-plus"></i>
				<span class="d-none d-sm-inline">Add Contest</span>
			</a>
		</div>
		
		<span class="d-block mb-3">
			<strong class="text-muted">Legend:</strong>
			<span class="badge badge-warning border rounded">Unpublished</span>
			<span class="badge badge-info border rounded">Upcoming</span>
			<span class="badge badge-success border rounded">Current</span>
			<span class="badge badge-light border rounded">Past</span>
			<span class="badge badge-danger border rounded">Awaiting</span>
		</span>
		
		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered nowrap" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th>Content</th>
							<th>Entries</th>
							<th>Winners</th>
							<th>Start Date</th>
							<th>End Date</th>
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
			rowReorder: false,
			columns: [
				{ data: { _: 'item.id', display: 'id' }, className: 'all', orderable: true },
				{ data: { _: 'item.page_title', display: 'page_title' }, className: 'all', orderable: true },
				{ data: { _: 'item.content', display: 'content' }, className: 'all', orderable: true },
				{ data: { _: 'entries', display: 'entries' }, className: 'all', orderable: true },
				{ data: { _: 'item.number_of_winners', display: 'winners' }, className: 'all', orderable: true },
				{ data: { _: 'item.date_start', display: 'item.date_start' }, className: 'all', orderable: true },
				{ data: { _: 'item.date_end', display: 'item.date_end' }, className: 'all', orderable: true },
				{ data: { _: 'timestamp.value', display: 'timestamp.label' }, className: 'all text-nowrap', orderable: true },
				{ data: 'options', className: 'all no-drag', orderable: false }
			],
			responsive: {
				details: {
					display: $.fn.dataTable.Responsive.display.modal({
						header: function(row) {
							var data = row.data();
							
							return 'Details for Contest ID #' + data.id;
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
				
				// Render Row States
				switch(true) {
					case !dataTable.row(row).data().published && tableRow.addClass('table-warning'):
					case dataTable.row(row).data().status.awaiting && tableRow.addClass('table-danger'):
					case dataTable.row(row).data().status.upcoming && tableRow.addClass('table-info'):
					case dataTable.row(row).data().status.current && tableRow.addClass('table-success'):
					case dataTable.row(row).data().status.past && tableRow.addClass('table-light'):
						break;
				}
				
				// Disable Unused Buttons
				!dataTable.row(row).data().entries && tableRow.find('a[href^="/user/entries/contests"]').addClass('disabled');
				dataTable.row(row).data().entries && tableRow.find('a[data-action="delete"]').addClass('disabled');
				
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
						console.log(data);
						
						// Switch Action
						switch(action) {
							case 'delete':
								// Confirm Deletion
								if(confirm('Are you sure you want to delete this?')) {
									// Handle Ajax
									$.ajax('/user/delete/contests/' + data.item.id, {
										dataType: 'json',
										method: 'delete',
										async: true,
										beforeSend: showLoader,
										complete: hideLoader,
										success: function(response) {
											// Switch Status
											switch(response.status) {
												case 'success':
													// Console Message
													console.info(response.message);
													
													// Reload
													dataTable.ajax.reload();
													break;
												case 'error':
													displayMessage(response.message || Object.keys(response.errors).map(function(key) {
														return response.errors[key];
													}).join('<br>'), 'alert', null);
													break;
												default:
													displayMessage(response.message || 'Something went wrong.', 'alert');
											}
										}
									});
								}
								break;
							default:
								console.error('Unknown Action:', action);
						}
					}
				});
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

