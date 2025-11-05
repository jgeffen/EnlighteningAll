<?php
/*
    AJAX Delete — Fridge Space
    Author: JGeffen for EnlighteningAll
*/

use Items\Enums\Tables;
use Items\Enums\Types;

/**
 * @var Admin\User $admin
 */

try {
    // --- Validate ID ---
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception('Invalid fridge space ID.');
    }

    // --- Fetch existing record ---
    $space = Database::Fetch("SELECT * FROM `fridge_spaces` WHERE `id` = :id", ['id' => $id]);
    if (!$space) {
        throw new Exception('Fridge space not found in the database.');
    }

    // --- Delete record ---
    Database::Action("DELETE FROM `fridge_spaces` WHERE `id` = :id", ['id' => $id]);

    // --- Optional: remove image file if stored locally ---
    if (!empty($space['image'])) {
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/fridge/' . $space['image'];
        if (file_exists($image_path)) {
            @unlink($image_path);
        }
    }

    // --- Log deletion ---
    $admin->log(
        type       : Types\Log::DELETE,
        table_name : Tables\Website::FRIDGE_SPACES,
        table_id   : $id,
        payload    : $_POST
    );

    // --- Return success response ---
    $json_response = [
        'status'  => 'success',
        'message' => sprintf('Fridge space "%s" (ID #%d) deleted successfully.', $space['name'], $id)
    ];

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
