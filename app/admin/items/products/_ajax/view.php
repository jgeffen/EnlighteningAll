<?php
/*
    Copyright (c) 2021–2025 FenclWebDesign.com
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Admin\User        $admin
 */

try {
    // Fetch products with optional fridge space link
    $query = "
        SELECT p.*, f.name AS fridge_space_name
        FROM products p
        LEFT JOIN fridge_spaces f ON f.id = p.fridge_space_id
        ORDER BY p.position DESC
    ";

    $results = Database::Action($query);

    // Set Response
    $json_response = array(
        'status'  => 'success',
        'message' => 'DataTables loaded successfully.',
        'data'    => array_map(
            fn(Items\Product $item) => array(
                'id'         => $item->getId(),
                'page_title' => Helpers::Truncate($item->getTitle(), 50),
                'content'    => Helpers::Truncate($item->getContent(), 50),
                'timestamp'  => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
                'published'  => $item->isPublished(),
                'item'       => array_merge(
                    $item->toArray(),
                    array(
                        // New fields (safe defaults if missing)
                        'is_refrigerated'   => $item->toArray()['is_refrigerated'] ?? 0,
                        'fridge_space_id'   => $item->toArray()['fridge_space_id'] ?? null,
                        'fridge_space_name' => $item->toArray()['fridge_space_name'] ?? null,
                    )
                ),
                'options'    => Render::GetTemplate(
                    'admin/items/products/options.twig',
                    array('id' => $item->getId())
                )
            ),
            Items\Product::FetchAll($results)
        )
    );
} catch (Exception $exception) {
    // Error handling
    $json_response = array(
        'status'  => 'error',
        'message' => Debug::Exception($exception),
        'data'    => array()
    );
}

// Output Response
echo json_encode($json_response);
