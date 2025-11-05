<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Search Engine Optimization
	$page_title       = 'Enlightening All™ Bar';
	$page_description = 'Enlightening All™ Bar';
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col text-center">
				<h1><?php echo SITE_COMPANY; ?> Bar</h1>
				<p>Scan an item from our fridge and pay for it with your card on file! You can view your receipt under the <a href="/members/transactions">My Purchases</a> tab!</p>
				<p>Be sure to hold your phone vertically while scanning</p>
				<?php if(Membership::LoggedIn(FALSE)): ?>
					<div id="create-account-buttons" class="container">
						<div class="row">
							<div class="col flex-center">
								<a href="/members/register" target="_blank" type="button" class="create-member-account btn btn-primary">Create Member Account</a>
								<a href="/members/login?rel=<?php echo $_SERVER["REDIRECT_URL"]; ?>" type="button" class="member-login btn btn-primary">Login</a>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if(Membership::LoggedIn()): ?>
					<?php if($member->wallet()): ?>
						<div class="form-group">
							<video id="preview-video" width="300" height="200" autoplay playsinline muted style="border:1px solid #ccc;"></video>
							<p id="scan-result" class="mt-2 text-muted">Scan a barcode…</p>
						</div>
						
						<form id="bar-form" class="mt-3">
						<input type="hidden" name="product_id" id="product-id">
						<input type="hidden" name="upc_code" id="upc-code">
						
						<div id="product-details" class="mb-3" style="display:none;">
							<h3 id="product-label"></h3>
							<p><strong>Price:</strong> $<span id="product-price"></span></p>
							
							<p id="product-tax-row" style="display:none;">
								<strong>Sales Tax:</strong> $<span id="product-tax"></span>
							</p>
							<p id="product-total-row" style="display:none;">
								<strong>Total:</strong> $<span id="product-total"></span>
							</p>
							
							<p class="text-muted mb-0">
								Note: Your saved card on file will be charged for this purchase unless your points balance is greater than or equal to the cost of this item.<br>
								<strong>Your points balance is <u><?php echo $member?->wallet()->getPoints(); ?></u></strong><br>
								**Points Must Be Used If Available**
							</p>
						</div>
						<div class="d-flex justify-content-center align-items-center mb-3 text-center">
							<i class="fa-solid fa-credit-card fa-2x mr-2" data-type="<?php echo $member->wallet()->getAccountType(); ?>"></i>
							<div style="line-height:1.2;">
								Your card ending in <?php echo $member->wallet()->getAccountNumber(TRUE); ?><br>
								<?php if(!$member->wallet()->isExpired()): ?>
									<small class="text-muted">
										Expires: <?php echo $member->wallet()->getExpirationDate()->format('m/Y'); ?>
									</small>
								<?php else: ?>
									<small class="text-danger">
										Expired: <?php echo $member->wallet()->getExpirationDate()->format('m/Y'); ?>
									</small>
								<?php endif; ?>
							</div>
							<a class="btn btn-link ml-2" href="/members/billing">Update</a>
						</div>
					<?php else: ?>
						<p>Add Card on File to Start Scanning Purchases</p>
						<div class="d-flex justify-content-center align-items-center mb-3 text-center">
							
							<i class="fa-solid fa-credit-card fa-2x mr-2"></i>
							<div style="line-height:1.2;">No Card on File</div>
							<a class="btn btn-link ml-2" href="/members/billing">Add Payment</a>
						</div>
					<?php endif; ?>
					
					<button type="submit" class="btn btn-primary" style="display:none;" id="purchase-btn">
						Purchase
					</button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php if(Membership::LoggedIn(FALSE)): ?>
	<script>
		$(function() {
			var checkInterval = setInterval(function() {
				$.ajax({
					url: '/ajax/members/member-logged-in',  // URL to the PHP script
					type: 'post',  // GET or POST depending on your preference
					dataType: 'json',  // Expect JSON in response
					success: function(data) {
						if(data.loggedIn) {

							console.log('User is logged in');

							$(document).find('.package-data-table').removeClass('d-none');
							$(document).find('#create-account-buttons').remove();

							clearInterval(checkInterval);
							// Handle logged-in status, e.g., redirect or display content
						} else {
							console.log(data.loggedIn);

							// Handle not logged-in status, e.g., redirect to login page
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log('Error fetching login status:', textStatus);
					}
				});
			}, 5000);

			$(document).on('click', '.continue-as-guest', function(event) {

				event.preventDefault();

				$(document).find('.package-data-table').removeClass('d-none');

				$(document).find('#create-account-buttons').remove();

			});
		});
	</script>
<?php endif; ?>

<script>
	$(function() {
		// Variable Defaults
		var ajaxForm = $('#bar-form');

		// Handle Submission
		ajaxForm.on('submit', function(event) {
			event.preventDefault();

			const data = ajaxForm.serialize();

			// Force an extra field to prove we got here
			const requestData = data + '&debug_marker=bar_form_submit';

			$.ajax('/ajax/products/purchase/auth-cim', {
				data: requestData,
				dataType: 'json',
				method: 'post',
				async: true,
				beforeSend: showLoader,
				complete: hideLoader,
				success: function(response) {
					switch(response.status) {
						case 'success':
							ajaxForm.html(response.html);
							break;
						case 'error':
							displayMessage(response.message || 'Something went wrong.', 'alert');
							break;
						default:
							displayMessage('Unexpected response', 'alert');
					}
				}
			});
		});
	});

	(async function() {
		if(typeof ZXing === 'undefined' || !ZXing.BrowserMultiFormatReader) {
			console.error('ZXing not loaded');
			return;
		}

		const codeReader = new ZXing.BrowserMultiFormatReader();
		const videoId    = 'preview-video';

		function showProduct(data) {
			$('#product-id').val(data.id);
			$('#upc-code').val(data.upc);
			$('#product-label').text(data.label);
			$('#product-price').text(parseFloat(data.price).toFixed(2));
			$('#product-details').show();
			$('#purchase-btn').show();

			// Handle sales tax if applicable
			if(data.is_taxable) {
				const price   = parseFloat(data.price);
				const taxRate = parseFloat(data.tax_rate);
				const tax     = taxRate;
				const total   = price + tax;

				$('#product-tax').text(tax.toFixed(2));
				$('#product-total').text(total.toFixed(2));
				$('#product-tax-row, #product-total-row').show();
			} else {
				$('#product-tax-row, #product-total-row').hide();
			}
		}

		codeReader.decodeFromVideoDevice(null, videoId, (result, err) => {
			if(result) {
				const upc = result.getText();
				console.log('Scanned UPC:', upc);
				$('#scan-result').text('✅ ' + upc).css('color', 'green');

				// Stop scanner after first scan
				codeReader.reset();

				// Lookup product by UPC
				$.post('/ajax/products/find-by-upc', { upc: upc }, function(response) {
					if(response.status === 'success') {
						showProduct({
							id: response.id,
							upc: upc,
							label: response.label,
							price: response.price,
							is_taxable: response.is_taxable,
							tax_rate: response.tax_rate
						});
					} else {
						$('#scan-result')
							.text('❌ ' + (response.message || 'Product not found'))
							.css('color', 'red');
					}
				}, 'json');
			}
			if(err && !(err instanceof ZXing.NotFoundException)) {
				console.error(err);
			}
		});
	})();
</script>

<?php include('includes/body-close.php'); ?>
