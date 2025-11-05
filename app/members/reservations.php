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
 * @var Membership        $member
 */

$transactions = Database::Action("SELECT * FROM `member_reservations` WHERE `member_id` = :member_id", array(
        'member_id' => $member->getId(),
))->fetchAll(PDO::FETCH_ASSOC);

// Search Engine Optimization
$page_title       = "";
$page_description = "";

// Start Header
include('includes/header.php');
?>
<style>
    .text-muted {
    font-size: 0.9rem;
    }
</style>
<div class="container-fluid main-content">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="sr-only">Your Reservations</h1>
                <div class="song-announcement text-center my-4 p-4">
                    <div style="background: rgba(60, 60, 60, 1); border: 1px solid #ff66cc; border-radius: 10px; padding: 25px; margin: 30px auto; text-align: left; max-width: 700px;">
                        <h3 style="color: #00e6ff; margin-bottom: 10px;">🎟 Special Launch Offer</h3>
                        <ul style="list-style: none; padding-left: 0; margin: 0; font-size: 1rem; color:white;">
                            <li>• Sign up now and receive <b>2 FREE Tickets</b> at a wide variety of classes & events.</li>
                            <!-- <li>• Refer 2 friends → Get <b>2 BFF BOGO Classes</b> for only $4 each.</li>
                            <li><i>(No card required to register.)</i></li> -->
                        </ul>
                        <p style="margin-top: 20px; font-size: 1rem; color:white;">
                            <a href="https://enlighteningall.com/members/register" target="_self" style="color:white;">➡️ <b>Pre-register here:</b></a><br>
                            Seats, mats, and dates are assigned in the order registrations come in.<br>
                            <b>No payment required,</b> Supplies are limited!<br>
                            Offer valid until <b>Dec 31, 2025</b>.
                        </p>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($transactions as $transaction): ?>
                        <?php
                        $event = !empty($transaction['event_id']) ? Items\Event::Init($transaction['event_id']) : null;
                        $share_link = $event ? $event->getLink() : '#';
                        ?>
                        <div class="col">
                            <div class="card shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-center mb-2">
                                        <?php echo htmlentities($transaction['name_on_pass']); ?>
                                    </h5>

                                    <ul class="list-unstyled small mb-3">
                                        <?php if ($event): ?>
                                            <li><i class="fa-solid fa-calendar-users text-muted mr-2"></i>
                                                <strong>Event:</strong> <?php echo $event->getHeading(); ?>
                                            </li>
                                            <li><i class="fas fa-calendar-alt text-muted mr-2"></i>
                                                <strong>Date:</strong> <?php echo $event->getEventDates(); ?>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (!empty($event) && !empty($event->getEventTimes())): ?>
                                            <li><i class="fa-solid fa-clock text-muted mr-2"></i>
                                                <strong>Time:</strong> <?php echo $event->getEventTimes(); ?>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (!empty($transaction['event_date'])): ?>
                                            <li><i class="fas fa-clock text-muted mr-2"></i>
                                                <strong>Date:</strong> <?php echo date('F j, Y', strtotime($transaction['event_date'])); ?>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (!empty($transaction['total_amount'])): ?>
                                            <li><i class="fas fa-dollar-sign text-muted mr-2"></i>
                                                <strong>Paid:</strong> $<?php echo number_format($transaction['total_amount'], 2); ?>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (!empty($transaction['seat_selected'])): ?>
                                            <li><i class="fa-solid fa-chair text-muted mr-2"></i>
                                                <strong>Seat Reserved:</strong> <?php echo htmlentities($transaction['seat_selected']); ?>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (!empty($transaction['status'])): ?>
                                            <li><i class="fas fa-check-circle text-muted mr-2"></i>
                                                <strong>Status:</strong> <?php echo ucwords($transaction['status']); ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>

                                    <!-- ✅ Share link button -->
                                    <?php if ($event): ?>
                                        <div class="mt-auto mb-3 text-center">
                                            <p class="mb-3" style="font-weight: 600; color: #d4af37; font-size: 1.05rem; text-shadow: 0 0 4px rgba(212,175,55,0.5);">
                                                ✨ Copy and share this event link with your friends:
                                            </p>
                                            <button class="btn btn-outline-secondary w-100 copy-link-btn"
                                                    data-link="https://enlighteningall.com<?php echo htmlentities($share_link); ?>">
                                                <i class="fa-solid fa-link mr-2"></i> Copy &amp; Share Event Link
                                            </button>
                                        </div>
                                    <?php endif; ?>



                                    <?php if (is_null($member->getCheckIn())): ?>
                                        <a href="/members/check-in" class="btn btn-primary">Check-In</a>
                                    <?php else: ?>
                                        <a href="/members/check-out" class="btn btn-primary">Check-Out</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- ✅ Copy-to-Clipboard Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.copy-link-btn');
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                const link = this.getAttribute('data-link');
                if (!link || link === '#') return alert('No share link available.');
                navigator.clipboard.writeText(link).then(() => {
                    this.innerHTML = '<i class="fa-solid fa-check text-success mr-2"></i> Link Copied!';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fa-solid fa-link mr-2"></i> Copy & Share Event Link';
                    }, 2500);
                });
            });
        });
    });
</script>

<?php include('includes/body-close.php'); ?>
