<?php
/*
	Copyright (c) 2025 EnlighteningAll
	Custom Member QR Verification Page
	Created for safe standalone self-verification of member QR codes
	Author: Jonathon Geffen (EnlighteningAll)
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Membership        $member
 */

use Items\Enums\Sizes;

// Page Meta
$page_title       = "Verify Your QR Code";
$page_description = "Check and refresh your Enlightening All member QR code.";

// Header
include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container py-4">
		<div class="row justify-content-center">
			<div class="col-md-8 col-lg-6">
				<div class="card shadow-lg border-0">
					<div class="card-body text-center">
						<h1 class="h3 mb-3">Verify Your QR Code</h1>
						<p class="text-muted mb-4">Use this page to confirm your QR code is still valid or generate a new one if expired.</p>

						<img class="rounded-circle mb-3"
							 src="<?php echo $member->getAvatar()?->getImage(Sizes\Avatar::L, TRUE) ?? Items\Defaults::AVATAR_L; ?>"
							 alt="<?php echo htmlspecialchars($member->getFullName()); ?>"
							 style="width: 100px; height: 100px; object-fit: cover;">

						<h4 class="mb-3"><?php echo htmlspecialchars($member->getFullName()); ?></h4>

						<div id="qrSection">
							<img id="Qrcodeimage"
								 class="img-fluid mb-3 border p-2 rounded"
								 src="<?php echo $member->qrCode()->getCheckIn()->generate()?->getDataUri(); ?>"
								 alt="Your Enlightening All QR Code">

							<div class="d-flex justify-content-center gap-2">
								<button class="btn btn-primary" id="btnVerifyQr"
										onclick="verificationQrCode(<?php echo $member->getId(); ?>)">
									<i class="fa fa-check-circle me-1"></i> Verify QR
								</button>

								<button class="btn btn-secondary" id="btnRegenerateQr"
										onclick="regenerateQrCode(<?php echo $member->getId(); ?>)">
									<i class="fa fa-sync me-1"></i> Regenerate
								</button>
							</div>
						</div>

						<div id="qrStatusMsg" class="mt-3 small text-muted"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="showQRcode" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Your Verified QR Code</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body text-center">
				<img id="QrcodeimageModal" class="img-fluid mb-3" alt="QR Code">
				<p id="qrModalMsg" class="text-muted"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/body-close.php'); ?>

<script>
	function displayMessage(message, type = 'info') {
		const alertBox = document.createElement('div');
		alertBox.className = `alert alert-${type}`;
		alertBox.textContent = message;
		alertBox.style.position = 'fixed';
		alertBox.style.top = '20px';
		alertBox.style.right = '20px';
		alertBox.style.zIndex = '9999';
		document.body.appendChild(alertBox);
		setTimeout(() => alertBox.remove(), 4000);
	}

	function verificationQrCode(member_id) {
		$.ajax('/ajax/members/verification-qr-code', {
			data: {id: member_id},
			dataType: 'json',
			method: 'post',
			success: function (response) {
				switch (response.status) {
					case 'success':
						$('#Qrcodeimage').attr('src', response.data);
						$('#QrcodeimageModal').attr('src', response.data);
						$('#qrModalMsg').text('QR code verified successfully.');
						$('#qrStatusMsg').text('QR code verified successfully.').removeClass().addClass('text-success small');
						$('#showQRcode').modal('show');
						break;

					case 'expired':
						displayMessage('Your QR code has expired. Regenerating...', 'warning');
						regenerateQrCode(member_id);
						break;

					case 'suggestion':
						$(response.modal)
							.on('hidden.bs.modal', destroyModal)
							.modal();
						break;

					case 'error':
					default:
						displayMessage(response.message || 'Verification failed.', 'danger');
						$('#qrStatusMsg').text(response.message || 'Verification failed.').removeClass().addClass('text-danger small');
						break;
				}
			},
			error: function () {
				displayMessage('Connection error. Please try again.', 'danger');
			}
		});
	}

	function regenerateQrCode(member_id) {
		$.ajax('/ajax/members/regenerate-qr-code', {
			data: {id: member_id},
			dataType: 'json',
			method: 'post',
			success: function (response) {
				if (response.status === 'success') {
					$('#Qrcodeimage').attr('src', response.data);
					$('#QrcodeimageModal').attr('src', response.data);
					$('#qrModalMsg').text('New QR code generated successfully.');
					$('#qrStatusMsg').text('New QR code generated successfully.').removeClass().addClass('text-success small');
					$('#showQRcode').modal('show');
					displayMessage('New QR code generated successfully.', 'success');
				} else {
					displayMessage(response.message || 'Unable to regenerate QR code.', 'danger');
					$('#qrStatusMsg').text('Unable to regenerate QR code.').removeClass().addClass('text-danger small');
				}
			},
			error: function () {
				displayMessage('Server error while regenerating QR code.', 'danger');
			}
		});
	}
</script>
