<?php
/*
    AJAX Add — Fridge Space
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

    // Collect inputs
    $door         = filter_input(INPUT_POST, 'door', FILTER_VALIDATE_INT);
    $shelf_level  = filter_input(INPUT_POST, 'shelf_level', FILTER_VALIDATE_INT);
    $name         = trim(filter_input(INPUT_POST, 'name'));
    $type         = trim(filter_input(INPUT_POST, 'type'));
    $price        = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $description  = trim(filter_input(INPUT_POST, 'description'));
    $available    = filter_input(INPUT_POST, 'available', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $image        = trim(filter_input(INPUT_POST, 'image'));

    // Required field checks
    if (empty($door)) $errors[] = 'Door number is required.';
    if (empty($shelf_level)) $errors[] = 'Shelf level is required.';
    if (empty($name)) $errors[] = 'Name is required.';
    if ($price === false) $errors[] = 'Price must be a valid number.';

    // Error handling
    if (!empty($errors)) {
        throw new FormException($errors, 'Missing required fields.');
    }

    // --- Insert record into database ---
    $table_id = Database::Action("
        INSERT INTO `fridge_spaces`
        SET
            `door`         = :door,
            `shelf_level`  = :shelf_level,
            `name`         = :name,
            `type`         = :type,
            `price`        = :price,
            `description`  = :description,
            `available`    = :available,
            `image`        = :image,
            `created_at`   = NOW()
    ", [
        'door'        => $door,
        'shelf_level' => $shelf_level,
        'name'        => $name,
        'type'        => $type ?: 'general',
        'price'       => $price,
        'description' => $description,
        'available'   => $available ? 1 : 0,
        'image'       => $image,
    ], TRUE);

    // --- Log the admin action ---
    $admin->log(
        type       : Types\Log::CREATE,
        table_name : Tables\Website::FRIDGE_SPACES,
        table_id   : $table_id,
        payload    : $_POST
    );

    // --- Success response ---
    $json_response = [
        'status'  => 'success',
        'message' => 'Fridge space added successfully!',
        'table_id' => $table_id
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

// Output as JSON
echo json_encode($json_response);
