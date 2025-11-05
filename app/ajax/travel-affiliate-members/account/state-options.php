  <?php

    /*
  Copyright (c) 2023, 2024 Daerik.com
  This script may not be copied, reproduced or altered in whole or in part.
  We check the Internet regularly for illegal copies of our scripts.
  Do not edit or copy this script for someone else, because you will be held responsible as well.
  This copyright shall be enforced to the full extent permitted by law.
  Licenses to use this script on a single website may be purchased from Daerik.com
  @Author: Daerik
  */

    try {
        $json_response = array(
            'status' => 'success',
            'options' => match (filter_input(INPUT_POST, 'type')) {
                'states' => Locations\State::Options(Database::Action("SELECT * FROM `location_states` WHERE `country_code` = :country_code ORDER BY `name`", array(
                    'country_code' => filter_input(INPUT_POST, 'sub_type')
                ))),
                NULL => array()
            }
        );
    } catch (Exception $exception) {
        $json_response = array(
            'status' => 'error',
            'message' => $exception->getMessage()
        );
    }

    // Output Response
    echo json_encode($json_response);
