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
$page_title = 'View Travel Affiliate Members';

// Start Header
include('includes/header.php');
?>

<style>
	#account-approval-modal textarea {
		border-radius: unset;
		resize: none;
		height: 600px;
		border: 2px solid #dc3545;
		box-shadow: unset;
		background: white;
		color: white;
	}

	#account-approval-modal textarea::placeholder {
		text-align: center;
		line-height: 600px;
		font-size: 25px;
		text-transform: uppercase;
	}

	#account-approval-modal textarea:focus {
		outline: none !important;
		border: 2px solid #28a745;
		box-shadow: unset;
	}
</style>

<main class="page-content test">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>

			<a class="btn btn-primary" href="#" data-action="account-approval">
				<i class="fas fa-bolt"></i>
				<span class="d-none d-sm-inline">Account Approval</span>
			</a>
		</div>

		<span class="d-block mb-3">
			<strong class="text-muted">Legend:</strong>
			<button class="badge badge-ev-light"><i class="fa-solid fa-badge-check"></i> Email Verified</button>
			<button class="badge badge-light">Approved</button>
			<button class="badge badge-danger">Banned</button>

		</span>

		<div class="row">
			<div class="col-12 table-responsive">
				<table id="data-table" class="table table-bordered" style="width:100%;">
					<thead>
						<tr>
							<th class="text-nowrap">Member ID</th>
							<th class="text-nowrap">Username</th>
							<th class="text-nowrap">Name</th>
							<th class="text-nowrap">Email</th>
							<th class="text-nowrap">Browser</th>
							<th class="text-nowrap">Device</th>
							<th class="text-nowrap">Platform</th>
							<th class="text-nowrap">Joined</th>
							<th class="text-nowrap">IP Address</th>
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
		var tableOptions = tableElement.data('tableOptions');

		// Init Data Tables
		tableElement.DataTable({
			order: [
				[2, 'asc']
			],
			//serverSide: true,
			//ajax: window.location.href,

			pageLength: 250,
			//iDeferLoading: 250,
			deferLoading: true,
			paging: true,
			searching: true,
			stateSave: true,
			info: true,
			ordering: true,
			columns: [{
					data: 'id',
					className: 'all'
				},
				{
					data: {
						_: 'username.value',
						display: 'username.label'
					},
					className: 'all'
				},
				{
					data: 'full_name_last',
					className: 'all'
				},
				{
					data: 'email',
					className: 'all'
				},
				{
					data: 'browser',
					className: 'all'
				},
				{
					data: 'device',
					className: 'all'
				},
				{
					data: 'platform',
					className: 'all'
				},
				{
					data: {
						_: 'timestamp.value',
						display: 'timestamp.label'
					},
					className: 'all'
				},
				{
					data: {
						_: 'ip_address.value',
						display: 'ip_address.label'
					},
					className: 'all'
				},
				{
					data: 'options',
					className: 'all no-drag text-center'
				},
				{
					data: 'is_verified',
					visible: false
				},
				{
					data: 'is_approved',
					visible: false
				},
				{
					data: 'is_banned',
					visible: false
				},

			],
			responsive: {
				details: {
					display: $.fn.dataTable.Responsive.display.modal({
						header: function(row) {
							var data = row.data();

							return 'Details for Member ID #' + data.id;
						}
					}),
					renderer: $.fn.dataTable.Responsive.renderer.tableAll({
						tableClass: 'table'
					})
				}
			},
			rowCallback: function(row, data) {
				//console.log("data");
				//console.log(data);
				// Variable Defaults
				var tableRow = $(row);
				var currentTable = $(this);
				var dataTable = currentTable.DataTable();

				// Append ID
				tableRow.prop('id', dataTable.row(row).data().id);
				//console.log("dataTable.row(row).data()");
				//console.log(dataTable.row(row).data());

				// Render Row States
				switch (true) {
					case dataTable.row(row).data().is_banned:
						tableRow.addClass('table-danger');
						break;

						// case !dataTable.row(row).data().is_approved:
						// 	!dataTable.row(row).data().avatar ? tableRow.addClass('table-info') : tableRow.addClass('table-warning');
						// 	break;

						// case dataTable.row(row).data().is_subscribed:
						// 	tableRow.addClass('table-success');
						// 	break;
				}



				// Bind Click Event to Action
				tableRow.off().on('click', '[data-action]', function(event) {
					// Check Event
					if (event) {
						// Prevent Default
						event.preventDefault();

						// Variable Defaults
						var currentRow = event.delegateTarget;
						var action = $(this).data('action');
						var data = dataTable.row(currentRow).data();

						// Switch Action
						switch (action) {
							case 'access':
								// Handle Ajax Request
								$.ajax('/ajax/admin/items/travel-affiliate-members/access/' + data.id, {
									dataType: 'json',
									async: false,
									method: 'post',
									beforeSend: showLoader,
									complete: hideLoader,
									success: function(response) {
										// Switch Status
										switch (response.status) {
											case 'success':
												// Open New Tab
												open(response.redirect, '_blank');
												break;
											case 'error':
												displayMessage(response.message || Object.keys(response.errors).map(function(key) {
													return response.errors[key];
												}).join('<br>'), 'alert');
												break;
											default:
												displayMessage(response.message || 'Something went wrong.', 'alert');
										}
									}
								});
								break;
							case 'account-approval':
								// Handled via Delegation
								break;

							case 'deactivate':
								// Confirm
								if (confirm('Are you sure you want to deactivate this account?\n\nAccount: ' + data.username.value)) {
									// Handle Ajax Request
									$.ajax('/modals/admin/items/travel-affiliate-members/deactivation/' + data.id, {
										dataType: 'json',
										async: false,
										method: 'get',
										beforeSend: showLoader,
										complete: hideLoader,
										success: function(response) {
											// Switch Status
											switch (response.status) {
												case 'success':
													// Show Account Modal
													$(response.modal).on('shown.bs.modal', function() {
														// Variable Defaults
														var modal = $(this);

														// Bind Click Event to Confirm Deactivation
														modal.on('click', '[data-action="confirm-deactivation"]', function(event) {
															// Prevent Default
															event.preventDefault();

															// Handle Ajax Request
															$.ajax('/ajax/admin/items/travel-affiliate-members/deactivation/' + response.data.id, {
																dataType: 'json',
																async: false,
																method: 'post',
																beforeSend: showLoader,
																complete: hideLoader,
																success: function(response) {
																	// Hide Modal Show Success
																	modal.on('hidden.bs.modal', function() {
																		// Destroy Modal
																		$(this).remove();

																		// Switch Status
																		switch (response.status) {
																			case 'success':
																				// Display Message
																				displayMessage(response.message, 'success', function() {
																					$(this).on('hide.bs.modal', function() {
																						// Reload
																						dataTable.ajax.reload();
																					});
																				});
																				break;
																			case 'error':
																				displayMessage(response.message || Object.keys(response.errors).map(function(key) {
																					return response.errors[key];
																				}).join('<br>'), 'alert', null);
																				break;
																			default:
																				displayMessage(response.message || 'Something went wrong.', 'alert');
																		}
																	}).modal('hide');
																}
															});
														});
													}).on('hidden.bs.modal', destroyModal).modal();
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

							case 'toggle':
								// Variable Defaults
								var field = $(this).data('field');

								// Switch Field
								switch (field) {
									case 'is_approved':
										// Handle Ajax
										$.ajax('/user/toggle/travel-affiliate-members/approved/' + data.id, {
											data: {
												status: data.is_approved ? 1 : 0
											},
											dataType: 'json',
											method: 'post',
											async: true,
											beforeSend: showLoader,
											complete: hideLoader,
											success: function(response) {
												// Switch Status
												switch (response.status) {
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
									case 'is_banned':
										// Confirm Ban
										if (!confirm('Are you sure you want to ban this user?')) break;

										// Handle Ajax
										$.ajax('/user/toggle/travel-affiliate-members/banned/' + data.id, {
											data: {
												status: data.is_banned ? 1 : 0
											},
											dataType: 'json',
											method: 'post',
											async: true,
											beforeSend: showLoader,
											complete: hideLoader,
											success: function(response) {
												// Switch Status
												switch (response.status) {
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
									case 'is_verified':
										// Handle Ajax
										$.ajax('/user/toggle/travel-affiliate-members/verified/' + data.id, {
											data: {
												status: data.is_verified ? 1 : 0
											},
											dataType: 'json',
											method: 'post',
											async: true,
											beforeSend: showLoader,
											complete: hideLoader,
											success: function(response) {
												// Switch Status
												switch (response.status) {
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
										console.error('Unknown Field:', field);
								}
								break;
							case 'view-profile':
								// Handle Ajax
								$.ajax('/ajax/admin/items/travel-affiliate-members/view/' + data.id, {
									dataType: 'json',
									method: 'post',
									async: false,
									beforeSend: showLoader,
									complete: hideLoader,
									success: function(response) {
										// Switch Status
										switch (response.status) {
											case 'success':
												// Show Preview
												$.fancybox.open({
													src: response.html,
													type: 'html',
													smallBtn: false,
													touch: false,
													width: '100%'
												});
												break;
											case 'error':
											default:
												displayMessage(response.message || 'Something went wrong.', 'alert');
										}
									}
								});
								break;
							default:
								console.error('Unknown Action:', action);
						}
					}
				});
			}
		});


		// Bind Action for Account Approval
		$(this.body).on('click', '[data-action="account-approval"]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);

			// Handle Ajax Request
			$.ajax('/ajax/admin/items/travel-affiliate-members/account-approval/accounts/approve', {
				data: {
					member_id: button.closest('[data-member-id]').data('member-id')
				},
				dataType: 'json',
				method: 'post',
				async: false,
				success: function(response) {
					console.log(response)
					// Switch Status
					switch (response.status) {
						case 'success':
							// Hide Modal
							displayMessage(response.message || 'Travel Affiliate Member Approved', 'alert');
							break;
						case 'error':
						default:
							displayMessage(response.message || 'Something went wrong.', 'alert');
					}
				}
			});
		});

		////////////////////////////////////////////////////////////////////////

		//IsVerified

		$(this.body).on('click', '.badge-ev-light', function() {
			var table = $('#data-table').DataTable();

			// Clear any existing search filters
			table.search('').draw();

			// Use a custom filter function
			$.fn.dataTable.ext.search.push(
				function(settings, data, dataIndex) {

					// adjust these indexes based on your actual table structure.
					var isVerified = data[10];
					var isBanned = data[12];

					// Adjust the condition based on how your data is represented (e.g., "true"/"false", 1/0)
					return isVerified === "true" && isBanned === "false";
				}
			);

			// Redraw the table with the filter applied
			table.draw();

			// Remove the custom filter to not affect global search
			$.fn.dataTable.ext.search.pop();
		});

		////////////////////////////////////////////////////////////////////////

		//IsApproved

		$(this.body).on('click', '.badge-light', function() {
			var table = $('#data-table').DataTable();

			// Clear any existing search filters
			table.search('').draw();

			// Use a custom filter function
			$.fn.dataTable.ext.search.push(
				function(settings, data, dataIndex) {

					// adjust these indexes based on your actual table structure.
					var isApproved = data[11];
					var isBanned = data[12];

					// Adjust the condition based on how your data is represented (e.g., "true"/"false", 1/0)
					return isApproved === "true" && isBanned === "false";
				}
			);

			// Redraw the table with the filter applied
			table.draw();

			// Remove the custom filter to not affect global search
			$.fn.dataTable.ext.search.pop();
		});

		////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////


		//isBanned

		$(this.body).on('click', '.badge-danger', function() {
			var table = $('#data-table').DataTable();

			// Clear any existing search filters
			table.search('').draw();

			// Use a custom filter function
			$.fn.dataTable.ext.search.push(
				function(settings, data, dataIndex) {

					// adjust these indexes based on your actual table structure.
					var isBanned = data[12];

					// Adjust the condition based on how your data is represented (e.g., "true"/"false", 1/0)
					return isBanned === "true";
				}
			);

			// Redraw the table with the filter applied
			table.draw();

			// Remove the custom filter to not affect global search
			$.fn.dataTable.ext.search.pop();
		});

		////////////////////////////////////////////////////////////////////////

	});
</script>

<?php include('includes/body-close.php'); ?>