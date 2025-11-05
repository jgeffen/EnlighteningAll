<?php
	/*
	Copyright (c) 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var null|Membership   $member
	 */
	
	// Variable Defaults
	$item = Items\Room::Init($dispatcher->getId());
	
	// Check Item
	if(is_null($item) || empty($member)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
?>

<div id="ajax-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fal fa-calendar-alt" aria-hidden="true"></i>
				<h3 class="modal-title text-truncate"><?php echo $item->getHeading(); ?></h3>
			</div>
			
			<div class="modal-body p-0">
				<img class="img-fluid w-100 mb-0" src="<?php echo $item->getSquareImage(); ?>" alt="<?php echo $item->getImageAlt(); ?>">
			</div>
			
			<div class="modal-footer p-3">
				<div class="mx-auto">
					<a class="btn btn-outline" href="<?php echo $item->getReviewLink(); ?>">Write a Review</a>
					<a class="btn btn-outline" href="<?php echo $item->getReviewsLink(); ?>">View All Reviews</a>
				</div>
			</div>
		</div>
	</div>
</div>
