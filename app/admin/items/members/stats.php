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
	
	// Set Total
	$totals = Database::Action("SELECT 'MEMBERS (TOTAL)' AS `label`, COUNT(*) AS `total` FROM `members` UNION ALL SELECT 'MEMBERS (APPROVED)', COUNT(*) FROM `members` WHERE `approved` IS TRUE AND `verified` IS TRUE UNION ALL SELECT 'MEMBERS (PENDING)', COUNT(*) FROM `members` WHERE `approved` IS FALSE AND `verified` IS TRUE UNION ALL SELECT 'MEMBERS (VERIFIED)', COUNT(*) FROM `members` WHERE `verified` IS TRUE UNION ALL SELECT 'MEMBERS (COUPLES)', COUNT(*) FROM `members` WHERE `couple` IS TRUE UNION ALL SELECT 'MEMBERS (SINGLES)', COUNT(*) FROM `members` WHERE `couple` IS FALSE UNION ALL SELECT 'POSTS (TOTAL)', COUNT(*) FROM `member_posts` UNION ALL SELECT 'POSTS (APPROVED)', COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE UNION ALL SELECT 'POSTS (PENDING)', COUNT(*) FROM `member_posts` WHERE `approved` IS FALSE UNION ALL SELECT 'POSTS (PRIVATE)', COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND `visibility` = 'FRIENDS' UNION ALL SELECT 'POSTS (PUBLIC)', COUNT(*) FROM `member_posts` WHERE `approved` IS TRUE AND `visibility` = 'MEMBERS' UNION ALL SELECT 'POSTS (CONTEST)', COUNT(DISTINCT `member_post_id`) FROM `member_post_type_social` WHERE `member_contest_id` IS NOT NULL")->fetchAll(PDO::FETCH_KEY_PAIR);
	
	// Set Title
	$page_title = 'Stats: Membership';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>
		
		<div class="row">
			<div class="col-12 border-bottom mb-3 pb-3">
				<div class="row">
					<?php foreach($totals as $label => $total): ?>
						<div class="col-md-2 col-lg-1 text-center">
							<strong><?php echo $total; ?></strong>
							<br>
							<small><?php echo str_replace(' ', '<br>', $label); ?></small>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			
			<div class="col-12">
				<ul class="nav nav-tabs justify-content-center" id="stats-tab" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="all-time-tab" data-bs-toggle="tab" data-bs-target="#all-time" type="button" role="tab" aria-controls="all-time" aria-selected="true">
							All Time
						</button>
					</li>
					
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="six-months-tab" data-bs-toggle="tab" data-bs-target="#six-months" type="button" role="tab" aria-controls="six-months" aria-selected="false">
							6 Months
						</button>
					</li>
					
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="thirty-days-tab" data-bs-toggle="tab" data-bs-target="#thirty-days" type="button" role="tab" aria-controls="thirty-days" aria-selected="false">
							30 Days
						</button>
					</li>
				</ul>
				
				<div id="stats-tab-content" class="tab-content p-3 bg-white border border-top-0">
					<div class="tab-pane show active" id="all-time" role="tabpanel" aria-labelledby="all-time-tab" tabindex="0">
						<div id="all-time"></div>
					</div>
					
					<div class="tab-pane" id="six-months" role="tabpanel" aria-labelledby="six-months-tab" tabindex="0">
						<div id="six-months"></div>
					</div>
					
					<div class="tab-pane" id="thirty-days" role="tabpanel" aria-labelledby="thirty-days-tab" tabindex="0">
						<div id="thirty-days"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<script>
	$(function() {
		// Tab Functionality
		Array.prototype.forEach.call(document.querySelectorAll('.nav-link'), function(tab) {
			tab.addEventListener('click', function(event) {
				event.preventDefault();

				// Remove "active" class from all tabs
				var allTabs = document.querySelectorAll('.nav-link');
				Array.prototype.forEach.call(allTabs, function(tab) {
					tab.classList.remove('active');
				});

				// Add "active" class to the clicked tab
				this.classList.add('active');

				var target           = document.querySelector(this.getAttribute('data-bs-target'));
				var activeTabContent = document.querySelector('.tab-pane.show.active');

				// Initialize the statsTab variable inside the event listener
				var statsTab = new bootstrap.Tab(target);

				// Activate the clicked tab
				statsTab.show();

				activeTabContent.classList.remove('show', 'active');
				target.classList.add('show', 'active');
			});
		});

		// Fetch Sales Data
		$.ajax({
			method: 'post',
			dataType: 'json',
			success: function(response) {
				// Switch Status
				switch(response.status) {
					case 'success':
						// Loop Through Charts
						Object.keys(response.charts).forEach(function(container_id) {
							// Variable Defaults
							var container = document.getElementById(container_id);

							// Loop Through Data
							Object.keys(response.charts[container_id]).forEach(function(label) {
								// Create Canvas
								var canvas = document.createElement('canvas');

								// Append Canvas
								container.appendChild(canvas);

								// Create Chart
								new Chart(canvas.getContext('2d'), {
									type: 'line',
									data: {
										datasets: response.charts[container_id][label]
									},
									options: {
										responsive: true,
										scales: {
											y: {
												beginAtZero: true,
												ticks: {
													callback: function(value, index, values) {
														return value.toLocaleString('en-US');
													}
												}
											}
										},
										plugins: {
											legend: {
												position: 'top'
											},
											title: {
												display: true,
												text: label
											},
											tooltip: {
												callbacks: {
													label: function(context) {
														return context.parsed.y.toLocaleString('en-US');
													},
													title: function(context) {
														var dateParts = context[0].label.split('-');
														var year      = dateParts[0];
														var month     = parseInt(dateParts[1], 10) - 1;

														return (new Date(year, month)).toLocaleString('en-US', {
															month: 'long',
															year: 'numeric'
														});
													}
												}
											}
										}
									},
									plugins: [
										{
											beforeInit: function(chart) {
												var colors   = ['red', 'blue', 'yellow', 'green', 'purple', 'orange', 'pink', 'cyan'];
												var datasets = chart.data.datasets;

												datasets.forEach(function(dataset, index) {
													var color                = colors[index % colors.length];
													dataset.borderColor      = Chart.helpers.color(color).rgbString();
													dataset.backgroundColor  = Chart.helpers.color(color).alpha(0.5).rgbString();
													dataset.pointStyle       = 'circle';
													dataset.pointRadius      = 10;
													dataset.pointHoverRadius = 15;
												});
											}
										}
									]
								});
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
	});
</script>

<?php include('includes/body-close.php'); ?>

