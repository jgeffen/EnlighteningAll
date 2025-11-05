/*!
 * jQuery Cropper v2.0
 *
 * Copyright 2020, 2021 Daerik
 * Released under the MIT license
 */
(function($) {
	// Variable Defaults
	var plugin = { name: 'QuickDeploy: jQuery Cropper', version: 'v2.0', author: 'Daerik', dependencies: ['Dropzone', 'Cropper'] };

	/**
	 * @param {Object}	 handlerSettings
	 * @param {String}	 [handlerSettings.acceptedFiles]	(Optional) Accepted MIME types
	 * @param {Object} 	 [handlerSettings.additionalData]	(Optional) Additional data
	 * @param {String}	 [handlerSettings.backgroundColor]	(Optional) Hex value for background color
	 * @param {String}	 handlerSettings.cropperModalUrl	(Required) Ajax cropper modal endpoint
	 * @param {String}	 handlerSettings.cropUrl			(Required) Ajax crop endpoint
	 * @param {Function} [handlerSettings.dateCallback]		(Optional) Callback function to set EXIF datetime
	 * @param {String} 	 handlerSettings.deleteUrl			(Required) Ajax delete endpoint
	 * @param {Integer}	 [handlerSettings.maxFilesize]		(Optional) Max file upload size in MB
	 * @param {Function} [handlerSettings.onCrop]			(Optional) Method for handling crop
	 * @param {Function} [handlerSettings.onDelete]			(Optional) Method for handling delete
	 * @param {Function} [handlerSettings.onView]			(Optional) Method for handling view
	 * @param {String} 	 handlerSettings.progressBarUrl		(Required) Ajax progress bar endpoint
	 * @param {String} 	 handlerSettings.stageUrl		 	(Required) Ajax stage endpoint
	 * @param {Object} 	 [handlerSettings.template]			(Optional) Template size
	 * @param {String} 	 handlerSettings.uploadUrl			(Required) Ajax upload endpoint
	 */
	$.fn.cropper = function(handlerSettings) {
		// Reset Settings
		handlerSettings = $.extend({}, {
			acceptedFiles: 'image/png,image/jpeg',
			additionalData: {},
			backgroundColor: '#FFFFFF',
			cropperModalUrl: '',
			cropUrl: '',
			dateCallback: null,
			deleteUrl: '',
			maxFilesize: window.hasOwnProperty('settings') && window.settings.hasOwnProperty('maxFilesize') ? window.settings.maxFilesize['MB'] : 16,
			onCrop: function(cropper, instance, handlerSettings) {
				// Variable Defaults
				var data = (
					cropper.find('[data-cropper-source]').length
						? cropper.find('[data-cropper-source]')
						: (
							$(instance).find('[data-cropper-source]').length
								? $(instance).find('[data-cropper-source]')
								: $(instance)
						)
				).data();

				// Handle Ajax Request
				$.ajax(handlerSettings.cropperModalUrl, {
					dataType: 'html',
					async: false,
					method: 'post',
					beforeSend: showLoader,
					complete: hideLoader,
					data: {
						data: JSON.stringify(handlerSettings.additionalData),
						template: JSON.stringify(handlerSettings.template),
						cropper: {
							aspect: data['cropperAspect'],
							format: data['cropperFormat'],
							id: data['cropperId'],
							source: data['cropperSource'],
							type: data['cropperType']
						}
					},
					success: function(response) {
						$(response).on('shown.bs.modal', function() {
							// Variable Defaults
							var cropperModal = $(this);

							try {
								// Init CropperJS
								new Cropper($('#image-cropper-modal-image')[0], {
									dragMode: 'move',
									cropBoxResizable: true,
									autoCropArea: 0.8,
									aspectRatio: data['cropperAspect'],
									ready: function() {
										// Variable Defaults
										var cropperJs = this.cropper;

										// Bind Controls
										$('#image-cropper-modal-controls').on('click', 'a[id^="image-cropper-modal-controls"]', function(event) {
											// Prevent Default
											event.preventDefault();

											// Switch ID
											switch($(this).prop('id')) {
												case 'image-cropper-modal-controls-rotate':
													cropperJs.rotate(90);
													break;
												case 'image-cropper-modal-controls-zoom-in':
													cropperJs.zoom(0.1);
													break;
												case 'image-cropper-modal-controls-zoom-out':
													cropperJs.zoom(-0.1);
													break;
												case 'image-cropper-modal-controls-cancel':
													cropperModal.modal('hide');
													break;
												case 'image-cropper-modal-controls-submit':
													// Handle Ajax Request
													$.ajax(handlerSettings.cropUrl, {
														dataType: 'json',
														async: false,
														method: 'post',
														beforeSend: showLoader,
														complete: hideLoader,
														data: {
															backgroundColor: handlerSettings.backgroundColor,
															data: JSON.stringify(handlerSettings.additionalData),
															cropper: {
																aspect: data['cropperAspect'],
																data: cropperJs.getData(true),
																format: data['cropperFormat'],
																id: data['cropperId'],
																source: data['cropperSource'],
																type: data['cropperType']
															}
														},
														success: function(response) {
															// Variable Defaults
															var thumb = response.hasOwnProperty('thumb') ? response.thumb : response.source;

															// Switch Status
															switch(response.status) {
																case 'success':
																	// Replace Source
																	$(instance).find('img[src^="' + thumb + '"]').prop('src', thumb + '?v=' + Date.now());

																	// Close Modal
																	cropperModal.modal('hide');
																	break;
																case 'error':
																	displayMessage(response.message || 'An unknown error has occurred.', 'alert');
																	break;
																case 'debug':
																default:
																	console.log(response);
															}
														},
														error: function(xhr) {
															displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
														}
													});
													break;
											}
										});
									}
								});
							} catch(exception) {
								console.error(plugin.name, exception.message);
							}
						}).on('hidden.bs.modal', function() {
							$(this).remove();
						}).modal({
							backdrop: 'static',
							keyboard: false
						}).find('div.modal-content').css({
							'max-width': '100%',
							'max-height': '100%',
							'width': '100vw',
							'height': '100vh'
						});
					},
					error: function(xhr) {
						displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
					}
				});
			},
			onDelete: function(cropper, instance, settings) {
				// Variable Defaults
				var data = ($(instance).find('[data-cropper-id]').length ? $(instance).find('[data-cropper-id]') : $(instance)).data();

				// Confirm Deletion
				if(confirm('Are you sure you want do delete this?')) {
					$.ajax(handlerSettings.deleteUrl, {
						dataType: 'json',
						async: false,
						method: 'post',
						beforeSend: showLoader,
						complete: hideLoader,
						data: {
							data: JSON.stringify(handlerSettings.additionalData),
							template: JSON.stringify(handlerSettings.template),
							cropper: {
								aspect: data['cropperAspect'],
								format: data['cropperFormat'],
								id: data['cropperId'],
								source: data['cropperSource'],
								type: data['cropperType']
							}
						},
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									$(instance).load(location.href + ' #' + $(instance).prop('id') + '>*', function() {
										if(!$(this).children().length) return $(this).remove();

										$(instance).cropper(settings);
									});
									break;
								case 'error':
									displayMessage(response.message, 'alert');
									break;
								case 'debug':
								default:
									displayMessage(response, 'alert');
							}
						},
						error: function(xhr) {
							displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
						}
					});
				}
			},
			onView: function(cropper, instance) {
				// Variable Defaults
				var data = ($(instance).find('[data-cropper-source]').length ? $(instance).find('[data-cropper-source]') : $(instance)).data();

				// Show Modal
				$(
					'<div id="cropper-image-preview-modal" class="modal fade" aria-hidden="true" tabindex="-1">' +
					'	<div class="modal-dialog modal-dialog-centered d-flex justify-content-center modal-xl">' +
					'		<div class="position-relative">' +
					'			<img src="' + data['cropperSource'] + '" class="img-fluid mb-0" style="max-height:80vh!important">' +
					'			<div class="card-img-overlay pb-0">' +
					'				<button class="btn btn-link text-white float-right p-0" type="button" aria-label="Close" data-dismiss="modal">' +
					'					<i class="fal fa-times-circle fa-2x"></i>' +
					'				</button>' +
					'			</div>' +
					'		</div>' +
					'	</div>' +
					'</div>'
				).on('hidden.bs.modal', function() {
					$(this).remove();
				}).modal();
			},
			progressBarUrl: '',
			stageUrl: '',
			template: { width: 800, height: 533 },
			type: 'public',
			uploadUrl: ''
		}, (typeof handlerSettings === 'object') ? handlerSettings : {});

		// Loop Through Image Handlers
		return this.each(function(key, instance) {
			// Loop Through Uploaders
			$(instance).find('div[id^="image-cropper-uploader"]').each(function(index, uploader) {
				// Variable Defaults
				var template = $(uploader).data('cropper-template') || handlerSettings.template;

				// Resize Canvas
				$(uploader).css('width', template.width || 800);

				try {
					// Init Dropzone
					new Dropzone(uploader, {
						url: handlerSettings.uploadUrl,
						acceptedFiles: handlerSettings.acceptedFiles,
						createImageThumbnails: false,
						previewsContainer: false,
						paramName: 'image',
						maxFiles: 1,
						maxFilesize: handlerSettings.maxFilesize,
						params: {
							backgroundColor: handlerSettings.backgroundColor,
							data: JSON.stringify(handlerSettings.additionalData),
							template: JSON.stringify(handlerSettings.template)
						},
						success: function(file, response) {
							// Set Instance
							var dzInstance = this;

							// Variable Defaults
							response = JSON.parse(response);

							// Hide Progress Bar
							$('#progress-modal').on('hidden.bs.modal', function() {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Check Edit
										if(response.edit) {
											// Reload Cropper
											$.ajax({
												dataType: 'html',
												async: true,
												method: 'get',
												beforeSend: showLoader,
												complete: hideLoader,
												success: function(response) {
													$(instance).html($(response).find('#' + $(instance).prop('id'))).cropper(handlerSettings);
												},
												error: function(xhr) {
													displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
												}
											});
										} else {
											// Set Uploaded Stage
											$(uploader).replaceWith($('<div id="image-cropper-uploaded" class="row justify-content-center"/>'));
											var stage = $('div[id^="image-cropper-uploaded"]');

											// Append Input Field to Stage
											stage.append(
												$('<input/>', {
													type: 'hidden',
													name: 'filename',
													value: response.filename
												})
											);

											// Iterate Over Sizes
											$.each(response.sizes, function(key, size) {
												// Append Sizes to Stage for Cropping
												$.ajax(handlerSettings.stageUrl, {
													dataType: 'html',
													async: true,
													method: 'post',
													beforeSend: null,
													complete: null,
													data: size,
													success: function(response) {
														// Append Size to Stage
														stage.append(response);
													},
													error: function(xhr) {
														displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
													}
												});
											});

											// Check Callback
											if(handlerSettings.dateCallback instanceof Function) {
												handlerSettings.dateCallback(response.datetime);
											}
										}
										break;
									case 'error':
										// Handle Error
										displayMessage(response.message, 'alert');

										// Reset
										dzInstance.removeAllFiles(true);
										break;
									default:
										// Handle Error
										displayMessage(response.message || 'An unknown error has occurred', 'alert');

										// Reset
										dzInstance.removeAllFiles(true);
								}
							}).modal('hide');
						},
						totaluploadprogress: function(totalUploadProgress, totalBytes, totalBytesSent) {
							// Variable Defaults
							var progress = Math.floor(totalUploadProgress) + '%';

							// Update Progress Bar
							if(totalBytesSent > 0) {
								$('.progress-bar').css('width', progress);
								$('#progress-label').html(progress);
							}
						},
						sending: function() {
							// Set Instance
							var dzInstance = this;

							// Create Progress Bar
							$.ajax(handlerSettings.progressBarUrl, {
								method: 'post',
								dataType: 'html',
								async: false,
								success: function(response) {
									$('footer').append(response);
									$('#progress-modal').on('hidden.bs.modal', function() {
										$(this).remove();
									}).modal();
								},
								error: function(xhr) {
									// Handle Error
									displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');

									// Reset
									dzInstance.removeAllFiles(true);
								}
							});
						},
						error: function(file, message) {
							// Set Instance
							var dzInstance = this;

							// Hide Progress Modal
							$('#progress-modal').modal('hide');

							// Handle Error
							displayMessage(message, 'alert');

							// Reset
							dzInstance.removeAllFiles(true);
						}
					});
				} catch(exception) {
					console.error(plugin.name, exception.message);
				}
			});

			// Bind Action Events
			$(instance).off('click').on('click', '[data-cropper-action]', function(event) {
				// Prevent Default
				event.preventDefault();

				// Switch Action
				switch($(this).data('cropper-action')) {
					case 'crop':
						handlerSettings.onCrop($(this), instance, handlerSettings);
						break;
					case 'delete':
						handlerSettings.onDelete($(this), instance, handlerSettings);
						break;
					case 'view':
						handlerSettings.onView($(this), instance, handlerSettings);
						break;
					default:
						console.error(plugin.name, 'Unknown Action', $(this).data('cropper-action'));
				}
			});
		});
	};
})(jQuery);