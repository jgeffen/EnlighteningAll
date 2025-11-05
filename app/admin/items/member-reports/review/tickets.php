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
	use Items\Enums\Sizes;
	use Items\Members;
	
	// Variable Defaults
	$page_title = 'Review Member Reports: Tickets';
	
	// Set Item
	$item = Members\Ticket::Init($dispatcher->getTableId());
	
	// Check Item
	if(is_null($item)) Admin\Render::ErrorDocument(HttpStatusCode::NOT_FOUND);
	
	// Check if Thread
	if(!is_null($item->getMemberTicketId())) {
		if(!is_null($item->getMemberTicket())) {
			Helpers::Redirect(sprintf("/user/review/member-reports/tickets/%d", $item->getMemberTicket()->getId()));
		}
		
		Render::ErrorDocument(HttpStatusCode::NOT_FOUND);
	}
	
	// Set Member
	$member = $item->getMember();
	
	// Start Header
	include('includes/header.php');
?>

<main id="ticket-wrapper" class="page-content" data-ticket="<?php echo $item->toJson(JSON_HEX_QUOT); ?>">
	<div id="page-title-btn">
		<h1><?php echo sprintf("Ticket #%s", $item->getId()); ?></h1>
	</div>
	
	<?php if(!is_null($member)): ?>
		<div class="media mb-3">
			<img class="align-self-center mr-3 img-thumbnail" src="<?php echo $member->getAvatar()?->getImage(Sizes\Avatar::XS, TRUE) ?? Items\Defaults::AVATAR_XS; ?>">
			
			<div class="media-body">
				<h5><?php echo $member->getUsername(); ?></h5>
				<ul class="card-text pl-3">
					<li>Name(s): <?php echo $member->getFirstNames(); ?></li>
					<li>
						Email:
						<a href="mailto:<?php echo $member->getEmail(); ?>">
							<?php echo $member->getEmail(); ?>
						</a>
					</li>
					<li>
						Profile:
						<a href="<?php echo $member->getLink(); ?>" target="_blank">
							<?php echo $member->getLink(TRUE); ?>
						</a>
					</li>
				</ul>
			</div>
		</div>
	<?php endif; ?>
	
	<div class="card">
		<div class="row g-0">
			<div class="col-12">
				<div class="position-relative">
					<div class="chat-messages p-4">
						<?php foreach($item->getThread() as $message): ?>
							<?php if($message->getInitiatedBy() == 'member'): ?>
								<div class="chat-message-left pb-4">
									<div class="flex-shrink-1 bg-light rounded py-2 px-3">
										<div class="font-weight-bold mb-1">
											<?php echo sprintf("%s (@%s)", $item->getMember()?->getFullName(), $item->getMember()?->getUsername()); ?>
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
											<?php echo Admin\User::Init($message->getAuthor())?->getFullName(); ?>
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
					<textarea id="ticket-pane-textarea" class="form-control disable-mce" name="message" placeholder="Type your message..." aria-label="Type your message..."></textarea>
				</div>
			</div>
		</div>
	</div>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var ticket = $('#ticket-wrapper').data('ticket');

		// Poll TinyMCE
		(function checkTinymce() {
			if(typeof tinymce !== 'undefined') {
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
					protect: [ /<div class="clear"><\/div>/g ],
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
									$.ajax({
										data: { ticket_id: ticket.id, message: tinymce.activeEditor.getContent({ format: 'html' }) },
										dataType: 'json',
										method: 'post',
										async: false,
										success: function(response) {
											// Switch Status
											switch(response.status) {
												case 'success':
													// Reload Page
													location.reload();
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
			} else setTimeout(checkTinymce, 100);
		})();
	});
</script>

<?php include('includes/body-close.php'); ?>

