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
	$page_title = 'Manage PDFs';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
			
			<button class="btn btn-primary" data-custom-action="add">
				<i class="fa fa-plus"></i>
				<span class="d-none d-sm-inline">Add File(s)</span>
			</button>
		</div>
		
		<span class="d-block mb-3">
			<strong class="text-muted">Legend:</strong>
			<span class="badge bg-warning text-dark">Unpublished</span>
		</span>
		
		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered nowrap w-100" data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
					<thead>
						<tr>
							<th>ID</th>
							<th>Filename</th>
							<th>Title</th>
							<th>Description</th>
							<th>Timestamp</th>
							<th>Options</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var tableView    = $('#view-table');
		var tableElement = $('#data-table');
		var tableOptions = tableElement.data('tableOptions');
		
		// Init Data Tables
		tableElement.DataTable({
			order: [],
			pageLength: 250,
			paging: true,
			searching: true,
			stateSave: true,
			info: true,
			ordering: false,
			rowReorder: { selector: 'td:not(.no-drag)', dataSrc: 'item.position', update: false },
			ajax: {
				data: { table_name: tableOptions.table_name.replace('-', '_'), table_id: tableOptions.table_id },
				method: 'post',
				complete: function(jqXHR) {
					// Set Response
					var response = jqXHR.responseJSON;
					
					// Switch Status
					switch(response.status) {
						case 'success':
							// Console Message
							console.info(response.message);
							break;
						case 'error':
							displayMessage(response.message || Object.keys(response.errors).map(function(key) {
								return response.errors[key];
							}).join('<br>'), 'alert', null);
							break;
						default:
							displayMessage(response.message || 'Something went wrong.', 'alert');
					}
				},
				async: false
			},
			columns: [
				{ data: 'id', className: 'all' },
				{ data: { _: 'item.filename', display: 'filename' }, className: 'all' },
				{ data: { _: 'item.title', display: 'title' }, className: 'all' },
				{ data: { _: 'item.description', display: 'description' }, className: 'all' },
				{ data: 'timestamp', className: 'all' },
				{ data: 'options', className: 'all no-drag' }
			],
			responsive: {
				details: {
					display: $.fn.dataTable.Responsive.display.modal({
						header: function(row) {
							var data = row.data();
							
							return 'Details for PDF ID #' + data.id;
						}
					}),
					renderer: $.fn.dataTable.Responsive.renderer.tableAll({
						tableClass: 'table'
					})
				}
			},
			rowCallback: function(row) {
				// Variable Defaults
				var tableRow     = $(row);
				var currentTable = $(this);
				var dataTable    = currentTable.DataTable();
				
				// Append ID
				tableRow.prop('id', dataTable.row(row).data().id);
				
				// Render Row States
				!dataTable.row(row).data().published && tableRow.addClass('table-warning');
				
				// Bind Click Event to Action
				tableRow.off().on('click', '[data-action]', function(event) {
					// Check Event
					if(event) {
						// Prevent Default
						event.preventDefault();
						
						// Variable Defaults
						var currentRow = event.delegateTarget;
						var action     = $(this).data('action');
						var data       = dataTable.row(currentRow).data();
						
						// Switch Action
						switch(action) {
							case 'delete':
								// Confirm Deletion
								if(confirm('Are you sure you want to delete this?')) {
									// Handle Ajax
									$.ajax('/user/delete/pdfs/' + data.item.id, {
										data: JSON.stringify({ table_name: tableOptions.table_name.replace('-', '_'), table_id: tableOptions.table_id }),
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
													dataTable.ajax.reload();
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
								$.ajax('/user/edit/pdfs/' + tableOptions.table_name + '/' + tableOptions.table_id + '/' + data.item.id, {
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
											$.ajax('/user/edit/pdfs/' + tableOptions.table_name + '/' + tableOptions.table_id + '/' + data.item.id, {
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
															displayMessage(response.message || 'Success!', 'success', function() {
																// Console Message
																console.info(response.message);
																
																// Reload
																$(this).one('hide.bs.modal', function() {
																	dataTable.ajax.reload();
																});
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
							default:
								console.error('Unknown Action:', action);
						}
					}
				});
			}
		});
		
		// Bind Custom Action Events
		tableView.on('click', '[data-custom-action]', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Variable Defaults
			var action    = $(this).data('customAction');
			var dataTable = tableElement.DataTable();
			
			// Switch Action
			switch(action) {
				case 'add':
					// Create Previews Container Modal
					$.ajax('/user/previews-container/pdfs', {
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
									url: '/user/upload/pdfs',
									acceptedFiles: 'application/pdf',
									previewsContainer: '#bulk-file-uploader-previews-container',
									createImageThumbnails: false,
									paramName: 'pdf',
									maxFilesize: settings.maxFilesize,
									parallelUploads: 1,
									params: {
										table_name: tableOptions.table_name.replace('-', '_'),
										table_id: tableOptions.table_id
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
										dzInstance.options.previewTemplate = $.ajax('/user/preview-template/pdfs', {
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
								dataTable.ajax.reload();
							}).modal();
						}
					});
					break;
				default:
					console.error('Unknown Custom Action:', action);
			}
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

