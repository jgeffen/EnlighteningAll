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


try {

    $lookup = Items\TravelAffiliateMember::Fetch(Database::Action("SELECT * FROM `travel_affiliate_members` WHERE `id` = :id", array(
        'id' => filter_input(INPUT_POST, 'id'),
    )));

    // Checkup Lookup
    if (is_null($lookup)) throw new Exception('Affiliate ID does not exist.');

    $lookupArray = $lookup->toArray();

    // Set Response
    $json_response = array(
        'status'   => 'success',
        'message'  => 'Successfully Verified Affiliate ID Exists',
        'data' =>  $lookupArray
    );
} catch (Error | PDOException $exception) {
    // Set Response
    $json_response = array(
        'status'  => 'error',
        'message' => Debug::Exception($exception)
    );
} catch (Exception $exception) {
    // Set Response
    $json_response = array(
        'status'  => 'error',
        'message' => $exception->getMessage()
    );
}

// Output JSON
echo json_encode($json_response);
