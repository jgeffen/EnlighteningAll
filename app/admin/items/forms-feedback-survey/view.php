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
	$page_title = 'View Form Submissions: Feedback Survey';
	
	// Start Header
	include('includes/header.php');
?>

<style>
	.input-group {
		width: auto;
	}

	.input-group input {
		height: auto;
		padding: 10px 25px;
	}
</style>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn" class="d-flex justify-content-between">
			<h1><?php echo $page_title; ?></h1>
			
			<div class="input-group">
				<div class="form-label-group mb-0">
					<input type="date" class="form-control" name="date_from" placeholder="Date From" aria-label="From">
					<label>Date From</label>
				</div>
				
				<div class="form-label-group mb-0">
					<input type="date" class="form-control" name="date_to" placeholder="Date To" aria-label="To">
					<label>Date To</label>
				</div>
				
				<div class="input-group-append">
					<button class="btn btn-primary" data-export="excel">
						<i class="fa-regular fa-file-excel"></i>
						<span class="d-none d-sm-inline">Export</span>
					</button>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered nowrap">
					<thead>
						<tr>
							<th>ID</th>
							<th>Avg. Rating</th>
							<th>Name</th>
							<th>Email</th>
							<th>Phone</th>
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

		// Init Flatpickr
		(function(dateFrom, dateTo) {
			var fpDateFrom = flatpickr(dateFrom, {
				mode: 'single',
				altInput: true,
				altFormat: 'M j, Y',
				dateFormat: 'Y-m-d',
				defaultDate: new Date().fp_incr(-7),
				plugins: [new confirmDatePlugin({
					confirmIcon: '<i class="fa-solid fa-circle-check ml-1"></i>',
					confirmText: 'Okay!',
					showAlways: true,
					theme: 'light'
				})],
				maxDate: new Date(),
				onChange: function(selectedDates, dateStr, instance) {
					fpDateTo.set('minDate', dateStr);
				}
			});

			var fpDateTo = flatpickr(dateTo, {
				mode: 'single',
				altInput: true,
				altFormat: 'M j, Y',
				dateFormat: 'Y-m-d',
				defaultDate: new Date(),
				plugins: [new confirmDatePlugin({
					confirmIcon: '<i class="fa-solid fa-circle-check ml-1"></i>',
					confirmText: 'Okay!',
					showAlways: true,
					theme: 'light'
				})],
				maxDate: new Date(),
				onChange: function(selectedDates, dateStr, instance) {
					fpDateFrom.set('maxDate', dateStr);
				}
			});
		})(document.querySelector('input[name="date_from"]'), document.querySelector('input[name="date_to"]'));

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
				{ data: 'id', className: 'all' },
				{ data: { _: 'avg_rating', display: 'avg_rating' }, className: 'all' },
				{ data: { _: 'item.contact_name', display: 'item.contact_name' }, className: 'all' },
				{ data: { _: 'item.contact_email', display: 'item.contact_email' }, className: 'all' },
				{ data: { _: 'item.contact_phone', display: 'item.contact_phone' }, className: 'all' },
				{ data: 'timestamp', className: 'all' },
				{ data: 'options', className: 'all no-drag' }
			],
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
						console.log(data);

						// Switch Action
						switch(action) {
							case 'delete':
								// Confirm Deletion
								if(confirm('Are you sure you want to delete this?')) {
									// Handle Ajax
									$.ajax('/user/delete/forms-feedback-survey/' + data.item.id, {
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

		// Bind Click Event to Export
		$('button[data-export="excel"]').on('click', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button    = $(this);
			var wrapper   = button.parents('.input-group');
			var date_from = wrapper.find('input[name="date_from"]').val();
			var date_to   = wrapper.find('input[name="date_to"]').val();

			// Download Export
			window.location.href = '/user/export?type=feedback-survey&date_from=' + encodeURIComponent(date_from) + '&date_to=' + encodeURIComponent(date_to);
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

