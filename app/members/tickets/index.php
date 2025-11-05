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
	 * @var Membership        $member
	 */
	
	// Imports
	use Items\Members\Ticket;
	
	// Search Engine Optimization
	$page_title       = $member->getTitle('Tickets');
	$page_description = "";
	
	// Sort Tickets
	$member->tickets()->uasort(fn(Ticket $a, Ticket $b) => $a->isRead() <=> $b->isRead() ?: $b->getTimestamp() <=> $a->getTimestamp());
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<?php if(!$member->tickets()->empty()): ?>
					<h1>
						Tickets
						
						<button class="btn btn-primary btn-sm float-right" data-ticket-action="new">
							<i class="fa fa-plus"></i> New Ticket
						</button>
					</h1>
					
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
							
							<?php foreach($member->tickets() as $ticket): ?>
								<div class="row btn-reveal-trigger align-itmes-center" data-ticket-id="<?php echo $ticket->getMemberTicketId() ?? $ticket->getId(); ?>">
									<div class="col-12 col-lg-1">
										<p data-tabletitle="ticket__id">
											<a href="<?php echo $ticket->getLink(); ?>">
												<?php echo $ticket->getMemberTicketId() ?? $ticket->getId(); ?>
											</a>
										</p>
									</div>
									
									<div class="col-12 col-lg-2">
										<p data-tabletitle="ticket__date">
											<b class="text-nowrap"><?php echo $ticket->getLastTimestamp()->format('Y-m-d H:i:s'); ?></b>
										</p>
									</div>
									
									<div class="col-12 col-lg-8">
										<p data-tabletitle="ticket__content" class="text-truncate">
											<?php echo $ticket->getContent(150); ?>
										</p>
									</div>
									
									<div class="col-12 col-lg-1">
										<p data-tabletitle="ticket__status">
											<?php if($ticket->isRead()): ?>
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
				<?php else: ?>
					<div class="row justify-content-center">
						<div class="col-sm-10 col-md-7 col-lg-6 col-xl-5">
							<h3 class="text-center mb-0">You do not have any tickets</h3>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Delegate Click Event to Ticket Actions
		$(this.body).on('click', '[data-ticket-action]', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Variable Defaults
			var button  = $(this);
			var dataset = button.data();
			var action  = dataset.ticketAction;
			
			// Switch Action
			switch(action) {
				case 'new':
					$.ajax('/modals/members/ticket', {
						dataType: 'html',
						method: 'get',
						async: false,
						beforeSend: showLoader,
						success: function(html) {
							// Show Modal
							$(html).on('shown.bs.modal', function() {
								// Variable Defaults
								var modal = $(this);
								
								// Init TinyMCE
								tinymce.init({
									target: modal.find('textarea')[0],
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
									height: 362,
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
										editor.ui.registry.addIcon('fa-paper-plane', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"><path d="M511.6 36.86l-64 415.1c-1.5 9.734-7.375 18.22-15.97 23.05c-4.844 2.719-10.27 4.097-15.68 4.097c-4.188 0-8.319-.8154-12.29-2.472l-122.6-51.1l-50.86 76.29C226.3 508.5 219.8 512 212.8 512C201.3 512 192 502.7 192 491.2v-96.18c0-7.115 2.372-14.03 6.742-19.64L416 96l-293.7 264.3L19.69 317.5C8.438 312.8 .8125 302.2 .0625 289.1s5.469-23.72 16.06-29.77l448-255.1c10.69-6.109 23.88-5.547 34 1.406S513.5 24.72 511.6 36.86z"/></svg>');
										editor.ui.registry.addButton('customSubmitButton', {
											icon: 'fa-paper-plane',
											tooltip: 'Submit Ticket',
											onAction: function() {
												// Check Message
												if(Boolean(tinymce.activeEditor.getContent({ format: 'text' }).trim())) {
													// Handle Ajax
													$.ajax('/ajax/members/ticket', {
														data: { ticket_id: null, message: tinymce.activeEditor.getContent({ format: 'html' }) },
														dataType: 'json',
														method: 'post',
														async: false,
														success: function(response) {
															// Switch Status
															switch(response.status) {
																case 'success':
																	// Close Modal
																	modal.on('hidden.bs.modal', function() {
																		displayMessage(response.message, 'success', function() {
																			$(this).on('hide.bs.modal', function() {
																				location.reload();
																			});
																		});
																	}).modal('hide');
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
									init_instance_callback: hideLoader
								});
							}).on('hidden.bs.modal', function() {
								tinymce.activeEditor.destroy();
								$(this).remove();
							}).modal();
						}
					});
					break;
				
				default:
					console.log('Unknown Ticket Action: ' + action);
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>
