<?php
	/*
		Copyright (c) 2021, 2022 FenclWebDesign.com
		This script may not be copied, reproduced or altered in whole or in part.
		We check the Internet regularly for illegal copies of our scripts.
		Do not edit or copy this script for someone else, because you will be held responsible as well.
		This copyright shall be enforced to the full extent permitted by law.
		Licenses to use this script on a single website may be purchased from FenclWebDesign.com
		@Author: Deryk
		*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	// Set Title
	$page_title = 'Manage Transactions';
	
	// Start Header
	include('includes/header.php');
?>
	<style>
		.bg-tip {
			background: #00ff80;
		}
		
		.table-tips {
			background: #00ff80;
		}
	</style>
	
	<main class="page-content">
		<section id="view-table" role="region">
			<div id="page-title-btn">
				<h1><?php echo $page_title; ?></h1>
			</div>
			
			<span class="d-block mb-3">
				<strong class="text-muted">Legend:</strong>
				<button data-action="approved" class="badge badge-ev-light">Approved</button>
				<button data-action="captured" class="badge bg-success text-light">Captured</button>
				<button data-action="pending-captured" class="badge bg-info text-light">Pending Capture</button>
				<button data-action="errored-declined" class="badge bg-danger text-light">Errored/Declined</button>
				<button data-action="refunded" class="badge bg-warning text-dark">Refunded</button>
				<button data-action="voided" class="badge bg-secondary text-light">Voided</button>
				<button data-action="tips" class="badge bg-tip text-light">Tips</button>
				<br>
				<br>
				<div class="row">
					<div class="col-12 table-responsive">
						<table id="data-table" class="table table-bordered" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
							<thead>
								<tr>
									<th class="text-nowrap">ID</th>
									<th class="text-nowrap">Form</th>
									<th class="text-nowrap">Type</th>
									<th class="text-nowrap">Payment Status</th>
									<th class="text-nowrap">Amount</th>
									<th class="text-nowrap">First Name</th>
									<th class="text-nowrap">Last Name</th>
									<th class="text-nowrap">Phone Number</th>
									<th class="text-nowrap">Email</th>
									<th class="text-nowrap">Reservation?</th>
									<th class="text-nowrap">Browser</th>
									<th class="text-nowrap">IP Address</th>
									<th class="text-nowrap">Timestamp</th>
									<th class="text-nowrap">Options</th>
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

			// Init Data Tables
			tableElement.DataTable({
				order: [
					[0, 'desc']
				],
				serverSide: true,

				ajax: {
					url: window.location.href,
					'data': function(d) {
						// Include the selected option in the data sent to the server
						if(d.order && d.order.length > 0) {
							d.selectedOption = ''; // Reset when sorting
						} else if(d.search && d.search.value) {
							// Reset the selectedOption if there's a search term
							d.selectedOption = '';
						} else {
							// Otherwise, use the selected filter option
							d.selectedOption = $('#table-filter select').val();
						}
					}
				},
				pageLength: 250,
				paging: true,
				searching: true,
				stateSave: true,
				info: true,
				ordering: true,
				rowReorder: false,
				columns: [{
					data: 'id',
					className: 'all'
				},
					{
						data: 'form',
						className: 'd-none'
					},
					{
						data: 'type',
						className: 'all'
					},
					{
						data: 'payment_status',
						className: 'all'
					},
					{
						data: 'amount',
						className: 'all'
					},
					{
						data: 'billing_first_name',
						className: 'all'
					},
					{
						data: 'billing_last_name',
						className: 'all'
					},
					{
						data: 'billing_phone',
						className: 'all',
						orderable: false
					},
					{
						data: 'email',
						className: 'all'
					},
					{
						data: 'reservation',
						className: 'all',
						orderable: false
					},
					{
						data: {
							_: 'user_agent.value',
							display: 'user_agent.label'
						},
						className: 'all text-nowrap',
						orderable: false
					},
					{
						data: {
							_: 'ip_address.value',
							display: 'ip_address.label'
						},
						className: 'all text-nowrap',
						orderable: true
					},
					{
						data: {
							_: 'timestamp.value',
							display: 'timestamp.label'
						},
						className: 'all text-nowrap',
						orderable: true
					},
					{
						data: 'options',
						className: 'all no-drag no-highlight',
						orderable: false,
						searchable: false
					}
				],
				rowCallback: function(row) {
					// Variable Defaults
					var tableRow     = $(row);
					var currentTable = $(this);
					var dataTable    = currentTable.DataTable();

					// Append ID
					tableRow.prop('id', dataTable.row(row).data().id);

					// Render Row States
					if(dataTable.row(row).data().is_voided) tableRow.find('td').not('.no-highlight').addClass('table-secondary');
					else if(dataTable.row(row).data().is_refunded) tableRow.find('td').not('.no-highlight').addClass('table-warning');
					else if(dataTable.row(row).data().is_error) tableRow.find('td').not('.no-highlight').addClass('table-danger');
					else if(dataTable.row(row).data().is_captured) tableRow.find('td').not('.no-highlight').addClass('table-success');
					else if(dataTable.row(row).data().is_pending) tableRow.find('td').not('.no-highlight').addClass('table-info');
					else if(dataTable.row(row).data().is_tips) tableRow.find('td').not('.no-highlight').addClass('table-tips');

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
								case 'capture':
								case 'refund':
									// Handle Ajax Request
									$.ajax('/user/' + action + '/transactions/' + data.item.table_name + '/' + data.item.table_id + '/' + data.item.id, {
										dataType: 'html',
										method: 'get',
										async: false,
										beforeSend: showLoader,
										complete: hideLoader,
										success: function(html) {
											// Display Modal
											$(html).on('submit', 'form', function(event) {
												// Prevent Default
												event.preventDefault();

												// Handle Ajax
												$.ajax('/user/' + action + '/transactions/' + data.item.table_name + '/' + data.item.table_id + '/' + data.item.id, {
													data: $(this).serializeArray(),
													dataType: 'json',
													method: 'post',
													async: false,
													beforeSend: function() {
														// Show Loader
														showLoader();

														// Hide Modal
														$(event.delegateTarget).modal('hide');
													},
													complete: hideLoader,
													success: function(response) {
														// Switch Status
														switch(response.status) {
															case 'success':
																displayMessage(response.message || 'Success!', 'success', function() {
																	// Console Message
																	console.info(response.message);
																});
																break;
															case 'error':
																displayMessage(response.message || Object.keys(response.errors).map(function(key) {
																	return response.errors[key];
																}).join('<br>'), 'alert');
																break;
															default:
																displayMessage(response.message || 'Something went wrong.', 'alert');
														}

														// Reload
														$(this).one('hide.bs.modal', dataTable.ajax.reload());
													},
													error: function(xhr) {
														displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert', function() {
															// Reload
															$(this).one('hide.bs.modal', dataTable.ajax.reload());
														});
													}
												});
											}).on('shown.bs.modal', function() {
												// Manually Trigger Select Boxes
												$(this).find('select').trigger('change');

												// Mask Fields
												$(this).find('[data-format="number"]').mask('000,000,000,000,000.00', {
													reverse: true
												});
											}).on('hidden.bs.modal', destroyModal).modal();
										}
									});
									break;
								case 'view':
									if(data.item.table_name && data.item.table_id) {
										// Handle Ajax
										$.ajax('/user/view/transactions/' + data.item.table_name + '/' + data.item.table_id + '/' + data.item.id, {
											dataType: 'html',
											method: 'get',
											async: false,
											beforeSend: showLoader,
											complete: hideLoader,
											success: function(modal) {
												// Show Modal
												$(modal).on('hidden.bs.modal', destroyModal).modal();
											}
										});
									} else {
										// Handle Ajax
										$.ajax('/user/view/transactions/' + data.item.id, {
											dataType: 'html',
											method: 'get',
											async: false,
											beforeSend: showLoader,
											complete: hideLoader,
											success: function(modal) {
												// Show Modal
												$(modal).on('hidden.bs.modal', destroyModal).modal();
											}
										});
									}
									break;
								case 'view-profile':
									// Handle Ajax
									$.ajax('/ajax/admin/items/members/view/' + data.item.member_id, {
										dataType: 'json',
										method: 'post',
										async: false,
										beforeSend: showLoader,
										complete: hideLoader,
										success: function(response) {
											// Switch Status
											switch(response.status) {
												case 'success':
													// Show Preview
													$.fancybox.open({
														src: response.html,
														type: 'html',
														smallBtn: false,
														touch: false,
														width: '100%',
														clickOutside: 'close'
													});
													break;
												case 'error':
												default:
													displayMessage(response.message || 'Something went wrong.', 'alert');
											}
										}
									});
									break;
								case 'void':
									// Confirm Action
									if(confirm('Are you sure you want to void this transaction?')) {
										// Handle Ajax
										$.ajax('/user/void/transactions/' + data.item.table_name + '/' + data.item.table_id + '/' + data.item.id, {
											data: $(this).serializeArray(),
											dataType: 'json',
											method: 'post',
											async: false,
											beforeSend: function() {
												// Show Loader
												showLoader();

												// Hide Modal
												$(event.delegateTarget).modal('hide');
											},
											complete: hideLoader,
											success: function(response) {
												// Switch Status
												switch(response.status) {
													case 'success':
														displayMessage(response.message || 'Success!', 'success', function() {
															// Console Message
															console.info(response.message);
														});
														break;
													case 'error':
														displayMessage(response.message || Object.keys(response.errors).map(function(key) {
															return response.errors[key];
														}).join('<br>'), 'alert');
														break;
													default:
														displayMessage(response.message || 'Something went wrong.', 'alert');
												}

												// Reload
												$(this).one('hide.bs.modal', dataTable.ajax.reload());
											},
											error: function(xhr) {
												displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert', function() {
													// Reload
													$(this).one('hide.bs.modal', dataTable.ajax.reload());
												});
											}
										});
									}
									break;
								default:
									console.error('Unknown Action:', action, data);
							}
						}
					});
				}
			});

			$('#table-filter select').change(function() {

				tableElement.DataTable().search('').columns().every(function() {
					this.search('');
				});
				tableElement.DataTable().ajax.reload();
			});

			$('[type="search"]').off('keyup.DT input.DT');

			var searchDelay = null;

			$('[type="search"]').on('keyup', function() {
				var search = $('[type="search"]').val();

				clearTimeout(searchDelay);

				searchDelay = setTimeout(function() {
					if(search != null) {
						tableElement.DataTable().search('').columns().every(function() {
							this.search('');
						});
						tableElement.DataTable().search(search).draw();
					}
				}, 2000);
			});

			$(this.body).on('click', 'button[data-action]', function() {
				var table = $('#data-table').DataTable();
				$('#table-filter select').val('').change();
				// Clear all global and column-specific searches
				table.search('').columns().every(function() {
					this.search('');
				});

				// Draw the table to apply cleared filters
				table.draw();

				switch($(this).data('action')) {

					case 'approved':

						table.column(2).search('Approved').draw();
						break;

					case 'captured':
						table.column(2).search('Captured').draw();
						break;

					case 'pending-captured':
						table.column(2).search('Pending').draw();
						break;

					case 'errored-declined':
						table.column(2).search('Declined').draw();
						break;

					case 'refunded':
						table.column(2).search('Refunded').draw();
						break;

					case 'voided':
						table.column(2).search('Voided').draw();
						break;
						
					case 'tips':
						table.column(1).search('Tips').draw();
						break;
				}

				// You might want to reload the data if the source is dynamic and needs to reflect changes
				table.ajax.reload(null, false);
			});
		});
	</script>

<?php include('includes/body-close.php'); ?>