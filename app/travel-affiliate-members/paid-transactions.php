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
 * @var TravelAffiliateMembership $member
 */

// Search Engine Optimization
$page_title       = $member->getTitle('Dashboard');
$page_description = "";

// Start Header
include('includes/header.php');
?>

<style>
    .table tr:nth-child(even) {
        background-color: #F3F6F7;
    }
</style>

<div class="container-fluid main-content">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>Dashboard</h1>

                <?php AffiliateDashBoardTransactionSubMenu($member); ?>

                <h2>View Paid Transactions</h2>

                <hr>

                <?php

                ///////////////////////////////////////////////////////////////////////////

                // Attempt to connect to the database and fetch transactions
                try {
                    $eventTransactions = Database::Action("SELECT *, 'event' AS type FROM `affiliate_paid_transactions` WHERE `purchase_type` = :purchase_type AND `affiliate_id` = :affiliate_id ORDER BY `timestamp`", [
                        'purchase_type' => "event",
                        'affiliate_id' => $member->getId()
                    ])->fetchAll(PDO::FETCH_ASSOC);

                    $roomTransactions = Database::Action("SELECT *, 'room' AS type FROM `affiliate_paid_transactions` WHERE `purchase_type` = :purchase_type AND `affiliate_id` = :affiliate_id ORDER BY `timestamp`", [
                        'purchase_type' => "room",
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
                                    <th>Purchase Type</th>
                                    <th>Amount</th>
                                    <th>Commission Rate</th>
                                    <th>Commission</th>
                                    <th>Purchase Date/Time</th>
                                    <th>Event End Date</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>

                            <?php $total_commission = 0; ?>

                            <?php foreach ($types['events'] as $transaction) { ?>

                                <?php


                                $earned_commission = round(htmlspecialchars($transaction['amount']) * (htmlspecialchars($transaction['commission_rate']) / 100), 2);

                                $events_total_commission += $earned_commission;

                                $total_commission += $earned_commission;

                                $timestamp = new DateTime($transaction['timestamp']);

                                $formattedTimestamp = $timestamp->format('M jS, Y, g:iA');

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

                                    <td><?php echo htmlspecialchars($transaction['payment_method']); ?></td>

                                </tr>

                            <?php    } ?>

                        </table><br>

                        <h3>Paid Event/Ticket Commission Total: $<?php echo $total_commission; ?></h3>

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
                                    <th>Purchase Type</th>
                                    <th>Amount</th>
                                    <th>Commission Rate</th>
                                    <th>Commission</th>
                                    <th>Purchase Date/Time</th>
                                    <th>Event End Date</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>

                            <?php $total_commission = 0; ?>

                            <?php foreach ($types['rooms'] as $transaction) { ?>

                                <?php

                                $earned_commission = round(htmlspecialchars($transaction['amount']) * (htmlspecialchars($transaction['commission_rate']) / 100), 2);
                                $rooms_total_commission += $earned_commission;

                                $total_commission += $earned_commission;


                                $timestamp = new DateTime($transaction['timestamp']);
                                $formattedTimestamp = $timestamp->format('M jS, Y, g:iA');

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

                                    <td><?php echo htmlspecialchars($transaction['payment_method']); ?></td>

                                </tr>

                            <?php    } ?>

                        </table><br>

                        <h3>Paid Room Commission Total: $<?php echo $total_commission; ?></h3>

                    <?php } ?>

                    <br />

                    <h2>Total Paid Commission for <?php echo htmlspecialchars($yearMonth); ?>: $<?php echo $events_total_commission + $rooms_total_commission; ?></h2>

                    <hr>

                <?php    } ?>




            </div>

        </div>

    </div>

</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>