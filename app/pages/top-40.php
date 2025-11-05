<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var null|Membership   $member
	 */
	
	// Redirect
	if(filter_input(INPUT_GET, 'dev') != JSON_THROW_ON_ERROR) {
		Helpers::Redirect('/book-now');
	}
	
	// Set Items
	$items = Items\Room::FetchAll(Database::Action("SELECT `rooms`.* FROM `rooms` LEFT JOIN `member_rooms` ON `member_rooms`.`room_id` = `rooms`.`id` WHERE `rooms`.`published` IS TRUE GROUP BY `rooms`.`id` ORDER BY SUM(`member_rooms`.`favorite`) DESC, `position` DESC LIMIT 40"));
	
	// Search Engine Optimization
	$page_title       = "Top 40 Rooms";
	$page_description = "Top 40 Rooms";
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1 class="title-underlined mb-5">Top 40 Rooms</h1>
				
				<?php if(!empty($items)): ?>
					<div class="rooms row">
						<?php foreach($items as $item): ?>
							<div id="<?php echo sprintf("room-%d", $item->getId()); ?>" class="rooms__child col-lg-6" data-room="<?php echo $item->toJson(JSON_HEX_QUOT); ?>">
								<div class="title-bar-trim-combo">
									<div class="title-bar equal-title">
										<h2><?php echo $item->getHeading(); ?></h2>
									</div>
									
									<div class="trim">
										<?php
											Render::Component('sliders/room-carousel/room-carousel', array(
												'item'           => $item,
												'margin'         => 'my-0 mx-auto',
												'inset'          => FALSE,
												'inset_position' => ''
											));
										?>
										
										<?php if(!is_null($member)): ?>
											<hr class="mt-3 mb-1">
											
											<?php
											Render::Template('members/rooms/toolbar.twig', array(
												'id'    => $item->getId(),
												'flags' => array(
													'favorite'     => in_array($item->getId(), $member->getRoomFavoriteIds()),
													'notification' => in_array($item->getId(), $member->getRoomNotificationIds()),
													'review'       => in_array($item->getId(), $member->getRoomReviewIds())
												)
											));
											?>
										<?php endif; ?>
										
										<hr class="mt-1 mb-4">
										
										<h4><b>Description:</b></h4>
										
										<div class="read-more">
											<div id="<?php echo sprintf("room-read-more-%d", $item->getId()); ?>">
												<?php echo $item->getContent(); ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<p>There are no rooms at the moment, please check back later.</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var mainCSS = $('link[href^="/css/styles-main.min.css"]');
		
		// Init Scripts
		$.when(
			// Load Styles
			$('<link>').attr({ type: 'text/css', rel: 'stylesheet', href: '/library/packages/fullcalendar/main.min.css' }).insertBefore(mainCSS),
			$('<link>').attr({ type: 'text/css', rel: 'stylesheet', href: '/components/calendars/fullcalendar/css/fullcalendar.css' }).insertBefore(mainCSS),
			
			// Load Scripts
			$.ajax('/library/packages/fullcalendar/main.min.js', { async: false, dataType: 'script' }),
			$.Deferred(function(deferred) {
				$(deferred.resolve);
			})
		).then(function() {
			// Check Read More on Rooms
			$('.rooms').find('.rooms__child').each(function(index, element) {
				// Variable Defaults
				var room    = $(element);
				var section = room.find('.read-more');
				var content = section.find('div[id^="room-read-more"]');
				var target  = '#' + content.attr('id');
				
				// Check Height
				if(content.height() > 150) {
					content.addClass('collapse read-more__content').attr('aria-expanded', false).after($('<a/>', {
						'class': 'collapsed read-more__btn',
						'aria-expanded': false,
						'data-target': target,
						'data-toggle': 'collapse',
						'role': 'button'
					}).collapse());
				}
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

