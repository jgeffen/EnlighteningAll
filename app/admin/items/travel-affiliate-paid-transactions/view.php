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
$page_title = $member->getTitle('Paid Transactions');




//print_r($approved_transactions);

// Start Header
include('includes/header.php');



?>

<main class="page-content" data-member-id="<?php echo $member->getId() ?>">

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

				<?php

				$admin_commission_notes = $member->getAdminCommissionNote(); ?>

				<hr>


				<?php

				///////////////////////////////////////////////////////////////////////////

				// Attempt to connect to the database and fetch transactions
				try {
					$approvedEventTransactions = Database::Action("SELECT *, 'event' AS type FROM `affiliate_paid_transactions` WHERE `purchase_type` = :purchase_type  AND `affiliate_id` = :affiliate_id ORDER BY `timestamp`", [
						'purchase_type' => "event",
						'affiliate_id' => $member->getId()
					])->fetchAll(PDO::FETCH_ASSOC);

					$approvedRoomTransactions = Database::Action("SELECT *, 'room' AS type FROM `affiliate_paid_transactions` WHERE `purchase_type` = :purchase_type AND `affiliate_id` = :affiliate_id ORDER BY `timestamp`", [
						'purchase_type' => "room",
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


				<?php foreach ($groupedTransactions as $yearMonth => $types) { ?>

					<div>

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
							<table id="event_data_<?php echo htmlspecialchars(str_replace(' ', '_', $yearMonth)); ?>" class="table table-bordered nowrap w-100 dataTable no-footer dtr-inline">
								<thead>
									<tr>
										<th>Affiliate ID</th>
										<th>Transaction ID</th>
										<th>Purchase Type</th>
										<th>Amount</th>
										<th>Commission Rate</th>
										<th>Commission</th>
										<th>Purchase Date/Time</th>
										<th>Event End Date</th>
										<th>Payment Method</th>
										<th>Mark Unpaid</th>

									</tr>
								</thead>

								<?php $total_commission = 0; ?>

								<?php foreach ($types['events'] as $transaction) { ?>

									<?php


									$earned_commission = round(htmlspecialchars($transaction['amount']) * (htmlspecialchars($transaction['commission_rate']) / 100), 2);

									$events_total_commission += $earned_commission;

									$total_commission += $earned_commission;


									// $timestamp = new DateTime($transaction['timestamp']);

									// $formattedTimestamp = $timestamp->format('M jS, Y, g:iA');



									?>
									<tr>

										<td><?php echo htmlspecialchars($transaction['affiliate_id']); ?></td>

										<td class="transaction_id"><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>

										<td><?php echo htmlspecialchars($transaction['purchase_type']); ?></td>

										<td>$<?php echo htmlspecialchars($transaction['amount']); ?></td>

										<td><?php echo htmlspecialchars($transaction['commission_rate']); ?>%</td>

										<td class="commission">$<?php echo htmlspecialchars($transaction['commission']); ?></td>

										<td><?php echo htmlspecialchars($transaction['timestamp']); ?></td>

										<td><?php echo htmlspecialchars($transaction['date_end']); ?></td>



										<td>
											<select class="payment-method" name="payment_method" style="width:100%">
												<option value="" <?php echo ($transaction['payment_method'] == "") ? 'selected' : ''; ?>>choose</option>
												<option value="check" <?php echo ($transaction['payment_method'] == "check") ? 'selected' : ''; ?>>Check</option>
												<option value="paypal" <?php echo ($transaction['payment_method'] == "paypal") ? 'selected' : ''; ?>>PayPal</option>
												<option value="Zelle" <?php echo ($transaction['payment_method'] == "Zelle") ? 'selected' : ''; ?>>Zelle</option>
											</select>
										</td>

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

							<table id="room_data_<?php echo htmlspecialchars(str_replace(' ', '_', $yearMonth)); ?>" class="table table-bordered nowrap w-100 dataTable no-footer dtr-inline">
								<thead>
									<tr>

										<th>Affiliate ID</th>
										<th>Transaction ID</th>
										<th>Purchase Type</th>
										<th>Amount</th>
										<th>Commission Rate</th>
										<th>Commission</th>
										<th>Purchase Date/Time</th>
										<th>Room End Date</th>
										<th>Payment Method</th>
										<th>Mark Unpaid</th>
									</tr>
								</thead>

								<?php $total_commission = 0; ?>

								<?php foreach ($types['rooms'] as $transaction) { ?>

									<?php

									$earned_commission = round(htmlspecialchars($transaction['amount']) * (htmlspecialchars($transaction['commission_rate']) / 100), 2);
									$rooms_total_commission += $earned_commission;

									$total_commission += $earned_commission;

									// $timestamp = new DateTime($transaction['timestamp']);

									// $formattedTimestamp = $timestamp->format('M jS, Y, g:iA');



									?>
									<tr>

										<td><?php echo htmlspecialchars($transaction['affiliate_id']); ?></td>

										<td class="transaction_id"><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>

										<td><?php echo htmlspecialchars($transaction['purchase_type']); ?></td>

										<td>$<?php echo htmlspecialchars($transaction['amount']); ?></td>

										<td><?php echo htmlspecialchars($transaction['commission_rate']); ?>%</td>

										<td class="commission">$<?php echo htmlspecialchars($transaction['commission']); ?></td>

										<td><?php echo htmlspecialchars($transaction['timestamp']); ?></td>

										<td><?php echo htmlspecialchars($transaction['date_end']); ?></td>

										<td>
											<select class="payment-method" name="payment_method" style="width:100%">
												<option value="" <?php echo ($transaction['payment_method'] == "") ? 'selected' : ''; ?>>choose</option>
												<option value="check" <?php echo ($transaction['payment_method'] == "check") ? 'selected' : ''; ?>>Check</option>
												<option value="paypal" <?php echo ($transaction['payment_method'] == "paypal") ? 'selected' : ''; ?>>PayPal</option>
												<option value="Zelle" <?php echo ($transaction['payment_method'] == "Zelle") ? 'selected' : ''; ?>>Zelle</option>
											</select>
										</td>

										<td><input class="admin-paid-affiliate-room-transaction-checkbox" type="checkbox" value="" style="transform: scale(1.5)"></td>

									</tr>

								<?php	} ?>

							</table><br>

							<h3>Room Commission Total: $<?php echo $total_commission; ?></h3>

						<?php } ?>

						<br />

						<?php
						// Initialize notes to an empty string in case there are no notes for the month
						$notes = "";
						// Check if there are notes for the current $yearMonth
						if (isset($admin_commission_notes[$yearMonth]['Notes'])) {
							$notes = $admin_commission_notes[$yearMonth]['Notes'];
						}
						?>
						<div id="ajax-wrapper">

							<form id="admin_commission_notes_<?php echo htmlspecialchars(str_replace(' ', '_', $yearMonth)); ?>" class="form-horizontal content-module">

								<div class="form-group">
									<label for="notes">Admin Commission Notes</label>
									<textarea class="form-control disable-mce" placeholder="Admin Commission Notes" name="admin_commission_notes" rows="4"><?php echo htmlspecialchars($notes); ?></textarea>
								</div>

								<div class="form-btns text-right">
									<div class="float-lg-left">
										<button class="btn btn-success btn-block-md mb-2 admin-commission-notes-save">
											<i class="fal fa-save"></i> Save
										</button>
									</div>
								</div>

							</form>

						</div>

						<h3>Grand Commission Total: $<?php echo $events_total_commission + $rooms_total_commission; ?></h3>

						<br />

						<hr>
					</div>

				<?php	} ?>

			</div>

		</div>

	</section>

</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		/////////////////////////////////////////////////////////////////////////////

		$(this.body).on('change', '.payment-method', function(event) {

			const $this = $(this);

			if ($this.val() !== 'choose') {

				console.log($this.val());

				console.log($this.parent().parent().find(".transaction_id").text());

				$.ajax({
					type: "post",
					url: "/ajax/admin/items/travel-affiliate-members/update-payment-type",
					data: {
						payment_method: $this.val(),
						transaction_id: $this.parent().parent().find(".transaction_id").text()
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
		/////////////////////////////////////////////////////////////////////////////
		$(this.body).on('change', '.admin-paid-affiliate-transaction-checkbox', function(event) {
			// Check the checkbox's checked property
			const $this = $(this);

			var isChecked = $this.prop('checked') ? 0 : 1;

			//console.log($this.parent().parent().find(".transaction_id").text())

			$.ajax({
				type: "post",
				url: "/ajax/admin/items/travel-affiliate-members/paid-affiliate-transaction",
				data: {
					is_paid: isChecked,
					transaction_id: $(this).parent().parent().find(".transaction_id").text()
				},
				success: function(response) {
					// Handle success
					//console.log(response);
					$.ajax({
						type: "post",
						url: "/ajax/admin/items/travel-affiliate-members/delete-affiliate-paid-transaction",
						data: {
							transaction_id: $this.parent().parent().find(".transaction_id").text(),
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
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error);
				}
			});
		});
		/////////////////////////////////////////////////////////////////////////////
		$(this.body).on('change', '.admin-paid-affiliate-room-transaction-checkbox', function(event) {
			// Check the checkbox's checked property

			const $this = $(this);
			var isChecked = $this.prop('checked') ? 0 : 1;

			//console.log($(this).parent().parent().find(".transaction_id").text())

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
					$.ajax({
						type: "post",
						url: "/ajax/admin/items/travel-affiliate-members/delete-affiliate-paid-transaction",
						data: {
							transaction_id: $this.parent().parent().find(".transaction_id").text(),
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
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(error);
				}
			});
		});
		/////////////////////////////////////////////////////////////////////////////
		$(this.body).on('submit', '[id*="admin_commission_notes_"]', function(event) {

			event.preventDefault();

			var monthYear = $(this).attr("id").replace("admin_commission_notes_", "").replace(/_/g, " ");

			var transactions = {};

			$(this).parent().parent().find('[id*="event_data_"]').find(".transaction_id").each(function(index) {
				var transactionId = $(this).text();

				var earnedEventsCommission = parseFloat($(this).siblings('.commission').text().trim().replace('$', ''));

				transactions[transactionId] = earnedEventsCommission;
			});

			$(this).parent().parent().find('[id*="room_data_"]').find(".transaction_id").each(function(index) {
				var transactionId = $(this).text();

				var earnedRoomsCommission = parseFloat($(this).siblings('.commission').text().trim().replace('$', ''));

				transactions[transactionId] = earnedRoomsCommission;
			});

			var totalPaid = Object.values(transactions).reduce((a, b) => a + b, 0);

			var notes = $(this).serializeArray()[0].value;

			var dataToSend = {
				[monthYear]: {
					"Transactions": transactions,
					"Total Paid": totalPaid,
					"Notes": notes
				}
			};

			//console.log(JSON.stringify(dataToSend))

			const memberId = $(document).find('[data-member-id]').data('member-id');

			$.ajax({

				method: "post",
				dataType: "json",
				contentType: "application/json; charset=utf-8",

				async: true,
				url: "/ajax/admin/items/travel-affiliate-members/update-admin-commission-notes/" + memberId,
				data: JSON.stringify(dataToSend),

				success: function(response) {
					console.log(response);
					window.location.reload();
				},
				error: function(xhr, status, error) {
					console.error(error);
				}
			});


		});

	});
</script>

<?php include('includes/body-close.php'); ?>