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
	 * @var null|Ticket       $ticket
	 */
	
	// Imports
	use Items\Members\Ticket;
	
	// Check Ticket
	if(is_null($ticket)) Render::ErrorDocument(HttpStatusCode::NOT_FOUND);
	
	// Check Ownership
	if($ticket->getMemberId() != $member->getId()) Render::ErrorDocument(HttpStatusCode::UNAUTHORIZED);
	
	// Check if Thread
	if(!is_null($ticket->getMemberTicketId())) {
		if(!is_null($ticket->getMemberTicket())) {
			Helpers::Redirect($ticket->getMemberTicket()->getLink());
		}
		
		Render::ErrorDocument(HttpStatusCode::NOT_FOUND);
	}
	
	// Mark As Read
	$ticket->markRead();
	
	// Search Engine Optimization
	$page_title       = sprintf("Ticket #%s", $ticket->getId());
	$page_description = "";
	
	// Start Header
	include('includes/header.php');
?>

<div id="ticket-wrapper" class="container-fluid main-content" data-ticket="<?php echo $ticket->toJson(JSON_HEX_QUOT); ?>">
	<div class="container">
		<h1><?php echo $page_title; ?></h1>
		
		<div class="card">
			<div class="row g-0">
				<div class="col-12">
					<div class="position-relative">
						<div id="ticket-pane" class="chat-messages p-4" data-ticket-id="<?php echo $ticket->getId(); ?>">
							<?php foreach($ticket->getThread() as $message): ?>
								<?php if($message->getInitiatedBy() == 'member'): ?>
									<div class="chat-message-left pb-4">
										<div class="flex-shrink-1 bg-light rounded py-2 px-3">
											<div class="font-weight-bold mb-1">
												You
												<small class="text-muted">
													<?php echo $message->getTimestamp()->format('Y-m-d H:i:s'); ?>
												</small>
											</div>
											
											<p><?php echo $message->getContent(); ?></p>
										</div>
									</div>
								<?php else: ?>
									<div class="chat-message-left pb-4">
										<div class="flex-shrink-1 bg-light rounded py-2 px-3">
											<div class="font-weight-bold mb-1">
												Member Support
												<small class="text-muted">
													<?php echo $message->getTimestamp()->format('Y-m-d H:i:s'); ?>
												</small>
											</div>
											
											<p><?php echo $message->getContent(); ?></p>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
					
					<div class="flex-grow-0 py-3 px-4 border-top" data-tinymce="align-toolbar-last-right no-border">
						<textarea id="ticket-pane-textarea" class="form-control" name="message" placeholder="Type your message..." aria-label="Type your message..."></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var ticket = $('#ticket-wrapper').data('ticket');
		
		// Init TinyMCE
		tinymce.init({
			selector: '#ticket-pane-textarea',
			theme: 'silver',
			cache_suffix: '?v=6.1.2',
			base_url: '/library/packages/tinymce',
			browser_spellcheck: true,
			document_base_url: '/',
			element_format: 'html',
			forced_root_block: '',
			newline_behavior: 'linebreak',
			formats: {
				bold: { inline: 'strong' },
				italic: { inline: 'em' },
				underline: { inline: 'u' }
			},
			height: 150,
			keep_styles: false,
			menubar: false,
			mobile: { toolbar_mode: 'scrolling' },
			plugins: 'emoticons',
			protect: [/<div class="clear"><\/div>/g],
			relative_urls: false,
			toolbar: 'bold italic underline emoticons | customSubmitButton',
			valid_elements: 'br,strong/b,em/i,u',
			verify_html: true,
			toolbar_location: 'bottom',
			setup: function(editor) {
				// Custom Send Button
				editor.ui.registry.addIcon('fa-paper-plane', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"><path d="M511.6 36.86l-64 415.1c-1.5 9.734-7.375 18.22-15.97 23.05c-4.844 2.719-10.27 4.097-15.68 4.097c-4.188 0-8.319-.8154-12.29-2.472l-122.6-51.1l-50.86 76.29C226.3 508.5 219.8 512 212.8 512C201.3 512 192 502.7 192 491.2v-96.18c0-7.115 2.372-14.03 6.742-19.64L416 96l-293.7 264.3L19.69 317.5C8.438 312.8 .8125 302.2 .0625 289.1s5.469-23.72 16.06-29.77l448-255.1c10.69-6.109 23.88-5.547 34 1.406S513.5 24.72 511.6 36.86z"/></svg>');
				editor.ui.registry.addButton('customSubmitButton', {
					icon: 'fa-paper-plane',
					tooltip: 'Send Message',
					onAction: function() {
						// Check Message
						if(Boolean(tinymce.activeEditor.getContent({ format: 'text' }).trim())) {
							// Handle Ajax
							$.ajax('/ajax/members/ticket', {
								data: { ticket_id: ticket.id, message: tinymce.activeEditor.getContent({ format: 'html' }) },
								dataType: 'json',
								method: 'post',
								async: false,
								success: function(response) {
									// Switch Status
									switch(response.status) {
										case 'success':
											// Clear Content
											tinymce.activeEditor.setContent('');
											break;
										case 'error':
										default:
											displayMessage(response.message || 'Something went wrong.', 'alert');
									}
								}
							});
						}
					}
				});
			},
			statusbar: false,
			init_instance_callback: function() {
				// Update Sticky
				if(window.hasOwnProperty('stickyMessagePane')) {
					window.stickyMessagePane.updateSticky();
				}
			}
		});
		
		// Add Ticket to Poll Object for Updating
		window.pollObject = $.extend({}, {
			ticket_id: parseInt(ticket.id)
		}, window.hasOwnProperty('pollObject') ? window.pollObject : {});
		
		// Update Polling
		if(!navigator.sendBeacon || navigator.sendBeacon('/ajax/members/server-poll/update')) {
			$.ajax('/ajax/members/server-poll/update', { method: 'post', async: false });
		}
	});
</script>

<?php include('includes/body-close.php'); ?>
