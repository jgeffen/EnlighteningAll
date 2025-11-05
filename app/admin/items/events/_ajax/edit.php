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
    $item     = Items\Event::Init($item['id']);
    $page_url = Helpers::FormatPageURL(filter_input(INPUT_POST, 'page_url'), TRUE, $item->getPageUrl());

    $errors = call_user_func(function() use ($page_url) {
        $required = array(
            'class_type'     => FILTER_DEFAULT,
            'page_title'     => FILTER_DEFAULT,
            'page_url'       => FILTER_DEFAULT,
            'heading'        => FILTER_DEFAULT,
            'event_dates'    => FILTER_DEFAULT,
            'published'      => FILTER_VALIDATE_INT,
            'published_date' => FILTER_DEFAULT
        );

        foreach($required as $field => $validation) {
            switch($validation) {
                case FILTER_VALIDATE_INT:
                    if(is_null(filter_input(INPUT_POST, $field, $validation)) || filter_input(INPUT_POST, $field, $validation) === FALSE) {
                        $errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
                    }
                    break;
                case FILTER_DEFAULT:
                default:
                    if(!filter_input(INPUT_POST, $field, $validation)) {
                        $errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
                    }
            }
        }

        if(filter_input(INPUT_POST, 'filename') && !filter_input(INPUT_POST, 'filename_alt')) {
            $errors[] = 'Image alt is required.';
        }

        return $errors ?? FALSE;
    });

    $hour_from     = $_POST['event_hour_from'] ?? NULL;
    $minute_from   = $_POST['event_minute_from'] ?? NULL;
    $meridian_from = $_POST['event_meridian_from'] ?? NULL;

    $hour_to     = $_POST['event_hour_to'] ?? NULL;
    $minute_to   = $_POST['event_minute_to'] ?? NULL;
    $meridian_to = $_POST['event_meridian_to'] ?? NULL;

    if($hour_from && $minute_from && $meridian_from && $hour_to && $minute_to && $meridian_to) {
        $event_time = sprintf(
            '%s:%s %s - %s:%s %s',
            $hour_from, $minute_from, strtoupper($meridian_from),
            $hour_to, $minute_to, strtoupper($meridian_to)
        );
    } else {
        $event_time = $item->getEventTimes();
    }

    if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
    if(is_null($item)) throw new Exception('Item not found in database');

    list($date_start, $date_end) = explode(' to ', filter_input(INPUT_POST, 'event_dates')) + array_fill(0, 2, filter_input(INPUT_POST, 'event_dates'));

    // ✅ Capture sort_order from the form (default 0 if not set)
    $sort_order = filter_input(INPUT_POST, 'sort_order', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);

    // ✅ Update Database with new sort_order field
    Database::Action(
        "UPDATE `events` SET 
            `category_id` = :category_id,
            `class_type` = :class_type,
            `page_title` = :page_title,
            `page_description` = :page_description,
            `heading` = :heading,
            `content` = :content,
            `youtube_id` = :youtube_id,
            `event_dates` = :event_dates,
            `event_times` = :event_times,
            `date_start` = :date_start,
            `date_end` = :date_end,
            `location` = :location,
            `price_text` = :price_text,
            `event_package_ids` = :event_package_ids,
            `filename` = :filename,
            `filename_alt` = :filename_alt,
            `page_url` = :page_url,
            `teacher_id` = :teacher_id,
            `accepting_rsvp` = :accepting_rsvp,
            `bookable` = :bookable,
            `display_rsvps` = :display_rsvps,
            `published` = :published,
            `published_date` = :published_date,
            `author` = :author,
            `user_agent` = :user_agent,
            `ip_address` = :ip_address,
            `sort_order` = :sort_order
         WHERE `id` = :id",
        array(
            'category_id'       => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT),
            'class_type'        => filter_input(INPUT_POST, 'class_type'),
            'page_title'        => filter_input(INPUT_POST, 'page_title'),
            'page_description'  => filter_input(INPUT_POST, 'page_description'),
            'heading'           => filter_input(INPUT_POST, 'heading'),
            'content'           => filter_input(INPUT_POST, 'content'),
            'youtube_id'        => Helpers::ExtractYouTubeID(filter_input(INPUT_POST, 'youtube_id')),
            'event_dates'       => filter_input(INPUT_POST, 'event_dates'),
            'event_times'       => $event_time,
            'date_start'        => $date_start,
            'date_end'          => $date_end,
            'location'          => filter_input(INPUT_POST, 'location'),
            'price_text'        => filter_input(INPUT_POST, 'price_text'),
            'event_package_ids' => json_encode(filter_input(INPUT_POST, 'event_package_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? array()),
            'filename'          => filter_input(INPUT_POST, 'filename'),
            'filename_alt'      => filter_input(INPUT_POST, 'filename_alt'),
            'page_url'          => $page_url,
            'teacher_id'        => filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT),
            'accepting_rsvp'    => filter_input(INPUT_POST, 'accepting_rsvp', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
            'bookable'          => filter_input(INPUT_POST, 'bookable', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
            'display_rsvps'     => filter_input(INPUT_POST, 'display_rsvps', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
            'published'         => filter_input(INPUT_POST, 'published', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
            'published_date'    => filter_input(INPUT_POST, 'published_date'),
            'author'            => $admin->getId(),
            'user_agent'        => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'ip_address'        => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
            'sort_order'        => $sort_order, // ✅ new field saved here
            'id'                => $item->getId()
        )
    );

    // Log Action
    $admin->log(
        type       : Types\Log::UPDATE,
        table_name : Tables\Website::EVENTS,
        table_id   : $item->getId(),
        payload    : $_POST
    );

    // Route handling (unchanged)
    $route        ??= Router\Route::Init('events', $item->getId());
    $parent_route ??= Router\Route::Init('categories', filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT));
    $parent_route ??= Router\Route::Init('events');

    if (is_null($route)) {
        $route_id = Database::Action(
            "INSERT INTO `routes` SET 
                `parent_route_id` = :parent_route_id,
                `table_name` = :table_name,
                `table_id` = :table_id,
                `page_url` = :page_url,
                `category` = :category,
                `categories` = :categories,
                `author` = :author,
                `user_agent` = :user_agent,
                `ip_address` = :ip_address",
            array(
                'parent_route_id' => $parent_route?->getId(),
                'table_name'      => 'events',
                'table_id'        => $item->getId(),
                'page_url'        => $page_url,
                'category'        => FALSE,
                'categories'      => FALSE,
                'author'          => $admin->getId(),
                'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
                'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
            ),
            TRUE
        );

        $admin->log(
            type       : Types\Log::CREATE,
            table_name : Tables\Website::ROUTES,
            table_id   : $route_id,
            payload    : $_POST
        );
    } else {
        Database::Action(
            "UPDATE `routes` SET 
                `parent_route_id` = :parent_route_id,
                `table_name` = :table_name,
                `table_id` = :table_id,
                `page_url` = :page_url,
                `category` = :category,
                `categories` = :categories,
                `author` = :author,
                `user_agent` = :user_agent,
                `ip_address` = :ip_address
             WHERE `id` = :id",
            array(
                'parent_route_id' => $parent_route?->getId(),
                'table_name'      => 'events',
                'table_id'        => $item->getId(),
                'page_url'        => $page_url,
                'category'        => FALSE,
                'categories'      => FALSE,
                'author'          => $admin->getId(),
                'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
                'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
                'id'              => $route->getId()
            )
        );

        $admin->log(
            type       : Types\Log::UPDATE,
            table_name : Tables\Website::ROUTES,
            table_id   : $route->getId(),
            payload    : $_POST
        );
    }

    // Success response
    Admin\SetMessage('Updated database successfully.', 'success');
    $json_response = array(
        'status'   => 'success',
        'message'  => Admin\GetMessage(),
        'table_id' => $item->getId()
    );

} catch(FormException $exception) {
    $json_response = array(
        'status' => 'error',
        'errors' => $exception->getErrors()
    );
} catch(PDOException $exception) {
    $json_response = array(
        'status'  => 'error',
        'message' => Debug::Exception($exception)
    );
} catch(Exception $exception) {
    $json_response = array(
        'status'  => 'error',
        'message' => $exception->getMessage()
    );
}

// Output Response
echo json_encode($json_response);
