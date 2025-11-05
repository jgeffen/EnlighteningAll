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
	$page_title = 'View Groups Posts';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
			
			<a class="btn btn-primary" href="/user/add/groups">
				<i class="fa fa-plus"></i>
				<span class="d-none d-sm-inline">Add Post</span>
			</a>
		</div>
		
		<span class="d-block mb-3">
			<strong class="text-muted">Legend:</strong>
			<span class="badge bg-warning text-dark">Unpublished</span>
		</span>
		
		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered nowrap" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th>Content</th>
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
			ordering: false,
			rowReorder: { selector: 'td:not(.no-drag)', dataSrc: 'item.position', update: false },
			columns: [
				{ data: 'id', className: 'all' },
				{ data: { _: 'item.page_title', display: 'page_title' }, className: 'all' },
				{ data: { _: 'item.content', display: 'content' }, className: 'all' },
				{ data: 'timestamp', className: 'all' },
				{ data: 'options', className: 'all no-drag' }
			],
			responsive: {
				details: {
					display: $.fn.dataTable.Responsive.display.modal({
						header: function(row) {
							var data = row.data();
							
							return 'Details for Blog Post ID #' + data.id;
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
				!dataTable.row(row).data().published && tableRow.addClass('table-warning');
				
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
									$.ajax('/user/delete/groups/' + data.item.id, {
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

