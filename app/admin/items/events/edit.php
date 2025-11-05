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
 * @var Admin\User        $admin
 */

// Variable Defaults
$page_title  = 'Edit Event';
$image_sizes = array(
        'landscape' => array('width' => 900, 'height' => 600),
        'portrait'  => array('width' => 600, 'height' => 900),
        'square'    => array('width' => 900, 'height' => 900),
        'poster'    => array('width' => 900, 'height' => 0)
);

// Set Item
$item = Items\Event::Fetch(Database::Action(
        "SELECT * FROM `events` WHERE `id` = :table_id",
        array('table_id' => $dispatcher->getTableId())
));

// Check Item
if (is_null($item)) Admin\Render::ErrorDocument(404);

// Set Template
$template = reset($image_sizes);

// Set Categories
if (Admin\Categories('events')) {
    $categories = Database::Action(
            "SELECT `id`, `name` FROM `categories` WHERE `table_name` = :table_name",
            array('table_name' => 'events')
    )->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}

// Set Packages
$packages = Database::Action(
        "SELECT `id`, CONCAT_WS(' ', `name`, CONCAT('($', FORMAT(`price`, 2), ')')) 
     FROM `event_packages` WHERE `published` = 1 
     ORDER BY `name`, `price` DESC"
)->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);

// ✅ Get total number of events to build the dropdown
$totalEvents = (int) Database::Action("SELECT COUNT(*) FROM `events`")->fetchColumn();

// ✅ Get current sort order safely from database
$currentSort = (int) Database::Action(
        "SELECT `sort_order` FROM `events` WHERE `id` = :id",
        ['id' => $item->getId()]
)->fetchColumn();


// Start Header
include('includes/header.php');
?>

