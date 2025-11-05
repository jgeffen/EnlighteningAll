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

// Imports
use Items\Enums\Sizes;

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
				<h1 class="sr-only">Check-In</h1>
				<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 justify-content-center">
					<div class="col">
						<div class="card">

							<div class="card-body pt-3">
								<h2 class="card-title text-center mb-3"><?php echo $member->getFullName(); ?></h2>

								<?php if (!$member->isApproved()) : ?>
									Member is Approved
								<?php else : ?>
									<p>Your account is already approved.</p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>