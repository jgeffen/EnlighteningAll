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
	
	// Imports
	use Items\Collections;
	use Items\Members\Ticket;
	
	// Set Items
	$items = new Collections\Tickets(Database::Action("SELECT `member_tickets`.* FROM (SELECT `id`, MAX(`timestamp`) AS `timestamp` FROM `member_tickets` GROUP BY COALESCE(`member_ticket_id`, `id`)) AS `tickets` JOIN `member_tickets` ON (`tickets`.`id` IN (`member_tickets`.`id`, `member_tickets`.`member_ticket_id`) AND `member_tickets`.`timestamp` = `tickets`.`timestamp`)"));
	
	// Sort Tickets
	$items->uasort(fn(Ticket $a, Ticket $b) => $a->isRead(TRUE) <=> $b->isRead(TRUE) ?: $b->getTimestamp() <=> $a->getTimestamp());
	
	// Set Title
	$page_title = 'View Member Reports: Tickets';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>
		
		<div class="dashboard-data-table table-responsive">
			<div class="resp-table-lg mb-4">
				<div class="row title-row">
					<div class="col-12 col-lg-1">
						<p id="ticket__id" class="text-nowrap">Ticket #</p>
					</div>
					
					<div class="col-12 col-lg-2">
						<p id="ticket__date">Date</p>
					</div>
					
					<div class="col-12 col-lg-8">
						<p id="ticket__content">Content</p>
					</div>
					
					<div class="col-12 col-lg-1">
						<p id="ticket__status">Status</p>
					</div>
				</div>
				
				<?php foreach($items as $item): ?>
					<div class="row btn-reveal-trigger align-itmes-center" data-ticket-id="<?php echo $item->getMemberTicketId() ?? $item->getId(); ?>">
						<div class="col-12 col-lg-1">
							<p data-tabletitle="ticket__id">
								<a href="/user/review/member-reports/tickets/<?php echo $item->getMemberTicketId() ?? $item->getId(); ?>">
									<?php echo $item->getMemberTicketId() ?? $item->getId(); ?>
								</a>
							</p>
						</div>
						
						<div class="col-12 col-lg-2">
							<p data-tabletitle="ticket__date">
								<b class="text-nowrap"><?php echo $item->getLastTimestamp()->format('Y-m-d H:i:s'); ?></b>
							</p>
						</div>
						
						<div class="col-12 col-lg-8">
							<p data-tabletitle="ticket__content" class="text-truncate">
								<?php echo $item->getContent(150); ?>
							</p>
						</div>
						
						<div class="col-12 col-lg-1">
							<p data-tabletitle="ticket__status">
								<?php if($item->isRead(TRUE)): ?>
									<span class="badge badge-pill badge-info">Read</span>
								<?php else: ?>
									<span class="badge badge-pill badge-danger">Unread</span>
								<?php endif; ?>
							</p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>

