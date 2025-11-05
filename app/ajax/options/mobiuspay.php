<?php
/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/

try {
	$json_response = array(
		'status'  => 'success',
		'options' => MobiusPay\Client::FormOptions(filter_input(INPUT_POST, 'type'), filter_input(INPUT_POST, 'sub_type'))
	);
} catch (Exception $exception) {
	$json_response = array(
		'status'  => 'exception',
		'message' => $exception->getMessage() ?: 'Something went wrong.'
	);
}

// Output Response
echo json_encode($json_response);
