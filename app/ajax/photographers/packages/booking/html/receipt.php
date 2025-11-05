<?php
	/**
	 * @var array $event
	 * @var array $form_values
	 * @var array $subjects
	 */
	
	// Turn On Output Buffering
	ob_start();
	
	// Start Email Top
	include('templates/email-template-top.php')
?>

<h3><?php echo $subjects['receipt']; ?></h3>

<b>Authorization Details</b>
<p>Invoice: <?php echo $form_values['invoice']; ?></p>
<p>Amount: <?php echo Helpers::FormatCurrency($form_values['amount']); ?></p>
<p>Account: <?php echo $form_values['account_number']; ?></p>

<hr>

<b>Billing Details</b>
<p>Name: <?php echo $form_values['billing_first_name']; ?> <?php echo $form_values['billing_last_name']; ?></p>
<p>Phone: <?php echo $form_values['billing_phone']; ?></p>
<p>Email: <?php echo $form_values['billing_email']; ?></p>
<p>Address Line 1: <?php echo $form_values['billing_address_line_1']; ?></p>
<p>Address Line 2: <?php echo $form_values['billing_address_line_2']; ?></p>
<p>City: <?php echo $form_values['billing_city']; ?></p>
<p>State: <?php echo MobiusPay\Client::FormOptions('states', $form_values['billing_country'], $form_values['billing_state']); ?></p>
<p>Country: <?php echo MobiusPay\Client::FormOptions('countries', NULL, $form_values['billing_country']); ?></p>
<p>Zip Code: <?php echo $form_values['billing_zip_code']; ?></p>

<hr>

<b>Photographer Package</b>
<p>Photographer: <?php echo $event['photographer']; ?></p>
<p>Package: <?php echo $event['heading']; ?></p>
<p>Description: <?php echo shortdesc($event['content'], 180); ?></p>

<hr>

<p>Comments: <?php echo nl2br($form_values['comments']); ?></p>

<hr>

<p>User Agent: <?php echo Helpers::FormatUserAgent(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT')); ?></p>
<p>IP Address: <?php echo filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP); ?></p>
<p>Date: <?php echo date_create()->format('l, F jS, Y, g:ia T'); ?></p>

<?php
	// Start Email Bottom
	include('templates/email-template-bottom.php');
	
	// Return Current Buffer Contents and Delete Current Output Buffer
	return ob_get_clean();
?>