<main class="page-content">
    <div id="page-title-btn">
        <h1><?php echo $page_title; ?></h1>
    </div>

    <div id="ajax-wrapper">
        <form class="form-horizontal content-module">

            <?php if (!empty($categories)): ?>
                <div class="form-group">
                    <label for="category-id">Category</label>
                    <div class="select-wrap form-control">
                        <select id="category-id" name="category_id" data-value="<?php echo $item->getCategoryId(); ?>">
                            <?php foreach ($categories as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-box"></div>
                    </div>
                </div>
            <?php endif; ?>

            <h2>Class Type</h2>
            <div class="form-group">
                <label for="class-type">Class Type</label>
                <div class="select-wrap form-control">
                    <select id="class-type" name="class_type" data-value="<?php echo $item->getClassType(); ?>">
                        <?php foreach (Items\Event::Options('class_types') as $value => $label): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="select-box"></div>
                </div>
            </div>

            <h2>Search Engine Optimization:</h2>

            <div class="form-group">
                <label for="page-title-input">Page Title</label>
                <div class="feedback-wrap">
                    <input id="page-title-input" class="form-control" type="text" name="page_title"
                           maxlength="255" value="<?php echo $item->getEncoded('page_title'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="page-description">Page Description</label>
                <div class="feedback-wrap">
                    <input id="page-description" class="form-control" type="text" name="page_description"
                           maxlength="255" value="<?php echo $item->getEncoded('page_description'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="heading">Heading</label>
                <div class="feedback-wrap">
                    <input id="heading" class="form-control" type="text" name="heading" maxlength="255"
                           value="<?php echo $item->getEncoded('heading'); ?>">
                </div>
            </div>

            <hr class="my-4">

            <h2>Page Content:</h2>

            <div class="form-group">
                <label for="content">Content</label>
                <div class="feedback-wrap">
                    <textarea id="content" class="form-control" name="content" rows="20"><?php echo $item->getContent(); ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="youtube-id">YouTube ID</label>
                <div class="feedback-wrap">
                    <input id="youtube-id" class="form-control" type="text" name="youtube_id" maxlength="255"
                           value="<?php echo $item->getEncoded('youtube_id'); ?>">
                </div>
            </div>

            <hr class="my-4">

            <h2>Event Details</h2>

            <div class="form-group">
                <label for="teacher_id">Teacher</label>
                <div class="select-wrap form-control">
                    <select name="teacher_id" id="teacher-id">
                        <option value="N/A">None</option>
                        <?php foreach (Items\Event::getApprovedTeachers() as $teacher): ?>
                            <?php
                            $selected = ($item?->getTeacherId() === (int) $teacher['id']) ? 'selected' : '';
                            $fullName = trim($teacher['first_name'] . ' ' . $teacher['last_name']);
                            ?>
                            <option value="<?php echo $teacher['id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($fullName, ENT_QUOTES); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="select-box"></div>
                </div>
            </div>

            <!-- ✅ NEW: Sort Order Dropdown -->
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <div class="select-wrap form-control">
                    <select id="sort_order" name="sort_order" data-value="<?php echo $currentSort; ?>">
                        <option value="0">0 (Unsorted / Auto)</option>
                        <?php for ($i = 1; $i <= $totalEvents; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($i === $currentSort) ? 'selected' : ''; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <div class="select-box"></div>
                </div>
                <small class="form-text text-muted">Determines the display order of this event. Lower numbers appear first.</small>
            </div>
            <!-- End Sort Order Dropdown -->

            <div class="form-group">
                <label for="event-dates">Event Date(s)</label>
                <div class="feedback-wrap">
                    <input id="event-dates" class="form-control" type="text" name="event_dates"
                           placeholder="- None -" value="<?php echo $item->getEncoded('event_dates'); ?>">
                </div>
            </div>

            <hr>
			
			<h2>Event Time(s)</h2>
			
			<div class="form-group">
				<label>Current Time:</label>
				<p class="form-control-plaintext">
					<?php echo htmlentities($item->getEventTimes()); ?>
				</p>
			</div>
			
			<!-- Optional note -->
			<small class="form-text text-muted mb-2">You can leave the dropdowns blank to keep the current time.</small>
			<h3>From</h3>
			<div class="form-row">
				<div class="form-group col-4">
					<label for="event_hour">Hour</label>
					
					<div class="select-wrap form-control">
						<select name="event_hour_from" id="event-hour">
							<option value="">--</option>
							<?php foreach(Items\Event::Options('hours') as $val => $label): ?>
								<option value="<?php echo $val; ?>"<?php if(($event_hour ?? '') === $val) echo ' selected'; ?>>
									<?php echo $label; ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div class="select-box"></div>
					</div>
				</div>
				
				<div class="form-group col-4">
					<label for="event_minute">Minute</label>
					<div class="select-wrap form-control">
						<select name="event_minute_from" id="event-minute">
							<option value="">--</option>
							<?php foreach(Items\Event::Options('minutes') as $val => $label): ?>
								<option value="<?php echo $val; ?>"<?php if(($event_minute ?? '') === $val) echo ' selected'; ?>>
									<?php echo $label; ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div class="select-box"></div>
					</div>
				</div>
				
				<div class="form-group col-4">
					<label for="event_meridian">AM/PM</label>
					<div class="select-wrap form-control">
						<select name="event_meridian_from" id="event-meridian">
							<option value="">--</option>
							<?php foreach(Items\Event::Options('meridians') as $val => $label): ?>
								<option value="<?php echo $val; ?>"<?php if(($event_meridian ?? '') === $val) echo ' selected'; ?>>
									<?php echo $label; ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div class="select-box"></div>
					</div>
				</div>
			</div>
			
			<h3>To</h3>
			<div class="form-row">
				<div class="form-group col-4">
					<label for="event_hour_to">Hour</label>
					
					<div class="select-wrap form-control">
						<select name="event_hour_to" id="event-hour">
							<option value="">--</option>
							<?php foreach(Items\Event::Options('hours') as $val => $label): ?>
								<option value="<?php echo $val; ?>"<?php if(($event_hour ?? '') === $val) echo ' selected'; ?>>
									<?php echo $label; ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div class="select-box"></div>
					</div>
				</div>
				
				<div class="form-group col-4">
					<label for="event_minute_to">Minute</label>
					<div class="select-wrap form-control">
						<select name="event_minute_to" id="event-minute">
							<option value="">--</option>
							<?php foreach(Items\Event::Options('minutes') as $val => $label): ?>
								<option value="<?php echo $val; ?>"<?php if(($event_minute ?? '') === $val) echo ' selected'; ?>>
									<?php echo $label; ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div class="select-box"></div>
					</div>
				</div>
				
				<div class="form-group col-4">
					<label for="event_meridian_to">AM/PM</label>
					<div class="select-wrap form-control">
						<select name="event_meridian_to" id="event-meridian">
							<option value="">--</option>
							<?php foreach(Items\Event::Options('meridians') as $val => $label): ?>
								<option value="<?php echo $val; ?>"<?php if(($event_meridian ?? '') === $val) echo ' selected'; ?>>
									<?php echo $label; ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div class="select-box"></div>
					</div>
				</div>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label for="location">Location</label>
				
				<div class="feedback-wrap">
					<input id="location" class="form-control" type="text" name="location" maxlength="75" value="<?php echo $item->getEncoded('location'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="price-text">Price Text</label>
				
				<div class="feedback-wrap">
					<input id="price-text" class="form-control" type="text" name="price_text" maxlength="20" value="<?php echo $item->getEncoded('price_text'); ?>">
				</div>
			</div>
			
			<div class="form-group">
				<label for="event-packages-ids">Packages</label>
				
				<div class="frame pb-4">
					<select id="event-packages-ids" name="event_package_ids[]" data-values="<?php echo $item->getPackagesIdsJson(JSON_HEX_QUOT); ?>" multiple>
						<?php foreach($packages as $value => $label): ?>
							<option value="<?php echo $value; ?>">
								<?php echo $label; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			
			<hr class="my-4">
			
			<fieldset id="image-cropper-wrapper">
				<h2>Upload Image:</h2>
				
				<div class="frame p-4 p-lg-5">
					<div id="image-cropper" class="form-group mb-0">
						<?php if($item->hasImage()): ?>
							<div id="image-cropper-uploaded" class="row justify-content-center">
								<input type="hidden" name="filename" value="<?php echo $item->getEncoded('filename'); ?>">
								
								<?php foreach($image_sizes as $type => $format): ?>
									<?php $croppable = ($format['width'] && $format['height']); ?>
									
									<div class="col-lg-6 col-xl-4">
										<a class="image-cropper-crop" href="#" data-cropper-action="crop">
											<h3><?php echo Helpers::PrettyTitle($type); ?></h3>
											
											<img class="img-fluid"
												src="<?php echo sprintf("/files/events/%s/%s?v=%d", $type, $item->getFilename(), time()); ?>"
												data-cropper-aspect="<?php echo $croppable ? ($format['width'] / $format['height']) : 0; ?>"
												data-cropper-format="<?php echo htmlentities(json_encode($format), ENT_QUOTES); ?>"
												data-cropper-source="<?php echo $item->getImage(); ?>"
												data-cropper-type="<?php echo $type; ?>">
										</a>
									</div>
								<?php endforeach; ?>
							</div>
							<div class="row justify-content-center my-3">
								<div class="col-lg-6 col-xl-5">
									<button class="btn btn-outline btn-block my-2" type="button" data-cropper-action="view">
										<i class="far fa-search-plus"></i>
										View Original
									</button>
								</div>
								<div class="col-lg-6 col-xl-5">
									<button class="btn btn-warning btn-block my-2" type="button" data-cropper-action="delete">
										<i class="far fa-trash"></i>
										Delete Image
									</button>
								</div>
							</div>
						<?php else: ?>
							<div id="image-cropper-uploader" class="dropzone-qd mx-auto d-flex align-items-center justify-content-center embed-responsive embed-responsive-16by9" data-template="<?php echo htmlentities(json_encode($template), ENT_QUOTES); ?>">
								<div class="dz-message d-flex flex-column">
									<i class="fas fa-cloud-upload-alt text-muted"></i>
									Upload Image
								</div>
							</div>
						<?php endif; ?>
						
						<div class="row justify-content-center mt-3">
							<div class="col-sm-12 col-lg-8 col-xl-6 mb-2">
								<div class="form-group">
									<label for="filename-alt">Image Alt:</label>
									
									<div class="feedback-wrap">
										<input class="form-control" type="text" name="filename_alt" id="filename-alt" value="<?php echo $item->getEncoded('filename_alt'); ?>">
									</div>
									
									<p class="note">
										<strong>Note:</strong> Image alt text is used for ADA compliance. Provide an accurate description of the image provided.
									</p>
								</div>
							</div>
						</div>
						
						<p class="note text-center mb-0"><strong>Note:</strong> Only images ending in jpeg, jpg and png are supported.</p>
					</div>
				</div>
			</fieldset>
			
			<div class="form-collapse">
				<div id="advanced-options" class="collapse">
					<div class="form-group">
						<label for="page-url">Page URL</label>
						
						<div class="feedback-wrap">
							<input id="page-url" class="form-control" type="text" name="page_url" maxlength="255" value="<?php echo $item->getEncoded('page_url'); ?>">
						</div>
					</div>
					
					<div class="form-group">
						<label for="accepting_rsvp">Allow RSVP?</label>
						
						<div class="select-wrap form-control">
							<select id="accepting_rsvp" name="accepting_rsvp" data-value="<?php echo (int)$item->isAcceptingRsvp(); ?>">
								<?php foreach(array('No', 'Yes') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="display-rsvps">Display RSVP List?</label>
						
						<div class="select-wrap form-control">
							<select id="display-rsvps" name="display_rsvps" data-value="<?php echo (int)$item->isDisplayRsvps(); ?>">
								<?php foreach(array('No', 'Yes') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="published">Published?</label>
						
						<div class="select-wrap form-control">
							<select id="published" name="published" data-value="<?php echo (int)$item->isPublished(); ?>">
								<?php foreach(array(1 => 'Yes', 0 => 'No') as $value => $label): ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="published-date">Published Date:</label>
						
						<div class="feedback-wrap">
							<input class="form-control" type="text" name="published_date" id="published-date" value="<?php echo $item->getEncoded('published_date'); ?>">
						</div>
					</div>
				</div>
				
				<hr>
				
				<div class="text-center">
					<a href="#" data-target="#advanced-options" class="btn btn-outline" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="advanced-options">
						Advanced Options
					</a>
				</div>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/events">
						<i class="fal fa-ban"></i> Cancel
					</a>
				</div>
			</div>
		</form>
	</div>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Variable Defaults
		var ajaxForm   = $('#ajax-wrapper');
		var imageSizes = null || <?php echo json_encode($image_sizes); ?>;
		var item       = null || <?php echo $item->toJson(); ?>;
		
		// Init Cropper
		$('#image-cropper-wrapper').cropper({
			acceptedFiles: 'image/png,image/jpeg',
			backgroundColor: '#FFFFFF',
			template: imageSizes[Object.keys(imageSizes)[0]],
			cropperModalUrl: '/modals/admin/cropper',
			cropUrl: '/ajax/admin/cropper/crop',
			deleteUrl: '/ajax/admin/cropper/delete',
			maxFilesize: settings.maxFilesize.MB,
			progressBarUrl: '/modals/admin/cropper/progress-bar',
			stageUrl: '/ajax/admin/cropper/stage',
			uploadUrl: '/ajax/admin/cropper/upload',
			additionalData: { table_name: 'events', sizes: imageSizes, column: 'filename', item: item },
			onView: function(cropper, instance) {
				// Variable Defaults
				var data = ($(instance).find('[data-cropper-source]').length ? $(instance).find('[data-cropper-source]') : $(instance)).data();
				
				// Show Fancybox
				$.fancybox.open([{ src: data.cropperSource }]);
			}
		});
		
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
		
		// Init Flatpickr (Event Dates)
		flatpickr('#event-dates', {
			mode: 'range',
			altInput: true,
			altFormat: 'M j',
			dateFormat: 'Y-m-d',
			plugins: [new confirmDatePlugin({
				confirmIcon: '<i class="fa-solid fa-circle-check ml-1"></i>',
				confirmText: 'Okay!',
				showAlways: true,
				theme: 'light'
			})]
		});
		
		// Handle Submission
		ajaxForm.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();
			
			// Handle Ajax
			$.ajax({
				data: Object.assign($(this).serializeObject(), { item: item }),
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							location.href = '/user/view/events';
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
		});
	});
</script>

<?php include('includes/body-close.php'); ?>

