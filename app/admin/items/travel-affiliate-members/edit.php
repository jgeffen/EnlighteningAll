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
use Items\Enums\Options;

// Variable Defaults
$page_title = 'Edit Travel Affiliate Member';
$member  = TravelAffiliateMembership::Init($dispatcher->getTableId());

// Check Member
if (is_null($member)) Admin\Render::ErrorDocument(404);

// Start Header
include('includes/header.php');
?>

<main class="content">
	<br />
	<div id="page-title-btn">
		<h1><?php echo $page_title; ?></h1>
	</div>

	<?php AffiliateTransactionSubMenu($member); ?>

	<div id="ajax-wrapper">
		<form class="form-horizontal content-module">
			<div class="card mb-4">
				<div class="card-header">
					<h2 class="mb-0">Account Information</h2>
				</div>

				<div class="card-body">
					<div class="form-group">
						<label for="username">Username</label>
						<div class="feedback-wrap">
							<input id="username" class="form-control" type="text" name="username" maxlength="16" value="<?php echo $member->getEncoded('username'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="email">Email</label>
						<div class="feedback-wrap">
							<input id="email" class="form-control" type="email" name="email" maxlength="64" value="<?php echo $member->getEncoded('email'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="first_name">First Name</label>
						<div class="feedback-wrap">
							<input id="first_name" class="form-control" type="text" name="first_name" maxlength="16" value="<?php echo $member->getEncoded('first_name'); ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="last_name">Last Name</label>
						<div class="feedback-wrap">
							<input id="last_name" class="form-control" type="text" name="last_name" maxlength="16" value="<?php echo $member->getEncoded('last_name'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="phone">Phone</label>
						<div class="feedback-wrap">
							<input id="phone" class="form-control" type="text" name="phone" maxlength="14" value="<?php echo $member->getEncoded('phone'); ?>" data-format="phone">
						</div>
					</div>

					<div class="form-group">
						<label for="travel_agency">Travel Agency</label>
						<div class="feedback-wrap">
							<input id="travel_agency" class="form-control" type="text" name="travel_agency" maxlength="255" value="<?php echo $member->getEncoded('travel_agency'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="ein_number">EIN Number</label>
						<div class="feedback-wrap">
							<input id="ein_number" class="form-control" type="text" name="ein_number" maxlength="32" value="<?php echo $member->getEncoded('ein_number'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="approved">Approved</label>
						<div class="select-wrap form-control">
							<select id="approved" name="approved" data-value="<?php echo (int)$member->isApproved(); ?>">
								<?php foreach (Options\YesNo::options() as $value => $label) : ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>

					<div class="form-group">
						<label for="banned">Banned</label>
						<div class="select-wrap form-control">
							<select id="banned" name="banned" data-value="<?php echo (int)$member->isBanned(); ?>">
								<?php foreach (Options\YesNo::options() as $value => $label) : ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>

					<div class="form-group">
						<label for="verified">Verified</label>
						<div class="select-wrap form-control">
							<select id="verified" name="verified" data-value="<?php echo (int)$member->isVerified(); ?>">
								<?php foreach (Options\YesNo::options() as $value => $label) : ?>
									<option value="<?php echo $value; ?>">
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="select-box"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="card mb-4">
				<div class="card-header">
					<h2 class="mb-0">Address Information:</h2>
				</div>

				<div class="card-body">
					<div class="form-group">
						<label for="address_line_1">Line 1</label>
						<div class="feedback-wrap">
							<input id="address_line_1" class="form-control" type="text" name="address_line_1" maxlength="64" value="<?php echo $member->getEncoded('address_line_1'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="address_line_2">Line 2</label>
						<div class="feedback-wrap">
							<input id="address_line_2" class="form-control" type="text" name="address_line_2" maxlength="16" value="<?php echo $member->getEncoded('address_line_2'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="address_city">City</label>
						<div class="feedback-wrap">
							<input id="address_city" class="form-control" type="text" name="address_city" maxlength="32" value="<?php echo $member->getEncoded('address_city'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="address_country">Country</label>
						<div class="feedback-wrap">
							<input id="address_country" class="form-control" type="text" name="address_country" maxlength="2" value="<?php echo $member->getEncoded('address_country'); ?>">
						</div>

						<p class="note"><strong>Note:</strong> Please only use <a href="https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes" target="_blank">Alpha-2 ISO 3166-1 Codes</a></p>
					</div>

					<div class="form-group">
						<label for="address_state">State</label>
						<div class="feedback-wrap">
							<input id="address_state" class="form-control" type="text" name="address_state" maxlength="2" value="<?php echo $member->getEncoded('address_state'); ?>">
						</div>

						<p class="note"><strong>Note:</strong> Please only use <a href="https://en.wikipedia.org/wiki/ISO_3166-2:US" target="_blank">2-Digit ISO 3166-2 Codes</a></p>
					</div>

					<div class="form-group">
						<label for="address_zip_code">Zip Code</label>
						<div class="feedback-wrap">
							<input id="address_zip_code" class="form-control" type="text" name="address_zip_code" maxlength="5" value="<?php echo $member->getEncoded('address_zip_code'); ?>" data-format="zip">
						</div>
					</div>

					<div class="form-group">
						<label for="ticket_commission_rate">Ticket Commission Rate</label>
						<div class="feedback-wrap">
							<input id="ticket_commission_rate" class="form-control" type="number" name="ticket_commission_rate" maxlength="6" min="0" max="100" step="0.01" value="<?php echo $member->getEncoded('ticket_commission_rate'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="room_commission_rate">Room Commission Rate</label>
						<div class="feedback-wrap">
							<input id="room_commission_rate" class="form-control" type="number" name="room_commission_rate" maxlength="6" min="0" max="100" step="0.01" value="<?php echo $member->getEncoded('room_commission_rate'); ?>">
						</div>
					</div>
				</div>
			</div>

			<hr>
			<div class="card mb-4">
				<div class="card-header">
					<h2 class="mb-0">Signatures</h2>
				</div>

				<div class="card-body">

					<div class="form-group">
						<label for="terms_privacy_signature">Affilaite Terms & Privacy Signature</label>
						<div class="feedback-wrap">
							<span id="terms_privacy_signature" class="form-control" name="terms_privacy_signature">
								<?php echo $member->getEncoded('terms_privacy_signature'); ?>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label for="affiliate_terms_conditions_signature">Affilaite Terms & Conditions Signature</label>
						<div class="feedback-wrap">
							<span id="affiliate_terms_conditions_signature" class="form-control" name="affiliate_terms_conditions_signature">
								<?php echo $member->getEncoded('affiliate_terms_conditions_signature'); ?>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label for="admin_approval_signature">Admin Approval Signature</label>
						<div class="feedback-wrap">
							<input type="text" id="admin_approval_signature" class="form-control" name="admin_approval_signature" maxlength="64" value="<?php echo $member->getEncoded('admin_approval_signature'); ?>">
						</div>
					</div>


				</div>
			</div>

			<div class="form-group">
				<label for="notes">Notes</label>
				<div class="feedback-wrap">
					<textarea id="notes" class="form-control disable-mce" name="notes" rows="4"><?php echo $member->getEncoded('notes'); ?></textarea>
				</div>
			</div>


			<hr>

			<div class="form-btns text-right">
				<div class="float-lg-right">
					<button class="btn btn-success btn-block-md mb-2">
						<i class="fal fa-save"></i> Save
					</button>

					<a class="btn btn-danger btn-block-md ml-lg-1 mb-2" href="/user/view/travel-affiliate-members">
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
		var item = null || <?php echo $member->toJson(); ?>;

		//console.log(item)

		// Init TinyMCE
		// tinymce.init({
		// 	selector: '#bio',
		// 	theme: 'silver',
		// 	cache_suffix: '?v=6.1.2',
		// 	base_url: '/library/packages/tinymce',
		// 	browser_spellcheck: true,
		// 	document_base_url: '/',
		// 	element_format: 'html',
		// 	forced_root_block: 'p',
		// 	formats: {
		// 		bold: {
		// 			inline: 'strong'
		// 		},
		// 		italic: {
		// 			inline: 'em'
		// 		},
		// 		underline: {
		// 			inline: 'u'
		// 		}
		// 	},
		// 	height: 362,
		// 	keep_styles: false,
		// 	menubar: false,
		// 	mobile: {
		// 		toolbar_mode: 'scrolling'
		// 	},
		// 	plugins: 'emoticons',
		// 	protect: [/<div class="clear"><\/div>/g],
		// 	relative_urls: false,
		// 	toolbar: 'bold italic underline emoticons',
		// 	valid_elements: 'p,br,strong/b,em/i,u',
		// 	verify_html: true,
		// 	statusbar: false
		// });

		// Add Placeholder to Inputs
		//$(':input').attr('placeholder', '(null)');

		// Handle Submission
		ajaxForm.on('submit', 'form', function(event) {
			// Prevent Default
			event.preventDefault();

			console.log(event);

			console.log($(this).serializeObject())

			// Handle Ajax
			$.ajax({
				data: Object.assign($(this).serializeObject(), {
					item: item
				}),
				dataType: 'json',
				method: 'post',
				async: false,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					//console.log(response);
					// Switch Status
					switch (response.status) {
						case 'success':
							console.log("success")
							location.href = '/user/view/travel-affiliate-members';
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