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
	$page_title = 'Add Event Package';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<div class="form-group">
				<label for="name">Name</label>
				
				<div class="feedback-wrap">
					<input id="name" class="form-control" type="text" name="name" maxlength="255">
				</div>
			</div>
			
			<div class="form-group">
				<label for="price">Price</label>
				
				<div class="feedback-wrap">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<i class="fal fa-dollar-sign"></i>
							</span>
						</div>
						<input id="price" class="form-control" type="text" name="price" data-format="number">
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="taxable">Taxable?</label>
				
				<div class="select-wrap form-control">
					<select id="taxable" name="taxable">
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
				<label for="is_bogo">BOGO?</label>
				
				<div class="select-wrap form-control">
					<select id="is_bogo" name="is_bogo">
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
				<label for="musical">Music Event?</label>
				
				<div class="select-wrap form-control">
					<select id="musical" name="musical">
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
				<label for="seatable">Assigned Seating?</label>
				
				<div class="select-wrap form-control">
					<select id="seatable" name="seatable">
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
				<label for="stock-quantity">How Many Seats/Available Tickets?</label>
				
				<div class="select-wrap form-control">
					<select id="stock-quantity" name="stock-quantity">
						<?php foreach(array_combine(range(0, 100), range(0, 100)) as $value => $label): ?>
							<option value="<?php echo $value; ?>">
								<?php echo $label; ?>
							</option>
						<?php endforeach; ?>
					</select>
					<div class="select-box"></div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="merchant">Merchant</label>
				
				<div class="select-wrap form-control">
					<select id="merchant" name="merchant">
						<?php foreach(Items\Events\Package::FormOptions('merchants') as $value => $label): ?>
							<option value="<?php echo $value; ?>">
								<?php echo $label; ?>
							</option>
						<?php endforeach; ?>
					</select>
					<div class="select-box"></div>
				</div>
			</div>
			
			<div class="form-collapse">
				<div id="advanced-options" class="collapse">
					<div class="form-group">
						<label for="published">Published?</label>
						
						<div class="select-wrap form-control">
							<select id="published" name="published">
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
							<input id="published-date" class="form-control" type="text" name="published_date">
						</div>
					</div>
				</div>
				
				<hr>
				
				<div class="text-center">
					<a class="btn btn-outline" href="#" aria-controls="advanced-options" aria-expanded="false" data-target="#advanced-options" data-toggle="collapse" role="button">
						Advanced Options
					</a>
				</div>
			</div>
			
			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>
					
					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/event-packages">
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
		var ajaxForm = $('#ajax-wrapper');

		// Init Flatpickr
		flatpickr('#published-date', {
			mode: 'single',
			altInput: true,
			altFormat: 'M j, Y',
			dateFormat: 'Y-m-d',
			defaultDate: new Date(),
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
				data: Object.assign($(this).serializeObject(), {}),
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					// Switch Status
					switch(response.status) {
						case 'success':
							location.href = '/user/view/event-packages';
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

