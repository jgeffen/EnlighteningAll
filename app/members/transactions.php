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
	
	$transactions = Database::Action("SELECT * FROM `transactions` WHERE `member_id` = :member_id ORDER BY `timestamp` DESC", array(
		'member_id' => $member->getId(),
	))->fetchAll(PDO::FETCH_ASSOC);
	
	// Search Engine Optimization
	$page_title       = "";
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="sr-only">Transactions</h1>
				
				<?php if($member->wallet()->getPoints() > 0): ?>
					<h2><b>My Points Total:</b> <?php echo $member->wallet()->getPoints(); ?></h2>
				<?php endif; ?>
				<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
					<?php foreach($transactions as $transaction): ?>
						<div class="col">
							<div class="card shadow-sm h-100">
								<div class="card-body d-flex flex-column">
									<h5 class="card-title text-center mb-2"><?php echo strtoupper($transaction['table_name']); ?></h5>
									
									<ul class="list-unstyled small mb-3">
										<?php if(!empty($transaction['payment_status'])): ?>
											<li><strong>Status:</strong> <?php echo $transaction['payment_status']; ?></li>
										<?php endif; ?>
										
										<?php if(!empty($transaction['product_label'])): ?>
											<li><strong>Product Label:</strong> <?php echo $transaction['product_label']; ?></li>
										<?php endif; ?>
										
										<?php if(!empty($transaction['product'])): ?>
											<li><strong>Product:</strong> <?php echo $transaction['product']; ?></li>
										<?php endif; ?>
										
										<?php if(!empty($transaction['product_quantity'])): ?>
											<li><strong>Quantity:</strong> <?php echo $transaction['product_quantity']; ?></li>
										<?php endif; ?>
										<li><strong>Amount:</strong> <?php echo Helpers::FormatCurrency((float)$transaction['amount']); ?></li>
										
										<?php if(!empty($transaction['total_amount'])): ?>
											<li><strong>Paid:</strong> $<?php echo number_format($transaction['total_amount'], 2); ?></li>
										<?php endif; ?>
										
										<?php if(!empty($transaction['invoice'])): ?>
											<li><strong>Invoice:</strong> <?php echo $transaction['invoice']; ?></li>
										<?php endif; ?>
										
										<?php if(!empty($transaction['timestamp'])): ?>
											<li><strong>Timestamp:</strong> <?php echo ucwords($transaction['timestamp']); ?></li>
										<?php endif; ?>
									</ul>
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

<?php include('includes/body-close.php'); ?>
