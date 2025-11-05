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
$page_title = $member->getTitle('Banned Transactions');

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
				<?php

				///////////////////////////////////////////////////////////////////////////

				// Attempt to connect to the database and fetch transactions
				try {
					$eventTransactions = Database::Action("SELECT *, 'event' AS type FROM `affiliate_transactions` WHERE `admin_approved` = :admin_approved AND `is_paid` = :is_paid AND `is_banned` = :is_banned AND `affiliate_id` = :affiliate_id ORDER BY `timestamp`", [
						'admin_approved' => 0,
						'is_paid' => 0,
						'is_banned' => 1,
						'affiliate_id' => $member->getId()
					])->fetchAll(PDO::FETCH_ASSOC);

					$roomTransactions = Database::Action("SELECT *, 'room' AS type FROM `affiliate_room_transactions` WHERE `admin_approved` = :admin_approved AND `is_paid` = :is_paid AND `is_banned` = :is_banned  AND `affiliate_id` = :affiliate_id ORDER BY `timestamp`", [
						'admin_approved' => 0,
						'is_paid' => 0,
						'is_banned' => 1,
						'affiliate_id' => $member->getId()
					])->fetchAll(PDO::FETCH_ASSOC);
				} catch (PDOException $e) {
					echo 'Query failed: ' . $e->getMessage();
					exit;
				}

				$allTransactions = array_merge($eventTransactions, $roomTransactions);

				///////////////////////////////////////////////////////////////////////////

				// Group the transactions
				$groupedTransactions = groupTransactionsByMonth($allTransactions);

				?>

				<!---------------------------------------------------------------------------------------->

				<?php foreach (array_reverse($groupedTransactions, true) as $yearMonth => $types) { ?>

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
									<th>Transaction Amount</th>
									<th>Ticket Commission Rate</th>
									<th>Commission</th>
									<th>Purchase Event Link</th>
									<th>Purchaser Profile Link</th>
									<th>CC Aproved</th>
									<th>Un Ban</th>
									<th>Date/Time</th>
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
								<tr>

									<td><?php echo htmlspecialchars($transaction['affiliate_id']); ?></td>

									<td class="transaction_id"><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>

									<td>$<?php echo htmlspecialchars($transaction['amount']); ?></td>

									<td><?php echo htmlspecialchars($transaction['ticket_commission_rate']); ?>%</td>

									<td>$<?php echo $earned_commission; ?></td>

									<td><a target="_blank" href="/<?php echo htmlspecialchars($transaction['type_table_id']); ?>.event">Event Link<a></td>

									<td>
										<?php if (!empty($purchaser_profile_username[0])) { ?>
											<a target="_blank" href="/members/profile/<?php echo htmlspecialchars($purchaser_profile_username[0], ENT_QUOTES, 'UTF-8'); ?>">Profile Link</a>
										<?php } else { ?>
											N/A
										<?php } ?>
									</td>

									<td><?php echo htmlspecialchars($transaction['confirmed_payment']); ?></td>

									<td><input class="admin-banned-affiliate-event-transaction-checkbox" type="checkbox" style="transform: scale(1.5)" /></td>

									<td><?php echo htmlspecialchars($formattedTimestamp); ?></td>

								</tr>

							<?php    } ?>

						</table><br>



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
									<th>Purchaser Profile</th>
									<th>Booking Dates</th>
									<th>Room Name</th>
									<th>Un Ban</th>
									<th>Date/Time</th>
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

								$timestamp = new DateTime($transaction['timestamp']);
								$formattedTimestamp = $timestamp->format('M jS, Y, g:iA');

								?>
								<tr>

									<td><?php echo htmlspecialchars($transaction['affiliate_id']); ?></td>

									<td class="transaction_id"><?php echo htmlspecialchars($transaction['id']); ?></td>

									<td>$<?php echo htmlspecialchars($transaction['amount']); ?></td>

									<td><?php echo htmlspecialchars($transaction['ticket_commission_rate']); ?>%</td>

									<td>$<?php echo $earned_commission; ?></td>

									<td>
										<?php if (!empty($purchaser_profile_username[0])) { ?>
											<a target="_blank" href="/members/profile/<?php echo htmlspecialchars($purchaser_profile_username[0], ENT_QUOTES, 'UTF-8'); ?>">Profile Link</a>
										<?php } else { ?>
											N/A
										<?php } ?>
									</td>

									<td><?php echo htmlspecialchars($transaction['booking_dates']); ?></td>

									<td><?php echo htmlspecialchars($transaction['room_name']); ?></td>

									<td><input class="admin-banned-affiliate-room-transaction-checkbox" type="checkbox" style="transform: scale(1.5)" /></td>

									<td><?php echo htmlspecialchars($formattedTimestamp); ?></td>

								</tr>

							<?php    } ?>

						</table><br>



					<?php } ?>

					<br />

					<hr>

				<?php } ?>

			</div>

		</div>

	</section>

</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {



		$(this.body).on('change', '.admin-banned-affiliate-event-transaction-checkbox', function(event) {
			// Check the checkbox's checked property
			var isChecked = $(this).prop('checked') ? 0 : 1;

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
		////////////////////////////////////////////////////////////////////////

		$(this.body).on('change', '.admin-banned-affiliate-room-transaction-checkbox', function(event) {
			// Check the checkbox's checked property
			var isChecked = $(this).prop('checked') ? 0 : 1;

			console.log($(this).parent().parent().find(".transaction_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/banned-affiliate-room-transaction",
				data: {
					is_banned: isChecked,
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

		////////////////////////////////////////////////////////////////////////

	});
</script>

<?php include('includes/body-close.php'); ?>