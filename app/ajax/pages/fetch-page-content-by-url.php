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


try {

    $page_lookup = Items\Page::Fetch(Database::Action("SELECT `content` FROM `pages` WHERE `page_url` = :page_url", array(
        'page_url' => filter_input(INPUT_POST, 'page_slug')
    )));

    // Checkup Lookup
    if (is_null($page_lookup)) throw new Exception('Page URL does not exist.');

    $page_lookup_array = $page_lookup->toArray();

    // Set Response
    $json_response = array(
        'status'   => 'success',
        'message'  => 'Successfully looked up page.',
        'data' =>  $page_lookup_array
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
