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
$page_title = 'View Members';

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
	.badge-indigo {
		background-color: #6610f2 !important;
		color: #FFFFFF !important;
	}

	.table-indigo {
		background-color: #6610f2 !important;
		color: #fff !important;
	}

	.badge-gold {
		background-color: #d8bb66 !important;
		color: #FFFFFF !important;
	}

	.table-gold {
		background-color: #d8bb66 !important;
		color: #fff !important;
	}
	
	.badge-slate {
		background-color: #2F4F4F !important;
	}

	.table-slate {
		background-color: #2F4F4F !important;
		color: #fff !important;
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

		<span id="filter-status-actions" class="d-block mb-3">
			<strong class="text-muted">Legend:</strong>
			<button data-action="email-verified" class="badge badge-ev-light"><i class="fa-solid fa-badge-check"></i> Email Verified</button>
			<button data-action="email-unverified" class="badge badge-unverified"><i class="fa-solid fa-badge-check"></i> Email Unverified</button>
			<button data-action="approved" class="badge badge-light">Approved</button>
			<button data-action="subscribed" class="badge badge-success">Subscribed</button>
			<button data-action="banned" class="badge badge-danger">Banned</button>
			<button data-action="unapproved-no-avatar" class="badge badge-info">w/o Avatar</button>
			<button data-action="unapproved-avatar" class="badge badge-warning">Unapproved w/ Avatar</button>
			<button data-action="teacher" class="badge badge-indigo text-white"><i class="fa-solid fa-chalkboard-user"></i> Teachers</button>
			<button data-action="teacher_approved" class="badge badge-gold text-white"><i class="fa-solid fa-chalkboard-user"></i> Approved Teachers</button>
			<button data-action="is_staff" class="badge badge-slate text-white"><i class="fa-solid fa-user-helmet-safety"></i> Staff Member</button>
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
	jQuery(document).ready(function($) {

		// Variable Defaults
		var tableElement = $('#data-table');
		var tableOptions = tableElement.data('tableOptions');

		// Init Data Tables
		tableElement.DataTable({
			order: [
				[2, 'asc']
			],
			serverSide: true,

			ajax: {
				url: window.location.href
			},
			searchDelay: 5000,
			pageLength: 250,
			//iDeferLoading: 250,
			//deferLoading: true,
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
					className: 'all',
					orderable: false
				},
				{
					data: 'device',
					className: 'all',
					orderable: false
				},
				{
					data: 'platform',
					className: 'all',
					orderable: false
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
					className: 'all no-drag text-center',
					orderable: false
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
					data: 'is_subscribed',
					visible: false
				},
				{
					data: 'is_banned',
					visible: false
				},
				{
					data: 'avatar',
					visible: false
				},
				{
					data: 'is_teacher',
					visible: false
				},
				{
					data: 'is_teacher_approved',
					visible: false
				},
				{
					data: 'is_staff',
					visible: false
				}

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

					case !dataTable.row(row).data().is_approved:
						!dataTable.row(row).data().avatar
							? tableRow.addClass('table-info')
							: tableRow.addClass('table-warning');
						break;

					case dataTable.row(row).data().is_staff:
						tableRow.addClass('table-slate');
						break;

					case dataTable.row(row).data().is_teacher_approved:
						tableRow.addClass('table-gold');
						break;

					case dataTable.row(row).data().is_teacher:
						tableRow.addClass('table-indigo');
						break;

					case dataTable.row(row).data().is_subscribed:
						tableRow.addClass('table-success');
						break;
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
								$.ajax('/ajax/admin/items/members/access/' + data.id, {
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
							case 'compensate':
								// Confirm
								if (confirm('Are you sure you want to compensate this account?\n\nAccount: ' + data.username.value)) {
									// Handle Ajax Request
									$.ajax('/ajax/admin/items/members/compensation/' + data.id, {
										dataType: 'json',
										async: false,
										method: 'post',
										beforeSend: showLoader,
										complete: hideLoader,
										success: function(response) {
											// Switch Status
											switch (response.status) {
												case 'success':
													// Display Message
													displayMessage(response.message, 'success');
													break;
												case 'error':
												default:
													displayMessage(response.message || 'Something went wrong.', 'alert');
											}

											// Reload
											dataTable.ajax.reload();
										}
									});
								}
								break;
							case 'deactivate':
								// Confirm
								if (confirm('Are you sure you want to deactivate this account?\n\nAccount: ' + data.username.value)) {
									// Handle Ajax Request
									$.ajax('/modals/admin/items/members/deactivation/' + data.id, {
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
															$.ajax('/ajax/admin/items/members/deactivation/' + response.data.id, {
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
							case 'free_drink':
								// Confirm
								if (confirm('Are you sure you want to issue a free drink?\n\nAccount: ' + data.username.value)) {
									// Handle Ajax Request
									$.ajax('/ajax/admin/items/members/free-drink/confirm/' + data.id, {
										dataType: 'json',
										async: false,
										method: 'post',
										beforeSend: showLoader,
										complete: hideLoader,
										success: function(response) {
											// Switch Status
											switch (response.status) {
												case 'success':
													displayMessage(response.message, 'success', function() {
														$(this).on('hide.bs.modal', function() {
															dataTable.ajax.reload();
														});
													});
													break;
												case 'error':
													displayMessage(response.message || Object.keys(response.errors).map(function(key) {
														return response.errors[key];
													}).join('<br>'), 'alert', function() {
														$(this).on('hide.bs.modal', function() {
															dataTable.ajax.reload();
														});
													});
													break;
												default:
													displayMessage(response.message || 'Something went wrong.', 'alert', function() {
														$(this).on('hide.bs.modal', function() {
															dataTable.ajax.reload();
														});
													});
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
										$.ajax('/user/toggle/members/approved/' + data.id, {
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
										$.ajax('/user/toggle/members/banned/' + data.id, {
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
										$.ajax('/user/toggle/members/verified/' + data.id, {
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
									case 'is_staff':
										// Handle Ajax
										$.ajax('/user/toggle/members/staff/' + data.id, {
											data: {
												status: data.is_staff ? 1 : 0
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
									case 'is_teacher_approved':
										// Handle Ajax
										$.ajax('/user/toggle/members/teacher/' + data.id, {
											data: {
												status: data.is_teacher_approved ? 1 : 0
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
								$.ajax('/ajax/admin/items/members/view/' + data.id, {
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
			var dataset = button.data();

			// Handle Ajax Request
			$.ajax('/modals/admin/items/members/account-approval', {
				dataType: 'html',
				async: false,
				method: 'get',
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					$(response).on('shown.bs.modal', function() {
						// Variable Defaults
						var modal = $(this);
						var form = modal.find('form');
						var input = form.find('textarea[name="data"]');

						// Bind QR Functionality to Textarea
						input.on('focusin', function() {
							input.attr('placeholder', 'QR Scanner Ready');
						}).on('focusout', function() {
							input.attr('placeholder', 'QR Scanner NOT Ready: Click Here');
						}).on('keypress', function(event) {
							// Trigger on "Enter"
							(event.which === 13) && form.trigger('submit');
						}).trigger('focus');

						// Bind Submit Functionality to Form
						form.on('submit', function(event) {
							// Prevent Default
							event.preventDefault();

							// Handle Ajax Request
							$.ajax('/ajax/admin/items/members/account-approval/fetch-account', {
								data: $(this).serializeArray(),
								dataType: 'json',
								async: false,
								method: 'post',
								beforeSend: showLoader,
								complete: hideLoader,
								success: function(response) {
									// Switch Status
									switch (response.status) {
										case 'success':
											modal.on('hidden.bs.modal', function() {
												// Destroy Modal
												$(this).remove();

												// Show Account Modal
												$(response.modal).on('shown.bs.modal', function() {
													// Variable Defaults
													var modal = $(this);
													var table = modal.find('table');
													var rows = table.find('tr');

													// Set Approval Button
													var approvalButton = modal.find('[data-type="account"]').find('[data-account-approval-action="approve"]');

													// Bind Change Event to Row
													rows.on('change', function() {
														// Update Rows
														rows = table.find('tr');

														// Apply Styles to Rows
														rows.filter('[data-approved="1"]').removeClass('table-danger').addClass('table-success');
														rows.filter('[data-approved="0"]').removeClass('table-success').addClass('table-danger');

														// Enable/Disable Avatar/Post Approval
														rows.filter('[data-approved="1"]').find('[data-account-approval-action]').addClass('disabled');
														rows.filter('[data-approved="0"]').find('[data-account-approval-action]').removeClass('disabled');

														// Always Allow Preview
														rows.find('[data-account-approval-action="preview"]').removeClass('disabled');

														// Enable/Disable Account Approval
														approvalButton.prop('disabled', rows.filter('[data-approved="0"]').length);
													}).first().trigger('change');

													// Bind Click Event to Account Approval Actions
													modal.on('click', '[data-account-approval-action]', function(event) {
														// Prevent Default
														event.preventDefault();

														// Variable Defaults
														var button = $(this);
														var type = button.closest('[data-type]').data('type');
														var action = button.data('account-approval-action');
														var row = button.closest('tr');

														// Switch Type
														switch (type) {
															case 'account':
																// Switch Action
																switch (action) {
																	case 'approve':
																		// Handle Ajax
																		$.ajax('/ajax/admin/items/members/account-approval/accounts/approve', {
																			data: {
																				member_id: modal.data('member-id')
																			},
																			dataType: 'json',
																			method: 'post',
																			async: false,
																			success: function(response) {
																				// Switch Status
																				switch (response.status) {
																					case 'success':
																						// Hide Modal
																						modal.on('hidden.bs.modal', function() {
																							// Destroy Modal
																							$(this).remove();

																							// Display Message
																							displayMessage(response.message, 'success', function() {
																								$(this).on('hide.bs.modal', function() {
																									// Show Loader
																									showLoader();

																									// Reload Page
																									location.reload();
																								});
																							});
																						}).modal('hide');
																						break;
																					case 'error':
																					default:
																						displayMessage(response.message || 'Something went wrong.', 'alert');
																				}
																			}
																		});
																		break;
																	case 'preview':
																		// Handle Ajax
																		$.ajax('/ajax/admin/items/members/account-approval/accounts/preview', {
																			data: {
																				member_id: modal.data('member-id')
																			},
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
																							src: response.preview,
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
																		console.error('unknown account approval action:', action, ', for type:', type);
																}
																break;
															case 'avatar':
																// Switch Action
																switch (action) {
																	case 'approve':
																		// Handle Ajax
																		$.ajax('/ajax/admin/items/members/account-approval/avatars/approve', {
																			data: {
																				member_id: modal.data('member-id'),
																				avatar_id: row.data('avatar-id')
																			},
																			dataType: 'json',
																			method: 'post',
																			async: false,
																			success: function(response) {
																				// Switch Status
																				switch (response.status) {
																					case 'success':
																						// Approve Row
																						row.attr('data-approved', 1).trigger('change');
																						break;
																					case 'error':
																					default:
																						displayMessage(response.message || 'Something went wrong.', 'alert');
																				}
																			}
																		});
																		break;
																	case 'reject':
																		// Confirm Rejection
																		if (confirm('Are you sure you want to reject this avatar?')) {
																			// Handle Ajax
																			$.ajax('/ajax/admin/items/members/account-approval/avatars/reject', {
																				data: {
																					member_id: modal.data('member-id'),
																					avatar_id: row.data('avatar-id')
																				},
																				dataType: 'json',
																				method: 'post',
																				async: false,
																				success: function(response) {
																					// Switch Status
																					switch (response.status) {
																						case 'success':
																							// Remove Row
																							row.remove();

																							// Check Rows
																							if (table.find('tr').length === 0) {
																								// Remove Table
																								table.remove();

																								// Enable Account Approval
																								approvalButton.prop('disabled', false);
																							}
																							break;
																						case 'error':
																						default:
																							displayMessage(response.message || 'Something went wrong.', 'alert');
																					}
																				}
																			});
																		}
																		break;
																	default:
																		console.error('unknown account approval action:', action, ', for type:', type);
																}
																break;
															case 'post':
																// Switch Action
																switch (action) {
																	case 'approve':
																		// Handle Ajax
																		$.ajax('/ajax/admin/items/members/account-approval/posts/approve', {
																			data: {
																				member_id: modal.data('member-id'),
																				post_id: row.data('post-id')
																			},
																			dataType: 'json',
																			method: 'post',
																			async: false,
																			success: function(response) {
																				// Switch Status
																				switch (response.status) {
																					case 'success':
																						// Approve Row
																						row.attr('data-approved', 1).trigger('change');
																						break;
																					case 'error':
																					default:
																						displayMessage(response.message || 'Something went wrong.', 'alert');
																				}
																			}
																		});
																		break;
																	case 'preview':
																		// Handle Ajax
																		$.ajax('/ajax/admin/items/members/account-approval/posts/preview', {
																			data: {
																				member_id: modal.data('member-id'),
																				post_id: row.data('post-id')
																			},
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
																							src: response.preview,
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
																	case 'reject':
																		// Confirm Rejection
																		if (confirm('Are you sure you want to reject this post?')) {
																			// Handle Ajax
																			$.ajax('/ajax/admin/items/members/account-approval/posts/reject', {
																				data: {
																					member_id: modal.data('member-id'),
																					post_id: row.data('post-id')
																				},
																				dataType: 'json',
																				method: 'post',
																				async: false,
																				success: function(response) {
																					// Switch Status
																					switch (response.status) {
																						case 'success':
																							// Remove Row
																							row.remove();

																							// Check Rows
																							if (table.find('tr').length === 0) {
																								// Remove Table
																								table.remove();

																								// Enable Account Approval
																								approvalButton.prop('disabled', false);
																							}
																							break;
																						case 'error':
																						default:
																							displayMessage(response.message || 'Something went wrong.', 'alert');
																					}
																				}
																			});
																		}
																		break;
																	default:
																		console.error('unknown account approval action:', action, ', for type:', type);
																}
																break;
															default:
																console.error('unknown account approval type:', type);
														}
													});
												}).on('hidden.bs.modal', destroyModal).modal();
											}).modal('hide');
											break;
										case 'error':
											modal.on('hidden.bs.modal', function() {
												// Destroy Modal
												$(this).remove();

												// Display Message
												displayMessage(response.message || Object.keys(response.errors).map(function(key) {
													return response.errors[key];
												}).join('<br>'), 'alert', null);
											}).modal('hide');
											break;
										default:
											modal.on('hidden.bs.modal', function() {
												// Destroy Modal
												$(this).remove();

												// Display Message
												displayMessage(response.message || 'Something went wrong.', 'alert');
											}).modal('hide');
									}
								}
							});
						});

						// Check Activation Code for Manual Override
						if (dataset.hasOwnProperty('activationCode')) {
							// Handle Ajax Request
							$.ajax('/ajax/admin/items/members/account-approval/fetch-activation-code', {
								data: {
									member_id: button.closest('[data-member-id]').data('member-id')
								},
								dataType: 'json',
								async: false,
								method: 'post',
								beforeSend: showLoader,
								complete: hideLoader,
								success: function(response) {
									// Switch Status
									switch (response.status) {
										case 'success':
											input.val(response.hash);
											form.trigger('submit');
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
					}).on('hidden.bs.modal', function() {
						$(this).remove();
					}).modal({
						backdrop: 'static',
						keyboard: false
					});
				}
			});
		});

		////////////////////////////////////////////////////////////////////////

		$('[type="search"]').off('keyup.DT input.DT');

		var searchDelay = null;

		$('[type="search"]').on('keyup', function() {
			var search = $('[type="search"]').val();

			clearTimeout(searchDelay);

			searchDelay = setTimeout(function() {
				if (search != null) {
					tableElement.DataTable().search('').columns().every(function() {
						this.search('');
					});
					tableElement.DataTable().search(search).draw();
				}
			}, 2000);
		});

		$(this.body).on('click', 'button[data-action]', function() {
			var table = $('#data-table').DataTable();

			// Clear all global and column-specific searches
			table.search('').columns().every(function() {
				this.search('');
			});

			// Draw the table to apply cleared filters
			table.draw();

			switch ($(this).data("action")) {

				case "email-verified":
					table.column(10).search('true').draw();
					break;
				case "email-unverified":
					table.column(10).search('false').draw();
					break;
				case "approved":
					table.column(11).search('true').draw();
					break;
				case "subscribed":
					table.column(12).search('true').draw();
					break;
				case "banned":
					table.column(13).search('true').draw();
					break;
				case "unapproved-no-avatar":
					table.column(14).search('false').draw();
					break;
				case "unapproved-avatar":
					table.column(14).search('true').draw();
					break;
				case "teacher":
					table.column(15).search('true').draw();
					break;
				case "teacher_approved":
					table.column(16).search('true').draw();
					break;
				case "is_staff":
					table.column(17).search('true').draw();
					break;
			}

			// You might want to reload the data if the source is dynamic and needs to reflect changes
			table.ajax.reload(null, false);
		});




	});
</script>

<?php include('includes/body-close.php'); ?>