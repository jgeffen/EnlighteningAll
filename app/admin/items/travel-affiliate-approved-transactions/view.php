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
$page_title = $member->getTitle('Approved Transactions');




//print_r($approved_transactions);

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

				<hr>

				<?php

				///////////////////////////////////////////////////////////////////////////

				// Attempt to connect to the database and fetch transactions
				try {
					$approvedEventTransactions = Database::Action("SELECT *, 'event' AS type FROM `affiliate_transactions` WHERE `admin_approved` = :admin_approved AND `is_paid` = :is_paid AND `affiliate_id` = :affiliate_id ORDER BY `timestamp`", [
						'admin_approved' => 1,
						'is_paid' => 0,
						'affiliate_id' => $member->getId()
					])->fetchAll(PDO::FETCH_ASSOC);

					$approvedRoomTransactions = Database::Action("SELECT *, 'room' AS type FROM `affiliate_room_transactions` WHERE `admin_approved` = :admin_approved AND `is_paid` = :is_paid AND `affiliate_id` = :affiliate_id ORDER BY `timestamp`", [
						'admin_approved' => 1,
						'is_paid' => 0,
						'affiliate_id' => $member->getId()
					])->fetchAll(PDO::FETCH_ASSOC);
				} catch (PDOException $e) {
					echo 'Query failed: ' . $e->getMessage();
					exit;
				}

				$allTransactions = array_merge($approvedEventTransactions, $approvedRoomTransactions);

				///////////////////////////////////////////////////////////////////////////

				// Group the transactions
				$groupedTransactions = groupTransactionsByMonth($allTransactions);

				?>

				<!---------------------------------------------------------------------------------------->

				<?php foreach ($groupedTransactions as $yearMonth => $types) { ?>

					<h1>
						Transactions for <?php echo htmlspecialchars($yearMonth); ?>
					</h1>

					<?php

					$events_total_commission = 0;
					$rooms_total_commission = 0;
					?>

					<?php if (!empty($types['events'])) { ?>
						<h2>Events/Tickets</h2>
						<!-- Include your HTML table structure here for Events/Tickets -->
						<!-- Loop through $types['events'] to display each transaction -->
						<table class="table table-bordered nowrap w-100 dataTable no-footer dtr-inline">
							<thead>
								<tr>
									<th>Affiliate ID</th>
									<th>Transaction ID</th>
									<th>Amount</th>
									<th>Commission Rate</th>
									<th>Commission</th>
									<th>Event Link</th>
									<th>Purchase Date/Time</th>
									<th>Event End Date</th>
									<th>Purchaser Profile</th>
									<th>CC Approved</th>
									<th>Admin Approved</th>
									<th>Admin Paid</th>

								</tr>
							</thead>

							<?php $total_commission = 0; ?>

							<?php foreach ($types['events'] as $transaction) { ?>

								<?php


								$earned_commission = round(htmlspecialchars($transaction['amount']) * (htmlspecialchars($transaction['ticket_commission_rate']) / 100), 2);

								$events_total_commission += $earned_commission;

								$total_commission += $earned_commission;

								try {
									$purchaser_profile_username = Database::Action("SELECT `username` FROM `members` WHERE `id` = :id", array(
										'id' => htmlspecialchars($transaction['purchaser_social_member_id'])
									))->fetchAll(PDO::FETCH_COLUMN, 0);
								} catch (PDOException $e) {
									echo 'Query failed: ' . $e->getMessage();
								}

								$timestamp = new DateTime($transaction['timestamp']);

								$formattedTimestamp = $timestamp->format('M jS, Y, g:iA');

								?>
								<tr data-purchase-type="event">

									<td class="affiliate_id"><?php echo htmlspecialchars($transaction['affiliate_id']); ?></td>

									<td class="transaction_id"><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>

									<td class="amount">$<?php echo htmlspecialchars($transaction['amount']); ?></td>

									<td class="commission_rate"><?php echo htmlspecialchars($transaction['ticket_commission_rate']); ?>%</td>

									<td class="commission">$<?php echo $earned_commission; ?></td>

									<td><a target="_blank" href="/<?php echo htmlspecialchars($transaction['type_table_id']); ?>.event">Event Link<a></td>

									<td class="purchase_date"><?php echo htmlspecialchars($transaction['timestamp']); ?></td>

									<td class="date_end"><?php echo htmlspecialchars($transaction['date_end']); ?></td>

									<td><a target="_blank" href="/members/profile/<?php echo $purchaser_profile_username[0] ?? null; ?>">Profile Link<a></td>

									<td><?php echo htmlspecialchars($transaction['confirmed_payment']); ?></td>

									<td><input class="admin-approved-affiliate-transaction-checkbox" type="checkbox" checked style="transform: scale(1.5)" /></td>

									<td><input class="admin-paid-affiliate-transaction-checkbox" type="checkbox" value="" style="transform: scale(1.5)"></td>


								</tr>

							<?php	} ?>

						</table><br>

						<h3>Event/Ticket Commission Total: $<?php echo $total_commission; ?></h3>

					<?php } ?>

					<br />

					<?php if (!empty($types['rooms'])) { ?>
						<h2>Rooms</h2>
						<!-- Include your HTML table structure here for Rooms -->
						<!-- Loop through $types['rooms'] to display each transaction -->

						<table class="table table-bordered nowrap w-100 dataTable no-footer dtr-inline">
							<thead>
								<tr>

									<th>Affiliate ID</th>
									<th>Transaction ID</th>
									<th>Amount</th>
									<th>Commission Rate</th>
									<th>Commission</th>
									<th>Purchase Date/Time</th>
									<th>Purchaser Profile</th>
									<th>Booking Dates</th>
									<th>Room End Date</th>
									<th>Room Name</th>
									<th>Admin Approved</th>
									<th>Admin Paid</th>

								</tr>
							</thead>

							<?php $total_commission = 0; ?>

							<?php foreach ($types['rooms'] as $transaction) { ?>

								<?php

								$earned_commission = round(htmlspecialchars($transaction['amount']) * (htmlspecialchars($transaction['ticket_commission_rate']) / 100), 2);
								$rooms_total_commission += $earned_commission;

								$total_commission += $earned_commission;

								try {
									$purchaser_profile_username = Database::Action("SELECT `username` FROM `members` WHERE `id` = :id", array(
										'id' => htmlspecialchars($transaction['purchaser_social_member_id'])
									))->fetchAll(PDO::FETCH_COLUMN, 0);
								} catch (PDOException $e) {
									echo 'Query failed: ' . $e->getMessage();
								}

								// $timestamp = new DateTime($transaction['timestamp']);
								// $formattedTimestamp = $timestamp->format('M jS, Y, g:iA');

								?>
								<tr data-purchase-type="room">

									<td class="affiliate_id"><?php echo htmlspecialchars($transaction['affiliate_id']); ?></td>

									<td class="transaction_id"><?php echo htmlspecialchars($transaction['id']); ?></td>

									<td class="amount">$<?php echo htmlspecialchars($transaction['amount']); ?></td>

									<td class="commission_rate"><?php echo htmlspecialchars($transaction['ticket_commission_rate']); ?>%</td>

									<td class="commission">$<?php echo $earned_commission; ?></td>

									<td class="purchase_date"><?php echo htmlspecialchars($transaction['timestamp']); ?></td>


									<td>
										<?php if (!empty($purchaser_profile_username[0])) { ?>
											<a target="_blank" href="/members/profile/<?php echo htmlspecialchars($purchaser_profile_username[0], ENT_QUOTES, 'UTF-8'); ?>">Profile Link</a>
										<?php } else { ?>
											N/A
										<?php } ?>
									</td>

									<td><?php echo htmlspecialchars($transaction['booking_dates']); ?></td>

									<td class="date_end"><?php echo htmlspecialchars($transaction['date_end']); ?></td>

									<td><?php echo htmlspecialchars($transaction['room_name']); ?></td>

									<td><input class="admin-approved-affiliate-room-transaction-checkbox" type="checkbox" checked style="transform: scale(1.5)" /></td>

									<td><input class="admin-paid-affiliate-room-transaction-checkbox" type="checkbox" value="" style="transform: scale(1.5)"></td>



								</tr>

							<?php	} ?>

						</table><br>

						<h3>Room Commission Total: $<?php echo $total_commission; ?></h3>

					<?php } ?>

					<br />
					<br />
					<br />
					<br />
					<h2>Total Approved Commission for <?php echo htmlspecialchars($yearMonth); ?>: $<?php echo $events_total_commission + $rooms_total_commission; ?></h2>

					<hr>

				<?php	} ?>



			</div>
		</div>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
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

		$(this.body).on('change', '.admin-paid-affiliate-transaction-checkbox', function(event) {

			const $this = $(this);
			// Check the checkbox's checked property
			var isChecked = $this.prop('checked') ? 1 : 0;

			//console.log($(this).parent().parent().find(".transaction_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/paid-affiliate-transaction",
				data: {
					is_paid: isChecked,
					transaction_id: $this.parent().parent().find(".transaction_id").text()
				},
				success: function(response) {
					// Handle success
					//console.log(response);

					updateAffiliatePaidTransactions($this);
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error);
				}
			});
		});

		/////////////////////////////////////////////////////////////////////////////#

		$(this.body).on('change', '.admin-approved-affiliate-room-transaction-checkbox', function(event) {
			// Check the checkbox's checked property
			var isChecked = $(this).prop('checked') ? 1 : 0;

			console.log($(this).parent().parent().find(".transaction_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/approve-affiliate-room-transaction",
				data: {
					admin_approved: isChecked,
					id: $(this).parent().parent().find(".transaction_id").text()
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

		$(this.body).on('change', '.admin-paid-affiliate-room-transaction-checkbox', function(event) {

			const $this = $(this);
			// Check the checkbox's checked property
			var isChecked = $this.prop('checked') ? 1 : 0;

			//console.log($this.parent().parent().find(".transaction_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/paid-affiliate-room-transaction",
				data: {
					is_paid: isChecked,
					id: $this.parent().parent().find(".transaction_id").text()
				},
				success: function(response) {
					// Handle success
					//console.log(response);
					updateAffiliatePaidTransactions($this);
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error);
				}
			});
		});

		/////////////////////////////////////////////////////////////////////////////#
		function updateAffiliatePaidTransactions($this) {
			const affiliateID = $this.parent().parent().find(".affiliate_id").text();
			//console.log(affiliateID);
			const transactionId = $this.parent().parent().find(".transaction_id").text();
			//console.log(transactionId);
			const purchaseType = $this.parent().parent().data("purchase-type");
			//console.log(purchaseType);
			const amount = parseFloat($this.parent().parent().find(".amount").text().replace('$', '')).toFixed(2);
			//console.log(amount);
			const commissionRate = parseFloat($this.parent().parent().find(".commission_rate").text().replace('%', '')).toFixed(2);
			//console.log(commissionRate);
			const commission = parseFloat($this.parent().parent().find(".commission").text().replace('$', '')).toFixed(2);
			//console.log(commission);
			const purchaseDate = $this.parent().parent().find(".purchase_date").text();
			//console.log(purchaseDate);
			const dateEnd = $this.parent().parent().find(".date_end").text();
			//console.log(dateEnd);

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/update-affiliate-paid-transactions",
				data: {
					affiliate_id: affiliateID,
					transaction_id: transactionId,
					purchase_type: purchaseType,
					amount: amount,
					commission_rate: commissionRate,
					commission: commission,
					purchase_date: purchaseDate,
					date_end: dateEnd,
				},
				success: function(response) {
					// Handle success
					//console.log(response);
					window.location.reload();
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error);
				}
			});
		}

	});
</script>

<?php include('includes/body-close.php'); ?>