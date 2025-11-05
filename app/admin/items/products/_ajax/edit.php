<?php
/*
    Copyright (c) 2021–2025 FenclWebDesign.com
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Admin\User        $admin
 */

// Imports
use Items\Enums\Tables;
use Items\Enums\Types;

try {
    // Variable Defaults
    $item     = filter_input(INPUT_POST, 'item', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $item     = Items\Product::Init($item['id']);
    $page_url = Helpers::FormatPageURL(filter_input(INPUT_POST, 'page_url'), TRUE, $item->getPageUrl());

    // Validation
    $errors = call_user_func(function() use ($page_url) {
        $required = [
            'page_url'       => FILTER_DEFAULT,
            'page_title'     => FILTER_DEFAULT,
            'heading'        => FILTER_DEFAULT,
            'published'      => FILTER_VALIDATE_INT,
            'published_date' => FILTER_DEFAULT
        ];

        foreach ($required as $field => $validation) {
            switch ($validation) {
                case FILTER_VALIDATE_INT:
                    if (is_null(filter_input(INPUT_POST, $field, $validation)) || filter_input(INPUT_POST, $field, $validation) === FALSE) {
                        $errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
                    }
                    break;
                default:
                    if (!filter_input(INPUT_POST, $field, $validation)) {
                        $errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
                    }
            }
        }

        if (filter_input(INPUT_POST, 'filename') && !filter_input(INPUT_POST, 'filename_alt')) {
            $errors[] = 'Image alt is required.';
        }

        return $errors ?? FALSE;
    });

    if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
    if (is_null($item)) throw new Exception('Item not found in database');

    // Handle UPC changes
    $submittedUpcs = array_filter(array_map('trim', explode(',', $_POST['upcs'] ?? '')));
    $currentUpcs = $item->getUPCs();

    $toAdd = array_diff($submittedUpcs, $currentUpcs);
    foreach ($toAdd as $upc) $item->addUPC($upc);

    $toRemove = array_diff($currentUpcs, $submittedUpcs);
    foreach ($toRemove as $upc) $item->removeUPC($upc);

    // Determine refrigeration settings
    $is_refrigerated   = filter_input(INPUT_POST, 'is_refrigerated', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
    $fridge_space_id   = filter_input(INPUT_POST, 'fridge_space_id', FILTER_VALIDATE_INT, ['options' => ['default' => NULL]]);
    $fridge_price      = filter_input(INPUT_POST, 'fridge_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $fridge_description = filter_input(INPUT_POST, 'fridge_description');

    // Update Database
    Database::Action("
        UPDATE `products`
        SET
            `category_id`       = :category_id,
            `label`             = :label,
            `merchant`          = :merchant,
            `price`             = :price,
            `stock_quantity`    = :stock_quantity,
            `taxable`           = :taxable,
            `is_refrigerated`   = :is_refrigerated,
            `fridge_space_id`   = :fridge_space_id,
            `fridge_price`      = :fridge_price,
            `fridge_description`= :fridge_description,
            `page_title`        = :page_title,
            `page_description`  = :page_description,
            `heading`           = :heading,
            `content`           = :content,
            `youtube_id`        = :youtube_id,
            `filename`          = :filename,
            `filename_alt`      = :filename_alt,
            `page_url`          = :page_url,
            `published`         = :published,
            `published_date`    = :published_date,
            `author`            = :author,
            `user_agent`        = :user_agent,
            `ip_address`        = :ip_address
        WHERE `id` = :id
    ", [
        'category_id'       => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT),
        'label'             => filter_input(INPUT_POST, 'label'),
        'merchant'          => filter_input(INPUT_POST, 'merchant'),
        'price'             => filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'stock_quantity'    => filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT),
        'taxable'           => filter_input(INPUT_POST, 'taxable', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]),
        'is_refrigerated'   => $is_refrigerated,
        'fridge_space_id'   => $fridge_space_id,
        'fridge_price'      => $is_refrigerated ? $fridge_price : NULL,
        'fridge_description'=> $is_refrigerated ? $fridge_description : NULL,
        'page_title'        => filter_input(INPUT_POST, 'page_title'),
        'page_description'  => filter_input(INPUT_POST, 'page_description'),
        'heading'           => filter_input(INPUT_POST, 'heading'),
        'content'           => filter_input(INPUT_POST, 'content'),
        'youtube_id'        => Helpers::ExtractYouTubeID(filter_input(INPUT_POST, 'youtube_id')),
        'filename'          => filter_input(INPUT_POST, 'filename'),
        'filename_alt'      => filter_input(INPUT_POST, 'filename_alt'),
        'page_url'          => $page_url,
        'published'         => filter_input(INPUT_POST, 'published', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]),
        'published_date'    => filter_input(INPUT_POST, 'published_date'),
        'author'            => $admin->getId(),
        'user_agent'        => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
        'ip_address'        => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
        'id'                => $item->getId()
    ]);

    // Log action
    $admin->log(
        type       : Types\Log::UPDATE,
        table_name : Tables\Website::PRODUCTS,
        table_id   : $item->getId(),
        payload    : $_POST
    );

    // Handle Routes
    $route        ??= Router\Route::Init('products', $item->getId());
    $parent_route ??= Router\Route::Init('categories', filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT));
    $parent_route ??= Router\Route::Init('products');

    if (is_null($route)) {
        $route_id = Database::Action("
            INSERT INTO `routes`
            SET
                `parent_route_id` = :parent_route_id,
                `table_name`      = :table_name,
                `table_id`        = :table_id,
                `page_url`        = :page_url,
                `category`        = :category,
                `categories`      = :categories,
                `author`          = :author,
                `user_agent`      = :user_agent,
                `ip_address`      = :ip_address
        ", [
            'parent_route_id' => $parent_route?->getId(),
            'table_name'      => 'products',
            'table_id'        => $item->getId(),
            'page_url'        => $page_url,
            'category'        => FALSE,
            'categories'      => FALSE,
            'author'          => $admin->getId(),
            'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
        ], TRUE);

        $admin->log(
            type       : Types\Log::CREATE,
            table_name : Tables\Website::ROUTES,
            table_id   : $route_id,
            payload    : $_POST
        );
    } else {
        Database::Action("
            UPDATE `routes`
            SET
                `parent_route_id` = :parent_route_id,
                `table_name`      = :table_name,
                `table_id`        = :table_id,
                `page_url`        = :page_url,
                `category`        = :category,
                `categories`      = :categories,
                `author`          = :author,
                `user_agent`      = :user_agent,
                `ip_address`      = :ip_address
            WHERE `id` = :id
        ", [
            'parent_route_id' => $parent_route?->getId(),
            'table_name'      => 'products',
            'table_id'        => $item->getId(),
            'page_url'        => $page_url,
            'category'        => FALSE,
            'categories'      => FALSE,
            'author'          => $admin->getId(),
            'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
            'id'              => $route->getId()
        ]);

        $admin->log(
            type       : Types\Log::UPDATE,
            table_name : Tables\Website::ROUTES,
            table_id   : $route->getId(),
            payload    : $_POST
        );
    }

    Admin\SetMessage('Updated database successfully.', 'success');

    $json_response = [
        'status'   => 'success',
        'message'  => Admin\GetMessage(),
        'table_id' => $item->getId()
    ];
}
catch (FormException $exception) {
    $json_response = ['status' => 'error', 'errors' => $exception->getErrors()];
}
catch (PDOException $exception) {
    $json_response = ['status' => 'error', 'message' => Debug::Exception($exception)];
}
catch (Exception $exception) {
    $json_response = ['status' => 'error', 'message' => $exception->getMessage()];
}

echo json_encode($json_response);
