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
	if(is_null($item)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
?>

<div id="ajax-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fal fa-calendar-alt" aria-hidden="true"></i>
				<h3 class="modal-title"><?php echo $item->getHeading(); ?></h3>
			</div>
			
			<div class="modal-body">
				<div id="availability-calendar" data-endpoint="/ajax/rooms/availability/<?php echo $item->getId(); ?>"></div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<a class="btn btn-success" href="/book-now">Book Now</a>
			</div>
		</div>
	</div>
</div>
