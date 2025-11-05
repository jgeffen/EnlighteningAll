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
$member = TravelAffiliateMembership::Init($dispatcher->getTableId());

// Check Member
if (is_null($member)) Admin\Render::ErrorDocument(404);

// Set Title
$page_title = $member->getTitle('Room Transactions');

// Start Header
include('includes/header.php');


?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>

		<?php AffiliateTransactionSubMenu($member); ?>

		<div style="display: grid; gap: .25rem;">
			<h2>Affiliate Account Info</h2>
			<span><b>Affiliate Full Name:</b> <?php echo $member->getFirstName(); ?> <?php echo $member->getLastName(); ?></span>
			<span><b>Username:</b> <?php echo $member->getUsername(); ?></span>
			<span><b>Email:</b> <?php echo $member->getEmail(); ?></span>

			<span><b>Travel Agency:</b> <?php echo $member->getTravelAgency(); ?></span>
			<span><b>EIN Number:</b> <?php echo $member->getTravelAgencyEinNumber(); ?></span>


			<span><b>Address:</b> <?php echo $member->getAddressLine1(); ?>, <?php echo $member->getAddressLine2(); ?>, <?php echo $member->getAddressCity(); ?>, <?php echo $member->getAddressState(); ?>, <?php echo $member->getAddressZipCode(); ?>, <?php echo $member->getAddressCountry(); ?></span>

		</div>
		<hr />
		<br />


		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered nowrap w-100" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
					<thead>
						<tr>

							<th>Affiliate ID</th>
							<th>Transaction ID</th>
							<th>Amount</th>
							<th>Room Commission Rate</th>
							<th>Commission</th>
							<th>Purchase Date/Time</th>
							<th>Purchaser Profile Link</th>
							<th>Booking Dates</th>
							<th>Room Name</th>
							<th>Room End Date</th>
							<th>Approved</th>
							<th>Banned</th>

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
				[0, 'desc']
			],
			pageLength: 250,
			paging: true,
			searching: true,
			stateSave: true,
			info: true,
			ordering: true,
			rowReorder: false,
			columns: [{
					data: 'affiliate_id',
					className: 'all'
				},
				{
					data: 'id',
					className: 'all transaction_table_id'
				},
				{
					data: 'amount',
					className: 'all'
				},
				{
					data: 'ticket_commission_rate',
					className: 'all'
				},
				{
					data: 'affiliate_earned_commission',
					className: 'all'
				},
				{
					data: 'date_time',
					className: 'all',
				},
				{
					data: 'purchaser_profile_link',
					className: 'all'
				},
				{
					data: 'booking_dates',
					className: 'all'
				},
				{
					data: 'room_name',
					className: 'all'
				},
				{
					data: 'date_end',
					className: 'all'
				},
				{
					data: 'admin_approved',
					className: 'all'
				},
				{
					data: 'is_banned',
					className: 'all'
				},

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
				var tableRow = $(row);
				var currentTable = $(this);
				var dataTable = currentTable.DataTable();

				// Append ID
				tableRow.prop('id', dataTable.row(row).data().id);

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
							default:
								console.error('Unknown Action:', action, data);
						}
					}
				});
			}
		});

		$(this.body).on('change', '.admin-approved-affiliate-room-transaction-checkbox', function(event) {
			// Check the checkbox's checked property
			var isChecked = $(this).prop('checked') ? 1 : 0;

			console.log($(this).parent().parent().find(".transaction_table_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/approve-affiliate-room-transaction",
				data: {
					admin_approved: isChecked,
					id: $(this).parent().parent().find(".transaction_table_id").text()
				},
				success: function(response) {
					// Handle success
					console.log(response);
					window.location.reload();
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error);
				}
			});
		});

		$(this.body).on('change', '.admin-banned-affiliate-room-transaction-checkbox', function(event) {
			// Check the checkbox's checked property
			var isChecked = $(this).prop('checked') ? 1 : 0;

			console.log($(this).parent().parent().find(".transaction_table_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/banned-affiliate-room-transaction",
				data: {
					is_banned: isChecked,
					id: $(this).parent().parent().find(".transaction_table_id").text()
				},
				success: function(response) {
					// Handle success
					console.log(response);
					window.location.reload();
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error);
				}
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>