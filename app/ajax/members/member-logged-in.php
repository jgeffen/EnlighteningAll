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

// Assuming Membership and other required classes are included or autoloaded

try {

    $loggedIn = Membership::LoggedIn(true);

    // Prepare a success response
    $json_response = array(
        'status'  => 'success',
        'loggedIn' => $loggedIn,  // Correctly pass the logged-in status
        'message' => 'User logged in status checked'
    );
} catch (Error | PDOException $exception) {
    // Handle Error or PDOException
    $json_response = array(
        'status'  => 'error',
        'message' => Debug::Exception($exception)
    );
} catch (Exception $exception) {
    // Handle general Exception
    $json_response = array(
        'status'  => 'error',
        'message' => $exception->getMessage()
    );
}


echo json_encode($json_response);
