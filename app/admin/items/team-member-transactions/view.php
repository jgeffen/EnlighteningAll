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
$page_title = $member->getTitle('Event Transactions');

// Start Header
include('includes/header.php');


?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>

		<?php TeamMemberTransactionSubMenu($member); ?>

		<div style="display: grid; gap: .25rem;">
			<h2>Employee Account Info</h2>
			<span><b>Affiliate Full Name:</b> <?php echo $member->getFirstName(); ?> <?php echo $member->getLastName(); ?></span>
			<span><b>Username:</b> <?php echo $member->getUsername(); ?></span>
			<span><b>Team Member Link:</b> /?TeamMember=<?php echo $member->getid(); ?></span>

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
							<th>Transaction Amount</th>
							<th>Purchase Event Link</th>
							<th>Purchase Date/Time</th>
							<th>Purchaser Profile Link</th>
							<th>CC Aproved</th>
							<th>Approved</th>
							<th>Banned</th>
							<th>Event End Date</th>
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
					data: 'transaction_id',
					className: 'all transaction_id'
				},
				{
					data: 'amount',
					className: 'all'
				},
				{
					data: 'purchase_event_link',
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
					data: 'confirmed_payment',
					className: 'all'
				},
				{
					data: 'approved',
					className: 'all',
				},
				{
					data: 'is_banned',
					className: 'all',
				},
				{
					data: 'event_end_date',
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

		$(this.body).on('change', '.admin-approved-affiliate-transaction-checkbox', function(event) {
			// Check the checkbox's checked property
			var isChecked = $(this).prop('checked') ? 1 : 0;

			console.log($(this).parent().parent().find(".transaction_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/approve-affiliate-transaction",
				data: {
					admin_approved: isChecked,
					transaction_id: $(this).parent().parent().find(".transaction_id").text()
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

		$(this.body).on('change', '.admin-banned-affiliate-transaction-checkbox', function(event) {
			// Check the checkbox's checked property
			var isChecked = $(this).prop('checked') ? 1 : 0;

			console.log($(this).parent().parent().find(".transaction_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/banned-affiliate-transaction",
				data: {
					is_banned: isChecked,
					transaction_id: $(this).parent().parent().find(".transaction_id").text()
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