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

/**
 * @var Router\Dispatcher $dispatcher
 * @var Admin\User        $admin
 */

// Always return JSON
header('Content-Type: application/json');

// Imports
use Items\Members;

// ===========================
// Modal Save: action=update
// ===========================
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            throw new Exception('Invalid reservation ID.');
        }

        $params = array(
            'id'            => $id,
            'name_on_pass'  => $_POST['name_on_pass']  ?? '',
            'seat_selected' => $_POST['seat_selected'] ?? '',
            'song_selected' => $_POST['song_selected'] ?? '',
            'comments'      => $_POST['notes']         ?? ''
        );

        Database::Action("
				UPDATE `member_reservations`
				   SET `name_on_pass`  = :name_on_pass,
				       `seat_selected` = :seat_selected,
				       `song_selected` = :song_selected,
				       `comments`      = :comments
				 WHERE `id` = :id
			", $params);

        echo json_encode(array('status' => 'success', 'message' => 'Reservation updated successfully.'));
    } catch (Error|PDOException $e) {
        echo json_encode(array('status' => 'error', 'message' => Debug::Exception($e)));
    } catch (Exception $e) {
        echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
    }
    exit; // IMPORTANT: don’t fall through to the listing JSON
}

// ===========================
// Original listing JSON
// ===========================
try {
    // Set Response
    $json_response = array(
        'status'     => 'success',
        'message'    => 'DataTables loaded successfully.',
        'data'       => array_map(fn(Members\Reservation $item) => array(
            'id'           => $item->getItemCount() == 1 ? $item->getId() : NULL,
            'notes'        => $item->getItemCount() == 1 ? $item->getComments() : NULL,
            'status'       => array(
                'value'   => $item->getStatus()?->getLabel(),
                'display' => Render::GetTemplate('admin/items/events/reservations/status.twig', array(
                    'label' => $item->getStatus()?->getLabel()
                ))
            ),
            'total_amount' => $item->getTotalAmount(TRUE),
            'name_on_pass' => Render::GetTemplate('admin/items/events/reservations/name-on-pass.twig', array(
                'name'  => $item->getNameOnPass(),
                'phone' => $item->getPhone(),
                'link'  => $item->getMember()?->getLink()
            )),
            'event'        => array(
                'value'   => $item->getEvent()?->getTitle(),
                'display' => Render::GetTemplate('admin/items/events/reservations/event.twig', array(
                    'name' => $item->getEvent()?->getHeading(),
                    'link' => $item->getEvent()?->getLink()
                ))
            ),
            'package'      => array(
                'value'   => $item->getPackage()?->getName(),
                'display' => Render::GetTemplate('admin/items/events/reservations/package.twig', array(
                    'name'  => $item->getPackage()?->getName(),
                    'price' => $item->getPackage()?->getPrice(TRUE)
                ))
            ),
            'item_count'   => Render::GetTemplate('admin/items/events/reservations/item-count.twig', array(
                'notes'            => $item->getComments(),
                'item_count'       => $item->getItemCount(),
                'item_count_total' => $item->getItemCountTotal()
            )),
            'first'        => $item->getItemCount() == 1,
            'last'         => $item->getItemCount() == $item->getItemCountTotal(),
            'paid'         => $item->isPaid(),
            'timestamp'    => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
            'item'         => $item->toArray()
        ), Members\Reservation::FetchAll(Database::Action("SELECT * FROM `member_reservations` WHERE `transaction_id` = :transaction_id OR :transaction_id IS NULL ORDER BY `timestamp` DESC", array(
            'transaction_id' => $dispatcher->getTableId()
        )))),
        'categories' => !$dispatcher->getTableId() ? call_user_func(fn($categories) => array(
            'data'   => $categories,
            'html'   => Render::GetTemplate('admin/items/categories.twig', array('categories' => $categories)),
            'filter' => 'event_id'
        ), array('default.show_all' => 'All') + Database::Action("SELECT `events`.`id`, CONCAT_WS(' - ', `events`.`date_start`, `events`.`heading`, CONCAT_WS(' ', COUNT(`member_reservations`.`id`), IF(COUNT(`member_reservations`.`id`) = 1, 'Reservation', 'Reservations'))) FROM `events` JOIN `member_reservations` ON `member_reservations`.`event_id` = `events`.`id` GROUP BY `events`.`id` ORDER BY `events`.`date_start` DESC, `events`.`heading`")->fetchAll(PDO::FETCH_KEY_PAIR)) : NULL
    );
} catch(Error|PDOException $exception) {
    // Set Response
    $json_response = array(
        'status'  => 'error',
        'message' => Debug::Exception($exception),
        'data'    => array()
    );
} catch(Exception $exception) {
    // Set Response
    $json_response = array(
        'status'  => 'error',
        'message' => $exception->getMessage(),
        'data'    => array()
    );
}

// Output Response
echo json_encode($json_response);
