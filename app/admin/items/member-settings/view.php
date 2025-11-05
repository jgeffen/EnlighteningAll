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
	$page_title = 'View Member Settings';
	
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
			<span class="badge bg-success text-light">Active</span>
			<span class="badge bg-danger text-light">Inactive</span>
		</span>
		
		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered w-100" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
					<thead>
						<tr>
							<th>ID</th>
							<th>Type</th>
							<th>Setting</th>
							<th>Descriptor</th>
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
				{ data: { _: 'item.type', display: 'type' }, className: 'all', orderable: true },
				{ data: { _: 'item.label', display: 'setting' }, className: 'all', orderable: true },
				{ data: { _: 'item.label_text', display: 'descriptor' }, className: 'all', orderable: false },
				{ data: { _: 'timestamp.value', display: 'timestamp.label' }, className: 'all text-nowrap', orderable: true },
				{ data: 'options', className: 'all no-drag', orderable: false }
			],
			rowCallback: function(row) {
				// Variable Defaults
				var tableRow     = $(row);
				var currentTable = $(this);
				var dataTable    = currentTable.DataTable();
				
				// Append ID
				tableRow.prop('id', dataTable.row(row).data().id);
				
				// Render Row States
				dataTable.row(row).data().enabled === false && tableRow.addClass('table-danger');
				dataTable.row(row).data().enabled === true && tableRow.addClass('table-success');
				
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
							case 'toggle':
								// Handle Ajax
								$.ajax('/user/toggle/member-settings/' + data.id, {
									data: { status: data.enabled ? 1 : 0 },
									dataType: 'json',
									method: 'post',
									async: true,
									beforeSend: showLoader,
									complete: hideLoader,
									success: function(response) {
										// Switch Status
										switch(response.status) {
											case 'success':
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
								break;
							default:
								console.error('unknown action:', action);
						}
					}
				});
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

