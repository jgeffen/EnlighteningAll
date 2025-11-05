<?php
/*
    AJAX Sort — Fridge Spaces
    Author: JGeffen for EnlighteningAll
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Admin\User        $admin
 */

use Items\Enums\Tables;
use Items\Enums\Types;

try {
    // --- Validate ---
    $rows = filter_input(INPUT_POST, 'rows', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if ($rows && is_array($rows)) {
        foreach ($rows as $data) {
            if (!isset($data['id']) || !isset($data['position'])) {
                continue;
            }

            // Update database
            Database::Action("
                UPDATE `fridge_spaces`
                SET `position` = :position
                WHERE `id` = :id
            ", [
                'position' => (int)$data['position'],
                'id'       => (int)$data['id']
            ]);
        }

        // Log sorting action
        $admin->log(
            type       : Types\Log::UPDATE,
            table_name : Tables\Website::FRIDGE_SPACES,
            table_id   : 0, // general sort action, not per record
            payload    : $_POST
        );

        $json_response = [
            'status'  => 'success',
            'message' => 'Fridge space positions successfully updated.'
        ];
    } else {
        $json_response = [
            'status'  => 'error',
            'message' => 'No valid row data sent.'
        ];
    }

} catch (PDOException $exception) {
    $json_response = [
        'status'  => 'error',
        'message' => Debug::Exception($exception)
    ];
} catch (Exception $exception) {
    $json_response = [
        'status'  => 'error',
        'message' => $exception->getMessage()
    ];
}

// --- Output JSON ---
echo json_encode($json_response);
