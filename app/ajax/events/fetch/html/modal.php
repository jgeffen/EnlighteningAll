<?php
	/**
	 * @var array $event
	 */
	// This isnt the modal used for the calendar, I know....I fell for it too. The real one is in /Library/src/templates/events/calendar/modal.twig -DL
	/* Turn On Output Buffering */
	ob_start();
?>

<div class="modal fade" id="ajax-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fal fa-calendar-alt" aria-hidden="true"></i>
				<h3 class="modal-title"><?php echo $event['heading']; ?></h3>
			</div>
			
			<div class="modal-body">
				<div class="trim mt-0 mb-4 pt-4 pb-2">
					<div class="row">
						<div class="col-md-6">
							<p>
								<i class="fal fa-calendar-alt"></i>
								<b>Date:</b> <?php echo $event['date']; ?>
							</p>
						</div>
						
						<?php if(!empty($event['event_times'])): ?>
							<div class="col-md-6">
								<p>
									<i class="fa-solid fa-clock"></i>
									<b>Time:</b> <?php echo $event['event_times']; ?>
								</p>
							</div>
						<?php endif; ?>
						
						<?php if(!empty($event['location'])): ?>
							<div class="col-md-6">
								<p>
									<i class="fal fa-map-marked"></i>
									<b>Location:</b> <?php echo $event['location']; ?>
								</p>
							</div>
						<?php endif; ?>
						
						<?php if(!empty($event['price_text'])): ?>
							<div class="col-md-6">
								<p>
									<i class="fal fa-tags"></i>
									<b>Price:</b> <?php echo $event['price_text']; ?>
								</p>
							</div>
						<?php endif; ?>
					</div>
				</div>
				
				<p><?php echo $event['text']; ?></p>
			
			</div>
			
			<div class="modal-footer">
				<a class="btn btn-primary" href="<?php echo $event['link']; ?>" title="<?php echo $event['alt']; ?>">
					View Event Details
				</a>
				
				<button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php
	/* Return Current Buffer Contents and Delete Current Output Buffer */
	return ob_get_clean();
?>

