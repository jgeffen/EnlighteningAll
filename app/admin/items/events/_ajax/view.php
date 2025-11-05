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

// --- Safe Output Setup ---
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

// --- Pagination & Search ---
$start       = isset($_POST['start']) ? (int) $_POST['start'] : 0;
$length      = isset($_POST['length']) ? (int) $_POST['length'] : 250;
$draw        = isset($_POST['draw']) ? (int) $_POST['draw'] : 0;
$searchValue = trim($_POST['search']['value'] ?? '');

// --- Total records ---
$totalRecords = (int) Database::Action("SELECT COUNT(*) FROM `events`")->fetchColumn();

// --- Orderable columns ---
$orderColumn = ['id', 'sort_order', 'page_title', 'published', 'content', 'date_start', 'date_end', 'packages', 'timestamp'];

// --- Default order ---
$orderBy        = 'sort_order';
$orderDirection = 'ASC';

// --- Handle user-selected sort ---
if (!empty($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
    $colIndex = (int) $_POST['order'][0]['column'];
    if (isset($orderColumn[$colIndex])) {
        $orderBy        = $orderColumn[$colIndex];
        $orderDirection = strtolower($_POST['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';
    }
}

/*
    ✅ Custom Sorting Logic
    Sorts all records with sort_order > 0 first,
    then pushes sort_order = 0 or NULL to the bottom.
*/
$orderByQuery = "
    ORDER BY 
        (CASE WHEN CAST(`sort_order` AS UNSIGNED) = 0 OR `sort_order` IS NULL THEN 1 ELSE 0 END) ASC,
        CAST(`sort_order` AS UNSIGNED) ASC,
        `date_start` ASC
";

// --- Filters ---
$isUnPublishedFilter = ($_POST['columns'][3]['search']['value'] ?? '') === 'false';
$isPublishedFilter   = ($_POST['columns'][3]['search']['value'] ?? '') === 'true';

$results         = [];
$filteredRecords = $totalRecords;

try {
    if ($isPublishedFilter) {
        $query = "SELECT * FROM `events` WHERE `published` = 1 $orderByQuery";
        if ($length != -1) $query .= " LIMIT $start, $length";
        $filteredRecords = (int) Database::Action("SELECT COUNT(*) FROM `events` WHERE `published` = 1")->fetchColumn();
        $results = Database::Action($query)->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($isUnPublishedFilter) {
        $query = "SELECT * FROM `events` WHERE `published` = 0 $orderByQuery";
        if ($length != -1) $query .= " LIMIT $start, $length";
        $filteredRecords = (int) Database::Action("SELECT COUNT(*) FROM `events` WHERE `published` = 0")->fetchColumn();
        $results = Database::Action($query)->fetchAll(PDO::FETCH_ASSOC);

    } elseif (!empty($searchValue)) {
        $safeSearch = '%' . str_replace('%', '\%', $searchValue) . '%';
        $params = ['search' => $safeSearch];

        $query = "SELECT * FROM `events`
                  WHERE `id` LIKE :search
                     OR `page_title` LIKE :search
                     OR `content` LIKE :search
                     OR `sort_order` LIKE :search
                  $orderByQuery";
        if ($length != -1) $query .= " LIMIT $start, $length";

        $filteredRecords = (int) Database::Action(
            "SELECT COUNT(*) FROM `events`
             WHERE `id` LIKE :search
                OR `page_title` LIKE :search
                OR `content` LIKE :search
                OR `sort_order` LIKE :search",
            $params
        )->fetchColumn();

        $results = Database::Action($query, $params)->fetchAll(PDO::FETCH_ASSOC);

    } else {
        $query = "SELECT * FROM `events` $orderByQuery";
        if ($length != -1) $query .= " LIMIT $start, $length";
        $results = Database::Action($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Compact content: limit to ~15 words, safe ellipsis ---
    $truncateShort = function ($text, $maxWords = 10) {
        $text = trim(strip_tags($text ?? ''));
        if ($text === '') return '';
        $words = preg_split('/\s+/', $text);
        if (count($words) > $maxWords) {
            $words = array_slice($words, 0, $maxWords);
            return implode(' ', $words) . '…';
        }
        return implode(' ', $words);
    };

    // --- Format for DataTables ---
    $data = array_map(function ($row) use ($truncateShort) {
        return [
            'id'          => (int) ($row['id'] ?? 0),
            'sort_order'  => (int) ($row['sort_order'] ?? 0),
            'page_title'  => htmlspecialchars($row['page_title'] ?? '', ENT_QUOTES, 'UTF-8'),
            'content'     => htmlspecialchars($truncateShort($row['content'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'start'       => !empty($row['date_start']) ? date('M jS, Y', strtotime($row['date_start'])) : '',
            'start_date'  => $row['date_start'] ?? '',
            'end'         => !empty($row['date_end']) ? date('M jS, Y', strtotime($row['date_end'])) : '',
            'end_date'    => $row['date_end'] ?? '',
            'packages'    => 0,
            'timestamp'   => $row['timestamp'] ?? '',
            'published'   => (bool) ($row['published'] ?? 0),
            'item'        => $row,
            'options'     => Render::GetTemplate('admin/items/events/options.twig', ['id' => $row['id'] ?? 0])
        ];
    }, $results);

    $json_response = [
        'status'          => 'success',
        'message'         => 'DataTables loaded successfully.',
        'draw'            => $draw,
        'recordsTotal'    => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data'            => $data
    ];

} catch (Throwable $e) {
    $json_response = [
        'status'  => 'error',
        'message' => 'Server error: ' . $e->getMessage(),
        'data'    => []
    ];
}

// --- Output JSON cleanly ---
$json = json_encode($json_response, JSON_UNESCAPED_UNICODE);
if ($json === false) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'JSON encoding failed',
        'error'   => json_last_error_msg()
    ]);
} else {
    echo $json;
}
