document.addEventListener('DOMContentLoaded', function() {
	$.when(
		// Load Scripts
		typeof tinymce !== 'object' &&
		$.getScript('/library/packages/tinymce/tinymce.min.js'),
		$.Deferred(function(deferred) {
			$(deferred.resolve);
		})
	).done(function() {
		// Set Body
		this.body = $('body');

		// Update Polling
		var updatePolling;
		(updatePolling = function() {
			// Handle Request
			if(
				!navigator.sendBeacon ||
				navigator.sendBeacon('/ajax/members/server-poll/update')
			) {
				$.ajax('/ajax/members/server-poll/update', {
					method: 'post',
					async: false
				});
			}
			/////////////////////////////////////////////////////////
			// Trevor Added this here for now
			/////////////////////////////////////////////////////////
			if(
				!navigator.sendBeacon ||
				navigator.sendBeacon(
					'/ajax/travel-affiliate-members/server-poll/update'
				)
			) {
				//console.log("Test navigator");
				$.ajax('/ajax/travel-affiliate-members/server-poll/update', {
					method: 'post',
					async: false
				});
			}
			/////////////////////////////////////////////////////////
			//End: Trevor Added this here for now
			/////////////////////////////////////////////////////////
		})();

		(function membersPollServer() {
			// Variable Defaults
			var retry = true;

			// Reset Object
			window.pollObject = $.extend(
				{},
				{
					timestamp: 0
				},
				window.hasOwnProperty('pollObject') ? window.pollObject : {}
			);

			// Handle Ajax
			$.ajax('/ajax/members/server-poll', {
				data: window.pollObject,
				dataType: 'json',
				method: 'post',
				async: true,
				success: function(response) {
					/**
					 *
					 * @param {Object}  response
					 * @param {String}  response.status
					 * @param {Boolean} response.debug
					 * @param {Object}  [response.data]
					 * @param {String}  [response.data.avatar]
					 * @param {Object}  [response.data.member]
					 * @param {Number}  [response.data.member.id]
					 * @param {String}  [response.data.member.first_name]
					 * @param {String}  [response.data.member.last_name]
					 * @param {String}  [response.data.member.email]
					 * @param {Array}   [response.data.likes]
					 * @param {Number}  [response.data.messages]
					 * @param {Number}  [response.data.notifications]
					 * @param {Array}   [response.data.reported]
					 * @param {Number}  [response.data.timestamp]
					 * @param {String}  [response.message]
					 * @param {Number}  [response.message-pane]
					 * @param {Object}  [response.post]
					 * @param {Number}  [response.post.id]
					 * @param {String}  [response.post.like]
					 * @param {String}  [response.post.comment-toggle]
					 * @param {String}  [response.post.comments]
					 *
					 */

					// Switch Status
					switch(response.status) {
						case 'success':
							// Check Debug
							if(response.debug)
								console.debug('Server Poll:', response.message);

							// Variable Defaults
							var avatarImages            = $('img.avatar');
							var notificationPlaceholder = $('.notification-count');
							var messagesPlaceholder     = $('.messages-count');
							var ticketsPlaceholder      = $('.tickets-count');
							var totalPlaceholder        = $('.total-count');
							var totalCount              =
									response.data.notifications +
									response.data.messages +
									response.data.tickets;
							var contactList             = $('#contact-list');
							var messagePane             = $('#messages-container');
							var ticketPane              = $(
								'#ticket-pane[data-ticket-id="' + response.ticket + '"]'
							);

							// Update Timestamp
							window.pollObject.timestamp = response.data.timestamp;

							// Update Avatar(s)
							if(response.data.avatar) {
								//console.log("Test avatarImages " + avatarImages);
								avatarImages.prop('src', response.data.avatar);
							}

							// Hide/Show Notifications
							response.data.notifications
								? notificationPlaceholder.text(response.data.notifications)
								: notificationPlaceholder.empty();

							// Hide/Show Messages
							response.data.messages
								? messagesPlaceholder.text(response.data.messages)
								: messagesPlaceholder.empty();

							// Hide/Show Tickets
							response.data.tickets
								? ticketsPlaceholder.text(response.data.tickets)
								: ticketsPlaceholder.empty();

							// Hide/Show Total
							totalCount
								? totalPlaceholder.text(totalCount)
								: totalPlaceholder.empty();

							// Update Post
							if(response.post.hasOwnProperty('id')) {
								// Replace Comments & Button(s)
								$('div[data-post-id="' + response.post.id + '"]')
									.find('button.toolbar__btn[data-post-action="like"]')
									.replaceWith(response.post.like)
									.end()
									.find(
										'button.toolbar__btn[data-post-action="comment-toggle"]'
									)
									.replaceWith(response.post['comment-toggle'])
									.end()
									.find('ul.list-unstyled')
									.empty()
									.append(response.post.comments.join('\n'));
							}

							// Update Contact List
							contactList.length &&
							contactList.find('.list-group').fetchContactList();

							// Load Message Pane
							messagePane.find(
								'div[data-member-id="' + response['message-pane'] + '"]'
							).length &&
							messagePane.loadMessagePane(response['message-pane'], true);

							// Check Ticket Pane
							if(ticketPane.length) {
								// Reload Ticket
								ticketPane.load(
									location.href + ' #' + ticketPane.prop('id') + '>*'
								);
							}
							break;
						case 'retry':
							// Check Debug
							if(response.debug)
								console.debug('Server Poll:', response.message);

							// Update Timestamp
							window.pollObject.timestamp = response.data.timestamp;
							break;
						case 'timeout':
							// Check Debug
							if(response.debug)
								console.debug('Server Poll:', response.message);
							break;
						case 'info':
							// Stop Retry
							retry = false;
							if(response.debug)
								console.info('Server Poll:', response.message);
							break;
						case 'error':
						default:
							// Stop Retry
							retry = false;
							console.error('Server Poll:', response.message);
					}

					// Set Retry
					retry && membersPollServer();
				}
			});
		})();

		(function affiliateMembersPollServer() {
			// Variable Defaults
			var retry = true;

			// Reset Object
			window.pollObject = $.extend(
				{},
				{
					timestamp: 0
				},
				window.hasOwnProperty('pollObject') ? window.pollObject : {}
			);

			/////////////////////////////////////////////////////////
			// Trevor Added this here for now
			/////////////////////////////////////////////////////////
			$.ajax('/ajax/travel-affiliate-members/server-poll', {
				data: window.pollObject,
				dataType: 'json',
				method: 'post',
				async: true,
				success: function(response) {
					/**
					 *
					 * @param {Object}  response
					 * @param {String}  response.status
					 * @param {Boolean} response.debug
					 * @param {Object}  [response.data]
					 * @param {Object}  [response.data.member]
					 * @param {Number}  [response.data.member.id]
					 * @param {String}  [response.data.member.first_name]
					 * @param {String}  [response.data.member.last_name]
					 * @param {String}  [response.data.member.email]
					 * @param {Array}   [response.data.reported]
					 * @param {Number}  [response.data.timestamp]
					 *
					 */

					// Switch Status
					switch(response.status) {
						case 'success':
							// Check Debug
							if(response.debug)
								console.debug('Server Poll:', response.message);

							// Variable Defaults

							// Update Timestamp
							window.pollObject.timestamp = response.data.timestamp;

							break;
						case 'retry':
							// Check Debug
							if(response.debug)
								console.debug('Server Poll:', response.message);

							// Update Timestamp
							window.pollObject.timestamp = response.data.timestamp;
							break;
						case 'timeout':
							// Check Debug
							if(response.debug)
								console.debug('Server Poll:', response.message);
							break;
						case 'info':
							// Stop Retry
							retry = false;
							if(response.debug)
								console.info('Server Poll:', response.message);
							break;
						case 'error':
						default:
							// Stop Retry
							retry = false;
							console.error('Server Poll:', response.message);
					}

					// Set Retry
					retry && affiliateMembersPollServer();
				}
			});
		})();
		/////////////////////////////////////////////////////////
		// End: Trevor Added this here for now
		/////////////////////////////////////////////////////////

		// Extend jQuery to Fetch Comments of Post
		$.fn.fetchComments = function(post_id) {
			// Variable Defaults
			var wrapper = $(this);

			// Handle Request
			$.ajax('/ajax/members/posts/comments/fetch', {
				data: { id: post_id },
				dataType: 'json',
				method: 'post',
				async: false,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							// Clear Wrapper Content and Append Comments
							wrapper.empty().append(response.comments);
							break;
						case 'error':
						default:
							displayMessage(
								response.message || 'Something went wrong.',
								'alert'
							);
					}
				}
			});
		};

		// Extend jQuery to Fetch Contact List
		$.fn.fetchContactList = function() {
			// Variable Defaults
			var wrapper = $(this);

			// Handle Request
			$.ajax('/ajax/members/messages/contact-list', {
				dataType: 'json',
				method: 'post',
				async: false,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							// Check Contacts
							if(
								response.hasOwnProperty('contacts') &&
								response.contacts.length
							) {
								// Clear Wrapper Content and Append Contacts
								wrapper.empty().append(response.contacts.join('\n'));
							}
							break;
						case 'error':
						default:
							displayMessage(
								response.message || 'Something went wrong.',
								'alert'
							);
					}
				}
			});
		};

		// Extend jQuery to Load Message Pane
		$.fn.loadMessagePane = function(member_id, is_read) {
			// Variable Defaults
			var wrapper        = $(this).find('.messages-container__inner');
			var activeMemberId = wrapper.find('[data-member-id]').data('member-id');

			// Handle Request
			$.ajax('/ajax/members/messages/message-pane', {
				data: { id: member_id, is_read: is_read || false },
				dataType: 'json',
				method: 'post',
				async: false,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							// Check Active Pane
							if(activeMemberId !== member_id) {
								// Clear Wrapper Content and Append Message Pane
								wrapper
									.empty()
									.append(response.pane)
									.find('.list-group.list-group-flush')
									.append(response.messages);

								// Init TinyMCE
								tinymce.init({
									selector: '#message-pane-textarea',
									theme: 'silver',
									cache_suffix: '?v=6.1.2',
									base_url: '/library/packages/tinymce',
									browser_spellcheck: true,
									document_base_url: '/',
									element_format: 'html',
									forced_root_block: '',
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
									toolbar:
										'customReportButton | bold italic underline emoticons | customSubmitButton',
									valid_elements: 'br,strong/b,em/i,u',
									verify_html: true,
									toolbar_location: 'bottom',
									setup: function(editor) {
										// Custom Send Button
										editor.ui.registry.addIcon(
											'fa-paper-plane',
											'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"><path d="M511.6 36.86l-64 415.1c-1.5 9.734-7.375 18.22-15.97 23.05c-4.844 2.719-10.27 4.097-15.68 4.097c-4.188 0-8.319-.8154-12.29-2.472l-122.6-51.1l-50.86 76.29C226.3 508.5 219.8 512 212.8 512C201.3 512 192 502.7 192 491.2v-96.18c0-7.115 2.372-14.03 6.742-19.64L416 96l-293.7 264.3L19.69 317.5C8.438 312.8 .8125 302.2 .0625 289.1s5.469-23.72 16.06-29.77l448-255.1c10.69-6.109 23.88-5.547 34 1.406S513.5 24.72 511.6 36.86z"/></svg>'
										);
										editor.ui.registry.addButton('customSubmitButton', {
											icon: 'fa-paper-plane',
											tooltip: 'Send Message',
											onAction: function() {
												// Check Message
												if(
													Boolean(
														tinymce.activeEditor
															.getContent({ format: 'text' })
															.trim()
													)
												) {
													// Handle Ajax
													$.ajax('/ajax/members/message', {
														data: {
															id: member_id,
															message: tinymce.activeEditor.getContent({
																format: 'html'
															})
														},
														dataType: 'json',
														method: 'post',
														async: false,
														success: function(response) {
															// Switch Status
															switch(response.status) {
																case 'success':
																	// Reload Message Pane
																	$('#messages-container').loadMessagePane(
																		member_id,
																		true
																	);

																	// Clear Content
																	tinymce.activeEditor.setContent('');
																	break;
																case 'suggestion':
																	// Show Modal
																	$(response.modal)
																		.on('hidden.bs.modal', destroyModal)
																		.modal();
																	break;
																case 'error':
																default:
																	displayMessage(
																		response.message || 'Something went wrong.',
																		'alert'
																	);
															}
														}
													});
												}
											}
										});

										// Custom Report Button
										editor.ui.registry.addIcon(
											'fa-triangle-exclamation',
											'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"><path d="M506.3 417l-213.3-364c-16.33-28-57.54-28-73.98 0l-213.2 364C-10.59 444.9 9.849 480 42.74 480h426.6C502.1 480 522.6 445 506.3 417zM232 168c0-13.25 10.75-24 24-24S280 154.8 280 168v128c0 13.25-10.75 24-23.1 24S232 309.3 232 296V168zM256 416c-17.36 0-31.44-14.08-31.44-31.44c0-17.36 14.07-31.44 31.44-31.44s31.44 14.08 31.44 31.44C287.4 401.9 273.4 416 256 416z"/></svg>'
										);
										editor.ui.registry.addButton('customReportButton', {
											icon: 'fa-triangle-exclamation',
											tooltip: 'Report Conversation',
											onAction: function() {
												// Confirm Action
												if(
													confirm(
														'Are you sure you want to report this conversation?'
													)
												) {
													// Handle Ajax
													$.ajax('/ajax/members/messages/report', {
														data: { id: member_id },
														dataType: 'json',
														method: 'post',
														async: false,
														success: function(response) {
															// Switch Status
															switch(response.status) {
																case 'success':
																	// Show Modal
																	$(response.modal)
																		.on('hide.bs.modal', function() {
																			// Reload Message Pane
																			$('#messages-container').loadMessagePane(
																				member_id,
																				true
																			);
																		})
																		.on('hidden.bs.modal', destroyModal)
																		.modal();
																	break;
																case 'error':
																default:
																	displayMessage(
																		response.message || 'Something went wrong.',
																		'alert'
																	);
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
							} else {
								// Clear Messages and Load New Ones
								wrapper
									.find('.list-group.list-group-flush')
									.empty()
									.append(response.messages);
							}
							break;
						case 'error':
						default:
							displayMessage(
								response.message || 'Something went wrong.',
								'alert'
							);
					}
				}
			});
		};

		// Bind Click Event to Temp Action
		$(this.body).on('click', '[data-temp-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);
			var action = button.data('temp-action');

			// Switch Action
			switch(action) {
				case 'comment-toggle':
					// Toggle Comments
					button
						.parents('.post-modal__content')
						.find('[data-post-action="comment-toggle"]')
						.trigger('click');
					break;
				default:
					console.error('unknown temp action:', action);
			}
		});

		// Bind Click Paid Suggestion Action
		$(this.body).on('click', '[data-paid-suggestion-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);
			var action = button.data('paid-suggestion-action');

			// Switch Action
			switch(action) {
				case 'private-photo-suggestion':
					// Handle Ajax
					$.ajax('/ajax/members/message', {
						data: { type: 'private' },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':

									break;
								case 'suggestion':
									// Replace Button(s)
									$(response.modal).on('hidden.bs.modal', destroyModal).modal();
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					button
						.parents('.post-modal__content')
						.find('[data-post-action="comment-toggle"]')
						.trigger('click');
					break;
				default:
					console.error('unknown temp action:', action);
			}
		});

		// Bind Click custom message  Suggestion Action
		$(this.body).on('click', '[data-custom-message-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);
			var action = button.data('custom-message-action');

			// Switch Action
			switch(action) {
				case 'custom-message-suggestion':
					// Handle Ajax
					$.ajax('/ajax/members/message', {
						data: { type: 'custom-message' },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':

									break;
								case 'suggestion':
									// Replace Button(s)
									$(response.modal).on('hidden.bs.modal', destroyModal).modal();
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					button
						.parents('.post-modal__content')
						.find('[data-post-action="comment-toggle"]')
						.trigger('click');
					break;
				default:
					console.error('unknown temp action:', action);
			}
		});

		// Bind Click Paid Suggestion Action
		$(this.body).on('click', '[data-private-photo-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);
			var action = button.data('private-photo-action');

			// Switch Action
			switch(action) {
				case 'private-photo-limit':
					// Handle Ajax
					$.ajax('/ajax/members/posts/photo-limit', {
						data: { private_photos_limit: $('#private-photo-input-limit').val() },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									$('#set_private_photo_limit').modal('hide');
									displayMessage(response.message, 'success');
									break;
								case 'suggestion':
									// Replace Button(s)
									$(response.modal).on('hidden.bs.modal', destroyModal).modal();
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					button
						.parents('.post-modal__content')
						.find('[data-post-action="comment-toggle"]')
						.trigger('click');
					break;
				default:
					console.error('unknown temp action:', action);
			}
		});

		// Bind Click Event to Room Action
		$(this.body).on('click', '[data-room-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button  = $(this);
			var room    = $(this).parents('.rooms__child');
			var dataset = room.data('room');
			var action  = button.data('room-action');
			var toolbar = button.parents('.navbar');

			// Switch Action
			switch(action) {
				case 'favorite':
					// Handle Ajax
					$.ajax('/ajax/members/rooms/favorite/' + dataset.id, {
						dataType: 'json',
						method: 'post',
						async: false,
						beforeSend: showLoader,
						complete: hideLoader,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Reload Toolbar
									toolbar.load(
										location.href + ' #' + toolbar.prop('id') + '>*',
										function() {
											// Check Return HTML
											if(this.children.length === 0) {
												// Remove Room
												room.remove();
											}
										}
									);
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				case 'notifications':
					// Handle Ajax
					$.ajax('/ajax/members/rooms/notifications/' + dataset.id, {
						dataType: 'json',
						method: 'post',
						async: false,
						beforeSend: showLoader,
						complete: hideLoader,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Reload Toolbar
									toolbar.load(
										location.href + ' #' + toolbar.prop('id') + '>*'
									);
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				case 'reviews':
					// Handle Ajax
					$.ajax('/modals/rooms/reviews/' + dataset.id, {
						dataType: 'html',
						method: 'get',
						async: false,
						beforeSend: showLoader,
						complete: hideLoader,
						success: function(html) {
							$(html).on('hidden.bs.modal', destroyModal).modal();
						}
					});
					break;
				case 'availability':
					// Handle Ajax
					$.ajax('/modals/rooms/availability/' + dataset.id, {
						dataType: 'html',
						method: 'get',
						async: false,
						beforeSend: showLoader,
						complete: hideLoader,
						success: function(html) {
							$(html)
								.on('shown.bs.modal', function() {
									// Variable Defaults
									var modal = $(this);

									// Show Loader
									showLoader();

									// Init Availability Calendar
									(function(eventObject) {
										new FullCalendar.Calendar(eventObject.calendar[0], {
											initialView: eventObject.getView(),
											themeSystem: 'bootstrap',
											events: {
												url: eventObject.calendar.data('endpoint'),
												method: 'post'
											},
											eventSourceSuccess: hideLoader,
											showNonCurrentDates: true,
											aspectRatio: 1.8,
											contentHeight: null,
											windowResizeDelay: 100,
											windowResize: function(view) {
												// Set View
												if(eventObject.getView() !== view.type) {
													this.changeView(eventObject.getView());
												}

												// Set Height/Aspect Ratio
												switch(true) {
													case eventObject.getWindowWidth() <= 991:
														eventObject.calendar
															.find('.fc-view-harness')
															.height($('.fc-list-table').outerHeight(true));
														break;
													default:
												}

												// Hanlde Modal Update
												modal.modal('handleUpdate');
											},
											eventRender: function(info) {
												// Highlight Availability
												$(info.el).addClass('d-md-none');
												if(info.event._def.extendedProps.available) {
													$(info.event._calendar.el)
														.find(
															'td.fc-day[data-date="' +
															info.event._def.extendedProps.dateString +
															'"]'
														)
														.addClass('bg-success available');
												} else {
													$(info.event._calendar.el)
														.find(
															'td.fc-day[data-date="' +
															info.event._def.extendedProps.dateString +
															'"]'
														)
														.addClass('bg-danger unavailable');
												}
											},
											datesRender: function(info) {
												console.log('Dates Render');

												// Remove Time & Marker
												$(this.el)
													.find('td.fc-list-item-time, td.fc-list-item-marker')
													.remove();

												// Highlight Availability
												$(this.el)
													.find('tr.tc-list-item.available')
													.addClass('bg-success');
												$(this.el)
													.find('tr.tc-list-item.unavailable')
													.addClass('bg-danger');
											}
										}).render();
									})({
										calendar: modal.find('#availability-calendar'),
										getWindowWidth: function() {
											return window.innerWidth;
										},
										getView: function() {
											return this.getWindowWidth() <= 991
												? 'listMonth'
												: 'dayGridMonth';
										}
									});
								})
								.on('hidden.bs.modal', destroyModal)
								.modal();
						}
					});
					break;
				default:
					console.error('Unknown Room Action:', action);
			}
		});

		// Bind Click Event to Comment Action
		$(this.body).on('click', '[data-comment-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button   = $(this);
			var dataset  = button.data();
			var action   = dataset.commentAction;
			var comment  = button.closest('[data-comment-id]');
			var post     = button.closest('[data-post-id]');
			var comments = post.find('div.post__comments');

			// Switch Action
			switch(action) {
				case 'remove':
					// Confirm Removal
					if(confirm('Are you sure you want to remove your comment?')) {
						// Handle Ajax
						$.ajax(
							'/ajax/members/posts/comments/remove/' +
							comment.data('commentId'),
							{
								dataType: 'json',
								method: 'post',
								async: false,
								success: function(response) {
									// Switch Status
									switch(response.status) {
										case 'success':
											// Update Comments
											comments
												.find('ul.list-unstyled')
												.fetchComments(post.data('postId'));

											// Replace Button(s)
											$('div[data-post-id="' + post.data('postId') + '"]')
												.find(
													'button.toolbar__btn[data-post-action="comment-toggle"]'
												)
												.replaceWith(response.html);

											// Display Message
											displayMessage(response.message, 'success');
											break;
										case 'error':
										default:
											displayMessage(
												response.message || 'Something went wrong.',
												'alert'
											);
									}
								}
							}
						);
					}
					break;
				case 'report':
					// Handle Ajax
					var commentId = $('#commentID').val();
					var type      = $('#comment-report-type').val();
					var message   = $('#comment_report_comment').val();
					$.ajax(
						'/ajax/members/posts/comments/report/' + commentId,
						{
							data: { type: type, message: message },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										$('#comment-report').modal('hide');
										displayMessage(response.message, 'success');
										break;
									case 'error':
									default:
										$('#comment-report').modal('hide');
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}
							}
						}
					);
					break;
				default:
					console.error('unknown comment action:', action);
			}
		});

		// Bind Click Event to Post Action
		$(this.body).on('click', '[data-post-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button     = $(this);
			var dataset    = button.data();
			var post       = button.closest('[data-post-id]');
			var action     = dataset.postAction;
			var comments   = post.find('div.post__comments');
			var scrollable = $(
				comments.parents('.post-modal__content').find('.trim')[0] ||
				$('html,body')
			).add('#ajax-post-modal .modal-body');

			// Switch Action
			switch(action) {
				case 'comment-submit':
					// Variable Defaults
					var input   = post.find(':input[name="comment"]');
					var comment = input.val();

					// Check Comment
					if(Boolean(comment.trim())) {
						// Handle Ajax
						$.ajax('/ajax/members/posts/comments/add', {
							data: { id: post.data('postId'), comment: comment },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Update Comments
										comments
											.find('ul.list-unstyled')
											.fetchComments(post.data('postId'));

										// Clear Input
										input.val('');

										// Replace Button(s)
										$('div[data-post-id="' + post.data('postId') + '"]')
											.find(
												'button.toolbar__btn[data-post-action="comment-toggle"]'
											)
											.replaceWith(response.html);
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}
							}
						});
					}
					break;
				case 'comment-toggle':
					// Check Collapsible Class
					if(button.hasClass('toolbar__btn-collapse')) {
						// Toggle Comments
						comments
							.one('show.bs.collapse', function() {
								// Load Comments
								comments
									.find('ul.list-unstyled')
									.fetchComments(post.data('postId'));

								// Hide Temp Action
								$('[data-temp-action="comment-toggle"]').hide();
							})
							.one('hide.bs.collapse', function() {
								console.log('Hide');

								// Show Temp Action
								$('[data-temp-action="comment-toggle"]').show();
							})
							.collapse('toggle');
					}

					// Scroll to Comment
					scrollable.animate(
						{
							scrollTop: comments.offset().top - 100
						},
						1000
					);
					break;
				case 'like':
					// Handle Ajax
					$.ajax('/ajax/members/posts/like', {
						data: { id: post.data('postId') },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Replace Button(s)
									$('div[data-post-id="' + post.data('postId') + '"]')
										.find('button.toolbar__btn[data-post-action="like"]')
										.replaceWith(response.html);
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				case 'modal':
					// Handle Ajax
					$.ajax('/modals/members/post', {
						data: { id: post.data('postId') },
						dataType: 'json',
						method: 'post',
						async: false,
						beforeSend: showLoader,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Add Post to Poll Object for Updating
									window.pollObject.post = $.extend(
										{},
										window.pollObject.hasOwnProperty('post')
											? window.pollObject.post
											: {},
										{
											id: post.data('postId')
										}
									);

									// Update Polling
									updatePolling();

									// Show Modal
									$(response.modal)
										.on('shown.bs.modal', function() {
											// Variable Defaults
											var comments   = $(this).find('div.post__comments');
											var scrollable = $(
												comments
													.parents('.post-modal__content')
													.find('.trim')[0] || $('html,body')
											).add('#ajax-post-modal .modal-body');

											// Init TinyMCE
											tinymce.init({
												selector: '#ajax-post-modal-textarea',
												theme: 'silver',
												cache_suffix: '?v=6.1.2',
												base_url: '/library/packages/tinymce',
												browser_spellcheck: true,
												document_base_url: '/',
												element_format: 'html',
												forced_root_block: '',
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
												toolbar:
													'bold italic underline emoticons | customSubmitButton',
												valid_elements: 'br,strong/b,em/i,u',
												verify_html: true,
												toolbar_location: 'bottom',
												setup: function(editor) {
													editor.ui.registry.addIcon(
														'fa-paper-plane',
														'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"><path d="M511.6 36.86l-64 415.1c-1.5 9.734-7.375 18.22-15.97 23.05c-4.844 2.719-10.27 4.097-15.68 4.097c-4.188 0-8.319-.8154-12.29-2.472l-122.6-51.1l-50.86 76.29C226.3 508.5 219.8 512 212.8 512C201.3 512 192 502.7 192 491.2v-96.18c0-7.115 2.372-14.03 6.742-19.64L416 96l-293.7 264.3L19.69 317.5C8.438 312.8 .8125 302.2 .0625 289.1s5.469-23.72 16.06-29.77l448-255.1c10.69-6.109 23.88-5.547 34 1.406S513.5 24.72 511.6 36.86z"/></svg>'
													);
													editor.ui.registry.addButton('customSubmitButton', {
														icon: 'fa-paper-plane',
														tooltip: 'Submit Comment',
														onAction: function() {
															// Check Comment
															if(
																Boolean(
																	tinymce.activeEditor
																		.getContent({ format: 'text' })
																		.trim()
																)
															) {
																// Handle Ajax
																$.ajax('/ajax/members/posts/comments/add', {
																	data: {
																		id: post.data('postId'),
																		comment: tinymce.activeEditor.getContent({
																			format: 'html'
																		})
																	},
																	dataType: 'json',
																	method: 'post',
																	async: false,
																	success: function(response) {
																		// Switch Status
																		switch(response.status) {
																			case 'success':
																				// Update Comments
																				comments
																					.find('ul.list-unstyled')
																					.fetchComments(post.data('postId'));

																				// Clear Textarea
																				tinymce.activeEditor.setContent('');

																				// Replace Button(s)
																				$(
																					'div[data-post-id="' +
																					post.data('postId') +
																					'"]'
																				)
																					.find(
																						'button.toolbar__btn[data-post-action="comment-toggle"]'
																					)
																					.replaceWith(response.html);
																				break;
																			case 'error':
																			default:
																				displayMessage(
																					response.message ||
																					'Something went wrong.',
																					'alert'
																				);
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

											// Check Scroll To
											if(button.data('scroll-to')) {
												// Scroll to Comment
												scrollable.animate(
													{
														scrollTop:
															$(this).find('div.post__comments').offset().top -
															100
													},
													1000
												);
											}
										})
										.on('hidden.bs.modal', function() {
											// Destroy Active Editor
											tinymce.activeEditor.destroy();

											// Remove Modal
											$(this).remove();

											// Remove Post from Poll Object
											window.pollObject.hasOwnProperty('post') &&
											delete window.pollObject.post;

											// Update Polling
											updatePolling();
										})
										.modal();
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				case 'report':
					// Handle Ajax
					$.ajax('/ajax/members/posts/report', {
						//data: { id: post.data("postId"), type: dataset.type },
						data: { id: $('#postID').val(), type: $('#post-report-type').val(), message: $('#post_report_comment').val() },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									$('#post-report').modal('hide');
									// Display Message
									displayMessage(response.message, 'success');

									// Replace Button(s)
									$('#post_id_' + $('#postID').val())
										.find('.toolbar__btn-report')
										.dropdown('toggle')
										.replaceWith(response.html);

									break;
								case 'error':
								default:
									$('#post-report').modal('hide');

									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				case 'unlike':
					// Handle Ajax
					$.ajax('/ajax/members/posts/unlike', {
						data: { id: post.data('postId') },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Replace Button(s)
									$('div[data-post-id="' + post.data('postId') + '"]')
										.find('button.toolbar__btn[data-post-action="unlike"]')
										.replaceWith(response.html);
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				case 'view-likes':
					// Handle Ajax
					$.ajax('/modals/members/posts/likes', {
						data: { id: post.data('postId') },
						dataType: 'json',
						method: 'post',
						async: false,
						beforeSend: showLoader,
						complete: hideLoader,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Show Modal
									$(response.modal).on('hidden.bs.modal', destroyModal).modal();
									break;
								case 'suggestion':
									// Show Modal
									$(response.modal).on('hidden.bs.modal', destroyModal).modal();
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				default:
					console.error('unknown post action:', action);
			}
		});

		// Bind Click Events to Event Toolbar
		$('div[id^="event-toolbar"]').on(
			'click',
			'[data-event-action]',
			function(event) {
				// Prevent Default
				event.preventDefault();

				// Variable Defaults
				var action  = $(this).data('event-action');
				var toolbar = $(event.delegateTarget);
				var data    = toolbar.data();

				// Switch Action
				switch(action) {
					case 'rsvp-add':
						// Handle Ajax Request
						$.ajax('/ajax/members/events/reservations/add', {
							dataType: 'json',
							async: false,
							method: 'post',
							data: { id: data.eventId },
							beforeSend: showLoader,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Re-Load Toolbar and RSVPs
										toolbar.add('div[id^="event-rsvps"]').each(function() {
											$(this).load(
												location.href + ' #' + $(this).prop('id') + '>*'
											);
										});

										// Update Lazy Load
										layzrInstance.update().check().handlers(true);

										// Hide Load
										hideLoader();
										break;
									case 'error':
										displayMessage(response.message, 'alert', function() {
											$(this).on('shown.bs.modal', hideLoader);
										});
										break;
									case 'debug':
									default:
										displayMessage(response, 'alert', function() {
											$(this).on('shown.bs.modal', hideLoader);
										});
								}
							}
						});
						break;
					case 'rsvp-remove':
						// Confirm Removal
						if(confirm('Are you sure you want to remove this RSVP?')) {
							// Handle Ajax Request
							$.ajax('/ajax/members/events/reservations/remove', {
								dataType: 'json',
								async: false,
								method: 'post',
								data: { id: data.eventId },
								beforeSend: showLoader,
								success: function(response) {
									// Switch Status
									switch(response.status) {
										case 'success':
											// Re-Load Toolbar and RSVPs
											toolbar.add('div[id^="event-rsvps"]').each(function() {
												$(this).load(
													location.href + ' #' + $(this).prop('id') + '>*'
												);
											});

											// Update Lazy Load
											layzrInstance.update().check().handlers(true);

											// Hide Load
											hideLoader();
											break;
										case 'error':
											displayMessage(response.message, 'alert', function() {
												$(this).on('shown.bs.modal', hideLoader);
											});
											break;
										case 'debug':
										default:
											displayMessage(response, 'alert', function() {
												$(this).on('shown.bs.modal', hideLoader);
											});
									}
								}
							});
						}
						break;
					default:
						console.error('unknown event action:', action);
				}
			}
		);

		// Bind Click Event to Profile Action
		$(this.body).on('click', '[data-profile-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button  = $(this);
			var dataset = button.data();
			var profile = button.closest('[data-profile-id]');
			var action  = dataset.profileAction;
			var toolbar = $('#profile-toolbar-combo');

			// Switch Action
			switch(action) {
				case 'block':
					// Confirm Action
					if(confirm('Are you sure you want to block this member?')) {
						// Handle Ajax
						$.ajax('/ajax/members/block', {
							data: { id: profile.data('profileId') },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										displayMessage(response.message, 'success');

										// Switch Page
										switch(location.pathname) {
											case '/members/friends':
												profile.remove();
												break;
											case '/members/messages':
												showLoader();
												location.reload();
												break;
											default:
												// Re-Load Toolbar
												toolbar.length &&
												toolbar.load(
													location.href + ' #' + toolbar.prop('id') + '>*'
												);

												// Check Modal & Reload
												if($('#ajax-modal').length) location.reload();
										}
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}
							}
						});
					}
					break;
				case 'friend-request-cancel':
					// Confirm Action
					if(confirm('Are you sure you want to cancel this request?')) {
						// Handle Ajax
						$.ajax('/ajax/members/friends/requests/cancel', {
							data: { id: profile.data('profileId') },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										displayMessage(response.message, 'success');

										// Switch Page
										switch(location.pathname) {
											case '/members/friends':
												profile.remove();
												break;
											default:
												// Remove Row
												!toolbar.length &&
												button.closest('[data-notification-id]').remove();
										}
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}

								// Re-Load Toolbar
								toolbar.length &&
								toolbar.load(
									location.href + ' #' + toolbar.prop('id') + '>*'
								);
							}
						});
					}
					break;
				case 'friend-request-remove':
					// Confirm Action
					if(confirm('Are you sure you want to remove this friend?')) {
						// Handle Ajax
						$.ajax('/ajax/members/friends/requests/remove', {
							data: { id: profile.data('profileId') },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										displayMessage(response.message, 'success');

										// Switch Page
										switch(location.pathname) {
											case '/members/friends':
												profile.remove();
												break;
											default:
												// Remove Row
												!toolbar.length &&
												button.closest('[data-notification-id]').remove();
										}
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}

								// Re-Load Toolbar
								toolbar.length &&
								toolbar.load(
									location.href + ' #' + toolbar.prop('id') + '>*'
								);
							}
						});
					}
					break;
				case 'friend-request-accept':
					// Handle Ajax
					var isChecked = $('#Verify').is(':checked');
					console.log('isChecked', isChecked);
					$.ajax('/ajax/members/friends/requests/accept', {
						data: { id: $('#verify-profile-request').data('profile-id'), verify: isChecked },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									displayMessage(response.message, 'success');

									// Switch Page
									switch(location.pathname) {
										case '/members/notifications':
											// Refresh Page
											location.reload();
											break;
										default:
											// Remove Row
											!toolbar.length &&
											button.closest('[data-notification-id]').remove();
									}
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}

							// Re-Load Toolbar
							toolbar.length &&
							toolbar.load(location.href + ' #' + toolbar.prop('id') + '>*');
						}
					});
					break;
				case 'friend-request-decline':
					// Handle Ajax
					$.ajax('/ajax/members/friends/requests/decline', {
						data: { id: profile.data('profileId') },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									displayMessage(response.message, 'success');

									// Switch Page
									switch(location.pathname) {
										case '/members/notifications':
											// Refresh Page
											location.reload();
											break;
										default:
											// Remove Row
											!toolbar.length &&
											button.closest('[data-notification-id]').remove();
									}
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}

							// Re-Load Toolbar
							toolbar.length &&
							toolbar.load(location.href + ' #' + toolbar.prop('id') + '>*');
						}
					});
					break;
				case 'send-friend-request-limt':
					displayMessage(
						'You have exceeded the daily limit. Please upgrade to a paid account to send the friend requests.<a href=\'/members/subscription\'>click here</a>',
						'alert'
					);
					break;
				case 'friend-request-send':
					// Handle Ajax
					const form        = $('#confirmation-friend')[0]; // Get the form DOM element (not the jQuery object)
					const formData    = new FormData(form);
					const formIsEmpty = isFormEmpty(formData);
					if(formIsEmpty) {
						displayMessage(
							'You are missing required fields.',
							'alert'
						);
						return;
					}
					const profileId = $('#profile').data('profile-id');
					const answer    = [];
					const questions = [];
					for(let pair of formData.entries()) {
						if(pair[0] == 'confirmation_answer') {
							answer.push(pair[1]);
						}
						if(pair[0] == 'confirmation_questions') {
							questions.push(pair[1]);
						}
					}

					$.ajax('/ajax/members/friends/requests/send', {
						data: { id: profileId },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':

									$.ajax('/ajax/members/friends/requests/confirmation', {
										data: { id: profileId, questions: questions, answer: answer },
										dataType: 'json',
										method: 'post',
										async: false,
										success: function(response) {
											$('#send_friend_request').modal('hide');
										}

									});
									displayMessage(response.message, 'success');
									$('#confirmation-friend')[0].reset();
									// Switch Page
									switch(location.pathname) {
										default:
											// Remove Row
											!toolbar.length &&
											button.closest('[data-notification-id]').remove();
									}
									break;
								case 'error':
								default:
									$('#send_friend_request').modal('hide');
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
									$('#confirmation-friend')[0].reset();
							}

							// Re-Load Toolbar
							toolbar.length &&
							toolbar.load(location.href + ' #' + toolbar.prop('id') + '>*');
						}
					});
					break;
				case 'message':
					// Handle Ajax
					$.ajax('/ajax/members/messages/modal', {
						data: { id: profile.data('profileId') },
						dataType: 'json',
						method: 'post',
						async: false,
						beforeSend: showLoader,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Show Modal
									$(response.modal)
										.one('shown.bs.modal', function() {
											// Variable Defaults
											var modal     = $(this);
											var member_id = modal.data('profile-id');

											// Init TinyMCE
											tinymce.init({
												selector: '#ajax-message-modal-textarea',
												theme: 'silver',
												cache_suffix: '?v=6.1.2',
												base_url: '/library/packages/tinymce',
												browser_spellcheck: true,
												document_base_url: '/',
												element_format: 'html',
												forced_root_block: '',
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
												toolbar:
													'bold italic underline emoticons | customSubmitButton',
												valid_elements: 'br,strong/b,em/i,u',
												verify_html: true,
												toolbar_location: 'bottom',
												setup: function(editor) {
													editor.ui.registry.addIcon(
														'fa-paper-plane',
														'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"><path d="M511.6 36.86l-64 415.1c-1.5 9.734-7.375 18.22-15.97 23.05c-4.844 2.719-10.27 4.097-15.68 4.097c-4.188 0-8.319-.8154-12.29-2.472l-122.6-51.1l-50.86 76.29C226.3 508.5 219.8 512 212.8 512C201.3 512 192 502.7 192 491.2v-96.18c0-7.115 2.372-14.03 6.742-19.64L416 96l-293.7 264.3L19.69 317.5C8.438 312.8 .8125 302.2 .0625 289.1s5.469-23.72 16.06-29.77l448-255.1c10.69-6.109 23.88-5.547 34 1.406S513.5 24.72 511.6 36.86z"/></svg>'
													);
													editor.ui.registry.addButton('customSubmitButton', {
														icon: 'fa-paper-plane',
														tooltip: 'Send Message',
														onAction: function() {
															// Check Message
															if(
																Boolean(
																	tinymce.activeEditor
																		.getContent({ format: 'text' })
																		.trim()
																)
															) {
																// Handle Ajax
																$.ajax('/ajax/members/message', {
																	data: {
																		id: member_id,
																		message: tinymce.activeEditor.getContent({
																			format: 'html'
																		})
																	},
																	dataType: 'json',
																	method: 'post',
																	async: false,
																	success: function(response) {
																		// Switch Status
																		switch(response.status) {
																			case 'success':
																				// Close Modal
																				modal
																					.on('hidden.bs.modal', function() {
																						displayMessage(
																							response.message,
																							'success'
																						);
																					})
																					.modal('hide');
																				break;
																			case 'suggestion':
																				// Close/Show Modal
																				modal
																					.on('hidden.bs.modal', function() {
																						$(response.modal)
																							.on(
																								'hidden.bs.modal',
																								destroyModal
																							)
																							.modal();
																					})
																					.modal('hide');
																				break;
																			case 'error':
																			default:
																				displayMessage(
																					response.message ||
																					'Something went wrong.',
																					'alert'
																				);
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
										})
										.on('hidden.bs.modal', destroyModal)
										.modal();
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				case 'message-submit':
					// Variable Defaults
					var input   = profile.find(':input[name="message"]');
					var message = tinymce.activeEditor.getContent({ format: 'text' });
					var modal   = input.parents('.modal');

					// Check Comment
					if(Boolean(message.trim())) {
						// Handle Ajax
						$.ajax('/ajax/members/message', {
							data: {
								id: profile.data('profileId'),
								message: tinymce.activeEditor.getContent({ format: 'html' })
							},
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Close Modal
										modal
											.on('hidden.bs.modal', function() {
												!$('#messages-container').length &&
												displayMessage(response.message, 'success');
											})
											.modal('hide');
										break;
									case 'suggestion':
										// Close/Show Modal
										modal
											.on('hidden.bs.modal', function() {
												$(response.modal)
													.on('hidden.bs.modal', destroyModal)
													.modal();
											})
											.modal('hide');
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}
							}
						});
					}
					break;
				case 'report':
					// Handle Ajax
					var profileID = $('#profileID').val();
					var type      = $('#profile-report-type').val();
					var message   = $('#profile_report_comment').val();
					$.ajax('/ajax/members/report', {
						data: { id: profileID, type: type, message: message },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									$('#profile-report').modal('hide');
									// Show Modal
									$(response.modal).on('hidden.bs.modal', destroyModal).modal();
									break;
								case 'error':
								default:
									$('#profile-report').modal('hide');
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				default:
					console.error('unknown profile action:', action);
			}
		});

		// Bind Click Event to Messages Action
		$(this.body).on('click', '[data-messages-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);
			var member = button.closest('[data-member-id]');
			var action = button.data('messages-action');

			// Switch Action
			switch(action) {
				case 'block':
					// Confirm Action
					if(confirm('Are you sure you want to block this member?')) {
						// Handle Ajax
						$.ajax('/ajax/members/block', {
							data: { id: member.data('memberId') },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								displayMessage(response.message, 'success');

								// Switch Status
								switch(response.status) {
									case 'success':
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}

								// Re-Load Toolbar
								toolbar.length &&
								toolbar.load(
									location.href + ' #' + toolbar.prop('id') + '>*'
								);
							}
						});
					}
					break;
				case 'friend-request-accept':
					// Handle Ajax
					$.ajax('/ajax/members/friends/requests/accept', {
						data: { id: member.data('memberId') },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									displayMessage(response.message, 'success');
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}

							// Load Message Pane
							$('#messages-container').loadMessagePane(
								member.data('memberId'),
								true
							);
						}
					});
					break;
				case 'friend-request-cancel':
					// Confirm Action
					if(confirm('Are you sure you want to cancel this request?')) {
						// Handle Ajax
						$.ajax('/ajax/members/friends/requests/cancel', {
							data: { id: member.data('memberId') },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										displayMessage(response.message, 'success');
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}

								// Load Message Pane
								$('#messages-container').loadMessagePane(
									member.data('memberId'),
									true
								);
							}
						});
					}
					break;
				case 'friend-request-remove':
					// Confirm Action
					if(confirm('Are you sure you want to remove this friend?')) {
						// Handle Ajax
						$.ajax('/ajax/members/friends/requests/remove', {
							data: { id: member.data('memberId') },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										displayMessage(response.message, 'success');
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}

								// Load Message Pane
								$('#messages-container').loadMessagePane(
									member.data('memberId'),
									true
								);
							}
						});
					}
					break;
				case 'friend-request-decline':
					// Handle Ajax
					$.ajax('/ajax/members/friends/requests/decline', {
						data: { id: member.data('memberId') },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									displayMessage(response.message, 'success');
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}

							// Load Message Pane
							$('#messages-container').loadMessagePane(
								member.data('memberId'),
								true
							);
						}
					});
					break;
				case 'friend-request-send':
					// Handle Ajax
					$.ajax('/ajax/members/friends/requests/send', {
						data: { id: member.data('memberId') },
						dataType: 'json',
						method: 'post',
						async: false,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									displayMessage(response.message, 'success');
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}

							// Load Message Pane
							$('#messages-container').loadMessagePane(
								member.data('memberId'),
								true
							);
						}
					});
					break;
				case 'load-message-pane':
					// Load Message Pane
					$('#messages-container').loadMessagePane(
						member.data('memberId'),
						true
					);

					// Add Contact to Poll Object for Updating
					window.pollObject.contact_id = member.data('memberId');

					// Update Polling
					updatePolling();
					break;
				case 'message':
					// Handle Ajax
					$.ajax('/ajax/members/messages/modal', {
						data: { id: member.data('memberId') },
						dataType: 'json',
						method: 'post',
						async: false,
						beforeSend: showLoader,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									// Show Modal
									$(response.modal)
										.one('shown.bs.modal', function() {
											// Init TinyMCE
											tinymce.init({
												selector: '#ajax-message-modal-textarea',
												theme: 'silver',
												cache_suffix: '?v=6.1.2',
												base_url: '/library/packages/tinymce',
												browser_spellcheck: true,
												document_base_url: '/',
												element_format: 'html',
												forced_root_block: 'p',
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
												toolbar: 'bold italic underline emoticons',
												valid_elements: 'p,br,strong/b,em/i,u',
												verify_html: true,
												init_instance_callback: hideLoader
											});
										})
										.on('hidden.bs.modal', destroyModal)
										.modal();
									break;
								case 'error':
								default:
									displayMessage(
										response.message || 'Something went wrong.',
										'alert'
									);
							}
						}
					});
					break;
				case 'message-submit':
					// Variable Defaults
					var input   = member.find(':input[name="message"]');
					var message = tinymce.activeEditor.getContent({ format: 'text' });
					var modal   = input.parents('.modal');

					// Check Comment
					if(Boolean(message.trim())) {
						// Handle Ajax
						$.ajax('/ajax/members/message', {
							data: {
								id: member.data('memberId'),
								message: tinymce.activeEditor.getContent({ format: 'html' })
							},
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Close Modal
										modal
											.on('hidden.bs.modal', function() {
												$('#messages-container').loadMessagePane(
													member.data('memberId'),
													true
												);
											})
											.modal('hide');
										break;
									case 'suggestion':
										// Close/Show Modal
										modal
											.on('hidden.bs.modal', function() {
												$(response.modal)
													.on('hidden.bs.modal', destroyModal)
													.modal();
											})
											.modal('hide');
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}
							}
						});
					}
					break;
				default:
					console.error('unknown messages action:', action);
			}
		});

		// Bind Click Event to Notifications Action
		$(this.body).on('click', '[data-notify-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);
			var notify = button.closest('[data-notify-id]');
			var action = button.data('notify-action');

			// Switch Action
			switch(action) {
				case 'delete':
					// Confirm Action
					if(confirm('Are you sure you want to delete this?')) {
						// Handle Ajax
						$.ajax('/ajax/members/notifications/delete', {
							data: { notify_id: notify.data('notifyId') },
							dataType: 'json',
							method: 'post',
							async: false,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Switch Page
										switch(location.pathname) {
											default:
												// Count Notifcations
												if($('div[data-notify-id]').length > 1) {
													// Remove Notification
													notify.remove();
												} else {
													// Reload Page
													location.reload();
												}
										}
										break;
									case 'error':
									default:
										displayMessage(
											response.message || 'Something went wrong.',
											'alert'
										);
								}
							}
						});
					}
					break;
				default:
					console.error('unknown notify action:', action);
			}
		});

		// Bind Click Event to Navigation Action
		$(this.body).on('click', '[data-nav-action]', function(event) {
			// Prevent Default
			event.preventDefault();

			// Variable Defaults
			var button = $(this);
			var action = button.data('nav-action');

			// Switch Action
			switch(action) {
				case 'member-lookup':
					// Handle Ajax
					$.ajax('/modals/members/lookup', {
						dataType: 'html',
						method: 'get',
						async: false,
						success: function(html) {
							// Show Modal
							$(html)
								.on('shown.bs.modal', function() {
									// Variable Defaults
									var modal = $(this);

									// Bind Submit Event to Form
									modal.on('submit', 'form', function(event) {
										// Prevent Default
										event.preventDefault();

										// Handle Ajax
										$.ajax('/ajax/members/lookup', {
											data: $(this).serializeArray(),
											dataType: 'json',
											method: 'post',
											async: false,
											success: function(response) {
												// Close Modal
												modal
													.on('hidden.bs.modal', function() {
														// Switch Status
														switch(response.status) {
															case 'success':
																// Show Loader
																showLoader();

																// Redirect to Profile Page
																location.href = response.redirect;
																break;
															case 'error':
															default:
																displayMessage(
																	response.message || 'Something went wrong.',
																	'alert'
																);
														}
													})
													.modal('hide');
											}
										});
									});
								})
								.on('hidden.bs.modal', destroyModal)
								.modal();
						}
					});
					break;
				default:
					console.error('unknown nav action:', action);
			}
		});

		// Delegate Popovers
		$(this.body).popover({
			selector: '[rel^="popover"]',
			html: true,
			delay: { show: 500, hide: 0 },
			placement: 'right',
			trigger: 'hover',
			content: function() {
				// Variable Defaults
				var popover = $(this);
				var type    = popover.attr('rel').replace(/popover-/, '');
				var content = '';

				// Switch Type
				switch(type) {
					case 'likes':
						// Variable Defaults
						var payload = popover.parents('.posts__child').data();

						// Handle Ajax Request
						$.ajax('/popovers/members/likes', {
							data: payload,
							dataType: 'json',
							async: false,
							method: 'post',
							success: function(response) {
								if(response.hasOwnProperty('users') && response.users.length) {
									// Check Length
									if(response.users.length < 12) {
										// Set Content
										content = response.users.join('<br>');
									} else {
										// Set Content
										content = response.users.slice(0, 10).join('<br>');

										// Check Length
										if(response.users.length > 10) {
											// Append More Users Note
											content += $('<small/>', {
												class: 'text-muted',
												html:
													'<br>+' +
													(response.users.length - 10) +
													' More Members'
											}).prop('outerHTML');
										}
									}
								}
							}
						});
						break;
					default:
						console.error('unknown popover type:', type);
				}

				// Fallback if Content is Empty
				if(!content.length) {
					popover.one('inserted.bs.popover', function() {
						$(this).popover('hide');
					});
				}

				// Dismiss on Click
				popover.one('click', function() {
					$(this).popover('hide');
				});

				return content;
			}
		});

		// Delegate Tooltips
		$(this.body).tooltip({
			selector: '[rel^="tooltip"]',
			delay: { show: 500, hide: 0 },
			title: function() {
				// Variable Defaults
				var tooltip = $(this);
				var type    = tooltip.attr('rel').replace(/tooltip-/, '');
				var title   = '';

				// Handle Ajax Request
				$.ajax('/ajax/tooltips/' + type, {
					dataType: 'text',
					async: false,
					method: 'get',
					success: function(response) {
						title = response;
					}
				});

				// Fallback if Title is Empty
				if(!title.length) {
					tooltip.one('inserted.bs.tooltip', function() {
						$(this).tooltip('hide');
					});
				}

				// Dismiss on Click
				tooltip.one('click', function() {
					$(this).tooltip('hide');
				});

				return title;
			}
		});

		// Attempt Closing Poll on Window Unload
		$(window).one('beforeunload', updatePolling);
	});

});

function verifyFriendRequest(initiated_by, member_id) {
	$('#view_confirmation_friend_request').modal('show');
	$('#verify-profile-request').attr('data-profile-id', initiated_by);
	$.ajax('/ajax/members/friends/requests/verify-friend', {
		data: { initiated_by: initiated_by, member_id: member_id },
		dataType: 'json',
		method: 'post',
		async: false,
		success: function(response) {
			// Switch Status
			switch(response.status) {
				case 'success':
					var $container = $('#view_confirmation-list');
					$container.empty();
					$.each(response.data, function(index, item) {
						$container.append('<p><strong>' + item.confirmation_questions + ':</strong> </p>');
						$container.append('<p>' + item.confirmation_answer + ' </p>');
					});
					break;
				case 'error':
				default:
					displayMessage(
						response.message || 'Something went wrong.',
						'alert'
					);
			}

		}
	});
}

function sendDefaultMessage(member_id, msg) {
	// Handle Ajax
	$.ajax('/ajax/members/message', {
		data: {
			id: member_id,
			message: msg
		},
		dataType: 'json',
		method: 'post',
		async: false,
		success: function(response) {
			// Switch Status
			switch(response.status) {
				case 'success':
					// Close Modal

					displayMessage(
						response.message,
						'success'
					);

					break;
				case 'suggestion':
					// Close/Show Modal
					$(response.modal)
						.on(
							'hidden.bs.modal',
							destroyModal
						)
						.modal();
					break;
				case 'error':
				default:
					displayMessage(
						response.message ||
						'Something went wrong.',
						'alert'
					);
			}
		}
	});
}

/*function verificationQrCode(member_id) {
	// Handle Ajax
	$.ajax('/ajax/members/verification-qr-code', {
		data: {
			id: member_id
		},
		dataType: 'json',
		method: 'post',
		async: false,
		success: function(response) {
			// Switch Status
			switch(response.status) {
				case 'success':
					// Close Modal
					//$('#showQRcode').modal('show');
					$('#Qrcodeimage').attr('src', response.data);

					break;
				case 'suggestion':
					// Close/Show Modal
					$(response.modal)
						.on(
							'hidden.bs.modal',
							destroyModal
						)
						.modal();
					break;
				case 'error':
				default:
					displayMessage(
						response.message ||
						'Something went wrong.',
						'alert'
					);
			}
		}
	});
} */
function verificationQrCode(member_id) {
    $.ajax('/ajax/members/verification-qr-code', {
        data: { id: member_id },
        dataType: 'json',
        method: 'post',
        async: false,
        success: function(response) {
            switch (response.status) {
                case 'success':
                    $('#Qrcodeimage').attr('src', response.data);
                    break;

                case 'error':
                    // Handle expired or invalid QR codes gracefully
                    const msg = response.message || 'This QR code is expired or invalid.';
                    displayMessage(msg, 'alert', function () {
                        if (msg.toLowerCase().includes('expired')) {
                            if (confirm('Your QR code expired. Generate a new one now?')) {
                                regenerateQrCode(member_id);
                            }
                        }
                    });
                    break;

                case 'suggestion':
                    $(response.modal)
                        .on('hidden.bs.modal', destroyModal)
                        .modal();
                    break;

                default:
                    displayMessage('Unexpected response. Please try again.', 'alert');
            }
        },
        error: function () {
            displayMessage('Could not contact server for QR verification.', 'alert');
        }
    });
}

function regenerateQrCode(member_id) {
    $.ajax('/ajax/members/regenerate-qr-code', {
        data: { id: member_id },
        dataType: 'json',
        method: 'post',
        async: false,
        success: function (response) {
            if (response.status === 'success') {
                $('#Qrcodeimage').attr('src', response.data);
                displayMessage('A new QR code has been generated.', 'success');
            } else {
                displayMessage(response.message || 'Failed to regenerate QR code.', 'alert');
            }
        },
        error: function () {
            displayMessage('Error connecting to server. Try again.', 'alert');
        }
    });
}


$('#send_friend_request').on('hidden.bs.modal', function() {
	// Reset the form fields
	$('#confirmation-friend')[0].reset();

	// Optionally, reset other fields like checkboxes or radio buttons
	$('#confirmation-friend')
		.find('input[type="checkbox"], input[type="radio"]')
		.prop('checked', false);
});

function isFormEmpty(formData) {
	var i = 0;
	for(let [key, value] of formData.entries()) {
		if(key === 'confirmation_answer' && value.trim()) { // Check if the key is 'confirmation_answer' and the value is not empty
			i++;
			//return false; // At least one 'confirmation_answer' field has a value
		}
	}
	if(i >= 2) {
		return false;
	}
	return true; // Form is empty
}

function showPostReportModal(postID) {
	$('#post-reports')[0].reset();
	$('#post-report').modal('show');
	$('#postID').val(postID);
}

function showProfileReportModal(profileID) {
	$('#profile-reports')[0].reset();
	$('#profile-report').modal('show');
	$('#profileID').val(profileID);
}

function showCommentReportModal(commentID) {
	$('#comment-reports')[0].reset();
	$('#comment-report').modal('show');
	$('#commentID').val(commentID);
}
