<?php
/*
    AJAX Edit — Fridge Space
    Author: JGeffen for EnlighteningAll
*/

use Items\Enums\Tables;
use Items\Enums\Types;
use Exceptions\FormException;

/**
 * @var Admin\User $admin
 */

try {
    // --- Validation ---
    $errors = [];

    $id           = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $door         = filter_input(INPUT_POST, 'door', FILTER_VALIDATE_INT);
    $shelf_level  = filter_input(INPUT_POST, 'shelf_level', FILTER_VALIDATE_INT);
    $name         = trim(filter_input(INPUT_POST, 'name'));
    $type         = trim(filter_input(INPUT_POST, 'type'));
    $price        = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $description  = trim(filter_input(INPUT_POST, 'description'));
    $available    = filter_input(INPUT_POST, 'available', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $image        = trim(filter_input(INPUT_POST, 'image'));
    $rented_until = trim(filter_input(INPUT_POST, 'rented_until'));
    $rented_by    = filter_input(INPUT_POST, 'rented_by', FILTER_VALIDATE_INT);

    // Check required fields
    if (empty($id)) $errors[] = 'Invalid record ID.';
    if (empty($door)) $errors[] = 'Door number is required.';
    if (empty($shelf_level)) $errors[] = 'Shelf level is required.';
    if (empty($name)) $errors[] = 'Name is required.';
    if ($price === false) $errors[] = 'Price must be a valid number.';

    // Throw error if missing fields
    if (!empty($errors)) {
        throw new FormException($errors, 'Missing or invalid fields.');
    }

    // --- Ensure record exists ---
    $existing = Database::Fetch("SELECT id FROM `fridge_spaces` WHERE `id` = :id", ['id' => $id]);
    if (!$existing) {
        throw new Exception('Fridge space not found in database.');
    }

    // --- Update database ---
    Database::Action("
        UPDATE `fridge_spaces`
        SET
            `door`         = :door,
            `shelf_level`  = :shelf_level,
            `name`         = :name,
            `type`         = :type,
            `price`        = :price,
            `description`  = :description,
            `available`    = :available,
            `image`        = :image,
            `rented_until` = :rented_until,
            `rented_by`    = :rented_by
        WHERE `id` = :id
    ", [
        'id'           => $id,
        'door'         => $door,
        'shelf_level'  => $shelf_level,
        'name'         => $name,
        'type'         => $type ?: 'general',
        'price'        => $price,
        'description'  => $description,
        'available'    => $available ? 1 : 0,
        'image'        => $image,
        'rented_until' => !empty($rented_until) ? date('Y-m-d H:i:s', strtotime($rented_until)) : null,
        'rented_by'    => $rented_by,
    ]);

    // --- Log the admin action ---
    $admin->log(
        type       : Types\Log::UPDATE,
        table_name : Tables\Website::FRIDGE_SPACES,
        table_id   : $id,
        payload    : $_POST
    );

    // --- Success response ---
    $json_response = [
        'status'  => 'success',
        'message' => 'Fridge space updated successfully.',
        'table_id' => $id
    ];

} catch (FormException $exception) {
    $json_response = [
        'status' => 'error',
        'errors' => $exception->getErrors()
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

// --- Output JSON Response ---
echo json_encode($json_response);
