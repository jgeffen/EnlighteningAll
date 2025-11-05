/*!
 * jQuery Uploader v1.0
 *
 * Copyright 2020, 2021 Daerik
 * Released under the MIT license
 */
(function($) {
	// Variable Defaults
	var plugin = { name: 'QuickDeploy: jQuery Uploader', version: 'v1.0', author: 'Daerik', dependencies: ['Dropzone'] };

	/**
	 * @param {Object}	 handlerSettings
	 * @param {String}	 [handlerSettings.acceptedFiles]	(Optional) Accepted MIME types
	 * @param {Object} 	 [handlerSettings.additionalData]	(Optional) Additional data
	 * @param {Function} [handlerSettings.dateCallback]		(Optional) Callback function to set EXIF datetime
	 * @param {String} 	 handlerSettings.deleteUrl			(Required) Ajax delete endpoint
	 * @param {Integer}	 [handlerSettings.maxFilesize]		(Optional) Max file upload size in MB
	 * @param {Function} [handlerSettings.onDelete]			(Optional) Method for handling delete
	 * @param {Function} [handlerSettings.onView]			(Optional) Method for handling view
	 * @param {String} 	 handlerSettings.progressBarUrl		(Required) Ajax progress bar endpoint
	 * @param {String} 	 handlerSettings.stageUrl		 	(Required) Ajax stage endpoint
	 * @param {String} 	 handlerSettings.uploadUrl			(Required) Ajax upload endpoint
	 */
	$.fn.uploader = function(handlerSettings) {
		// Reset Settings
		handlerSettings = $.extend({}, {
			acceptedFiles: 'image/png,image/jpeg,image/gif',
			additionalData: {},
			dateCallback: null,
			deleteUrl: '',
			maxFilesize: window.hasOwnProperty('settings') && window.settings.hasOwnProperty('maxFilesize') ? window.settings.maxFilesize['MB'] : 16,
			onDelete: function(uploader, instance, settings) {
				// Variable Defaults
				var data = ($(instance).find('[data-uploader-id]').length ? $(instance).find('[data-uploader-id]') : $(instance)).data();

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
							uploader: {
								aspect: data['uploaderAspect'],
								format: data['uploaderFormat'],
								id: data['uploaderId'],
								source: data['uploaderSource'],
								type: data['uploaderType']
							}
						},
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									$(instance).load(location.href + ' #' + $(instance).prop('id') + '>*', function() {
										if(!$(this).children().length) return $(this).remove();

										$(instance).uploader(settings);
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
			onView: function(uploader, instance) {
				// Variable Defaults
				var data = ($(instance).find('[data-uploader-source]').length ? $(instance).find('[data-uploader-source]') : $(instance)).data();

				// Show Modal
				$(
					'<div id="uploader-image-preview-modal" class="modal fade" aria-hidden="true" tabindex="-1">' +
					'	<div class="modal-dialog modal-dialog-centered d-flex justify-content-center modal-xl">' +
					'		<div class="position-relative">' +
					'			<img src="' + data['uploaderSource'] + '" class="img-fluid mb-0" style="max-height:80vh!important">' +
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
			uploadUrl: ''
		}, (typeof handlerSettings === 'object') ? handlerSettings : {});

		// Loop Through Image Handlers
		return this.each(function(key, instance) {
			// Loop Through Uploaders
			$(instance).find('div[id^="image-uploader-stage"]').each(function(index, uploader) {
				// Resize Canvas
				$(uploader).css('width', 800);

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
							data: JSON.stringify(handlerSettings.additionalData)
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
													$(instance).html($(response).find('#' + $(instance).prop('id'))).uploader(handlerSettings);
												}
											});
										} else {
											// Set Uploaded Stage
											$(uploader).replaceWith($('<div id="image-uploader-stage" class="row justify-content-center"/>'));
											var stage = $('div[id^="image-uploader-stage"]');

											// Append Input Field to Stage
											stage.append(
												$('<input/>', {
													type: 'hidden',
													name: 'filename',
													value: response.filename
												})
											);

											// Append Image to Stage
											$.ajax(handlerSettings.stageUrl, {
												dataType: 'html',
												async: true,
												method: 'post',
												beforeSend: null,
												complete: null,
												data: response,
												success: function(response) {
													// Append Image to Stage
													stage.append(response);
												},
												error: function(xhr) {
													displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
												}
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
			$(instance).off('click').on('click', '[data-uploader-action]', function(event) {
				// Prevent Default
				event.preventDefault();

				// Switch Action
				switch($(this).data('uploader-action')) {
					case 'delete':
						handlerSettings.onDelete($(this), instance, handlerSettings);
						break;
					case 'view':
						handlerSettings.onView($(this), instance, handlerSettings);
						break;
					default:
						console.error(plugin.name, 'Unknown Action', $(this).data('uploader-action'));
				}
			});
		});
	};
})(jQuery);