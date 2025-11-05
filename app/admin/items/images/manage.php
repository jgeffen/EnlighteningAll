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
	
	// Variable Defaults
	$page_title  = 'Manage Images';
	$image_sizes = array(
		'landscape' => array(
			'width'  => 900,
			'height' => 600
		),
		'portrait'  => array(
			'width'  => 600,
			'height' => 900
		),
		'square'    => array(
			'width'  => 900,
			'height' => 900
		)
	);
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
			
			<button class="btn btn-primary" data-custom-action="add">
				<i class="fa fa-plus"></i>
				<span class="d-none d-sm-inline">Add Images(s)</span>
			</button>
		</div>
		
		<span class="d-block mb-3">
			<strong class="text-muted">Legend:</strong>
			<span class="badge bg-warning text-dark">Unpublished</span>
		</span>
		
		<div id="image-management" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4" data-sortablejs="parent" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>"></div>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var tableView    = $('#view-table');
		var tableElement = $('#image-management');
		var tableOptions = tableElement.data('tableOptions');
		var imageSizes   = null || <?php echo json_encode($image_sizes); ?>;
		
		// Init Table
		var initTable = (function() {
			// Variable Defaults
			var options = {
				data: { table_name: tableOptions.table_name.replace('-', '_'), table_id: tableOptions.table_id },
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							// Console Message
							console.info(response.message);
							
							// Loop Through Data
							response.data.forEach(function(item) {
								// Append Stage
								$(item.stage).appendTo(tableElement);
							});
							
							// Trigger Lazy Load
							layzrInstance.update().check();
							
							// Init Cropper
							$('div[id^="image-cropper-wrapper-"]').cropper({
								acceptedFiles: 'image/png,image/jpeg',
								backgroundColor: '#FFFFFF',
								template: imageSizes[Object.keys(imageSizes)[0]],
								cropperModalUrl: '/modals/admin/cropper',
								cropUrl: '/ajax/admin/cropper/crop',
								deleteUrl: null,
								maxFilesize: settings.maxFilesize.MB,
								progressBarUrl: '/modals/admin/cropper/progress-bar',
								stageUrl: null,
								uploadUrl: null,
								additionalData: { table_name: 'images', sizes: imageSizes }
							});
							break;
						case 'error':
							displayMessage(response.message || Object.keys(response.errors).map(function(key) {
								return response.errors[key];
							}).join('<br>'), 'alert', null);
							break;
						default:
							displayMessage(response.message || 'Something went wrong.', 'alert');
					}
				}
			};
			
			// Handle Ajax
			$.ajax(options);
			
			return {
				reload: function() {
					// Clear Children
					tableElement.children().remove();
					
					// Handle Ajax
					$.ajax(options);
				}
			};
		})();
		
		// Bind Action Events
		tableElement.on('click', '[data-action]', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Variable Defaults
			var currentRow = $(this).parents('.sortable-item');
			var action     = $(this).data('action');
			var data       = currentRow.data() || [];
			
			// Switch Action
			switch(action) {
				case 'delete':
					// Confirm Deletion
					if(confirm('Are you sure you want to delete this?')) {
						// Handle Ajax
						$.ajax('/user/delete/images/' + data.item.id, {
							data: JSON.stringify({ table_name: tableOptions.table_name, table_id: tableOptions.table_id }),
							dataType: 'json',
							method: 'delete',
							async: true,
							beforeSend: showLoader,
							complete: hideLoader,
							success: function(response) {
								// Switch Status
								switch(response.status) {
									case 'success':
										// Console Message
										console.info(response.message);
										
										// Reload
										initTable.reload();
										break;
									case 'error':
										displayMessage(response.message || Object.keys(response.errors).map(function(key) {
											return response.errors[key];
										}).join('<br>'), 'alert', null);
										break;
									default:
										displayMessage(response.message || 'Something went wrong.', 'alert');
								}
							}
						});
					}
					break;
				case 'edit':
					// Handle Ajax Request
					$.ajax('/user/edit/images/' + tableOptions.table_name + '/' + tableOptions.table_id + '/' + data.item.id, {
						dataType: 'html',
						method: 'get',
						async: false,
						beforeSend: showLoader,
						complete: hideLoader,
						success: function(html) {
							// Display Modal
							$(html).on('submit', 'form', function(event) {
								// Prevent Default
								event.preventDefault();
								
								// Handle Ajax
								$.ajax('/user/edit/images/' + tableOptions.table_name + '/' + tableOptions.table_id + '/' + data.item.id, {
									data: Object.assign($(this).serializeObject(), { item: data.item }),
									dataType: 'json',
									method: 'post',
									async: false,
									beforeSend: function() {
										// Show Loader
										showLoader();
										
										// Hide Modal
										$(event.delegateTarget).modal('hide');
									},
									complete: hideLoader,
									success: function(response) {
										// Switch Status
										switch(response.status) {
											case 'success':
												initTable.reload();
												break;
											case 'error':
												displayMessage(response.message || Object.keys(response.errors).map(function(key) {
													return response.errors[key];
												}).join('<br>'), 'alert', null);
												break;
											default:
												displayMessage(response.message || 'Something went wrong.', 'alert');
										}
									}
								});
							}).on('shown.bs.modal', function() {
								// Manually Trigger Select Boxes
								$(this).find('select').trigger('change');
								
								// Init Flatpickr
								flatpickr('#published-date', {
									mode: 'single',
									altInput: true,
									altFormat: 'M j, Y',
									dateFormat: 'Y-m-d',
									plugins: [new confirmDatePlugin({
										confirmIcon: '<i class="fa-solid fa-circle-check ml-1"></i>',
										confirmText: 'Okay!',
										showAlways: true,
										theme: 'light'
									})]
								});
							}).on('hidden.bs.modal', function() {
								// Destroy Flatpickr
								$(this).find('.flatpickr-input')[0]._flatpickr.destroy();
								
								// Destroy Modal
								$(this).remove();
							}).modal();
						}
					});
					break;
				case 'view':
					// Show Fancybox
					$.fancybox.open([{ src: data.cropperSource, opts: { caption: data.item.caption } }]);
					break;
				default:
					console.error('Unknown Action:', action);
			}
		});
		
		// Bind Custom Action Events
		tableView.on('click', '[data-custom-action]', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Variable Defaults
			var action = $(this).data('customAction');
			
			// Switch Action
			switch(action) {
				case 'add':
					// Create Previews Container Modal
					$.ajax('/user/previews-container/images', {
						method: 'post',
						dataType: 'html',
						async: false,
						success: function(html) {
							// Show Modal
							$(html).on('shown.bs.modal', function() {
								// Variable Defaults
								var previewsModal    = $(this);
								var previewsFooter   = previewsModal.find('.modal-footer');
								var previewsProgress = previewsFooter.find('.progress-bar');
								
								// Init Dropzone
								new Dropzone('#bulk-file-uploader', {
									url: '/user/upload/images',
									acceptedFiles: 'image/png,image/jpeg',
									previewsContainer: '#bulk-file-uploader-previews-container',
									createImageThumbnails: true,
									thumbnailWidth: imageSizes[Object.keys(imageSizes)[0]].width * 0.1,
									thumbnailHeight: imageSizes[Object.keys(imageSizes)[0]].height * 0.1,
									paramName: 'image',
									maxFilesize: settings.maxFilesize.MB,
									maxThumbnailFilesize: settings.maxFilesize.MB,
									parallelUploads: 1,
									params: {
										table_name: tableOptions.table_name.replace('-', '_'),
										table_id: tableOptions.table_id,
										sizes: JSON.stringify(imageSizes)
									},
									success: function(file, response) {
										// Variable Defaults
										response = JSON.parse(response);
										
										// Switch Status
										switch(response.status) {
											case 'success':
												// Handle Success
												console.info(response.message);
												break;
											case 'error':
											default:
												// Handle Error
												$(file.previewElement)
													.addClass('upload-error')
													.find('strong[data-dz-errormessage]')
													.text(response.message || 'An unknown error has occurred')
													.parent()
													.addClass('mt-3');
										}
									},
									totaluploadprogress: function(totalUploadProgress, totalBytes, totalBytesSent) {
										// Set Instance
										var dzInstance = this;
										
										// Check if Upload Sent
										if(dzInstance.batchUpload) {
											// Update Batch Upload
											dzInstance.batchUpload.loaded += totalBytesSent - dzInstance.batchUpload.current;
											dzInstance.batchUpload.current  = totalBytesSent;
											dzInstance.batchUpload.progress = Math.floor((dzInstance.batchUpload.loaded / dzInstance.batchUpload.total) * 100) + '%';
											
											// Update Progress Bar
											if(totalBytesSent > 0) {
												previewsProgress.css('width', dzInstance.batchUpload.progress);
												previewsProgress.html(dzInstance.batchUpload.progress);
											}
										}
									},
									sending: function() {
										// Set Instance
										var dzInstance = this;
										
										// Check Batch Upload
										if(!this.hasOwnProperty('batchUpload')) {
											// Show Modal Footer
											previewsFooter.show();
											
											// Remove Uploader
											$(dzInstance.element).remove();
											
											// Set Batch Upload
											dzInstance.batchUpload = {
												loaded: 0,
												current: 0,
												progress: '0%',
												total: Array.from(dzInstance.files).reduce(function(a, b) {
													return a + b.size;
												}, 0)
											};
										} else {
											// Reset Batch Upload
											dzInstance.batchUpload.current = 0;
										}
									},
									init: function() {
										// Set Instance
										var dzInstance = this;
										
										// Update Template
										dzInstance.options.previewTemplate = $.ajax('/user/preview-template/images', {
											method: 'post',
											dataType: 'html',
											async: false,
											error: function(xhr) {
												// Handle Error
												displayMessage(xhr.status + ': ' + xhr.statusText + ' (' + this.url + ')', 'alert');
												
												// Reset
												dzInstance.removeAllFiles(true);
											}
										}).responseText;
										
										// Check Complete
										this.on('complete', function() {
											if(this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
												// Console Info
												console.info('All Uploads Complete');
												
												// Variable Defaults
												var bootstrapData = previewsModal.data('bs.modal');
												
												// Release Modal Backdrop
												previewsModal.data('bs.modal', $.extend(true, bootstrapData, {
													_config: $.extend(true, bootstrapData._config, {
														backdrop: true,
														keyboard: true
													})
												}));
											}
										});
									},
									error: function(file, message) {
										// Set Instance
										var dzInstance = this;
										
										// Handle Error
										displayMessage(message, 'alert');
										
										// Reset
										dzInstance.removeAllFiles(true);
									}
								});
							}).one('hide.bs.modal', function() {
								// Destroy Modal
								$(this).remove();
								
								// Reload
								initTable.reload();
							}).modal();
						}
					});
					break;
				default:
					console.error('Unknown Custom Action:', action);
			}
		});
		
		// Init SortableJS
		$('div[data-sortablejs="parent"]').each(function() {
			new Sortable(this, {
				animation: 150,
				chosenClass: 'sortable-chosen',
				dataIdAttr: 'data-sortablejs-id',
				dragClass: 'sortable-drag',
				draggable: 'div[data-sortablejs="child"]',
				easing: 'cubic-bezier(1, 0, 0, 1)',
				forceFallback: true,
				ghostClass: 'sortable-ghost',
				handle: '.sortable-item__handler',
				bubbleScroll: true,
				onStart: function() {
					// Add Dragging Class to Body
					$('body').addClass('dragging');
				},
				onEnd: function() {
					// Handle Ajax
					$.ajax('/user/sort/images', {
						data: { rows: this.toArray().filter(Number).reverse() },
						dataType: 'json',
						method: 'post',
						async: true,
						success: function(response) {
							// Switch Status
							switch(response.status) {
								case 'success':
									break;
								case 'error':
									displayMessage(response.message || Object.keys(response.errors).map(function(key) {
										return response.errors[key];
									}).join('<br>'), 'alert', null);
									break;
								default:
									displayMessage(response.message || 'Something went wrong.', 'alert');
							}
						}
					});
					
					// Remove Dragging Class from Body
					$('body').removeClass('dragging');
				},
				onMove: function(sortableJsEvent) {
					return !!$(sortableJsEvent.related).data('sortablejs-id');
				}
			});
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

