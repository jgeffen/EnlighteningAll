<?php
/*
    Refrigerator Spaces — Admin View
    Author: JGeffen for EnlighteningAll
    Date: 2025-10-08
*/

/*
Copyright (c) 2021, 2025 FenclWebDesign.com
This script may not be copied, reproduced or altered in whole or in part.
We check the Internet regularly for illegal copies of our scripts.
Do not edit or copy this script for someone else, because you will be held responsible as well.
This copyright shall be enforced to the full extent permitted by law.
Licenses to use this script on a single website may be purchased from FenclWebDesign.com
@Author: Developer
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Admin\User        $admin
 */

use Helpers\{Html, Render};

// Page setup
$page_title = 'View Refrigerator Spaces';

// Fetch all fridge spaces ordered by door and shelf
$spaces = Database::FetchAll("SELECT * FROM `fridge_spaces` ORDER BY `door`, `shelf_level` ASC");

// Include admin header
include(__DIR__ . '/../../includes/header.php');
?>

<div id="load-table" class="well well-sm">
    <h1><i class="fa-solid fa-snowflake"></i> View Refrigerator Spaces</h1>
    <legend></legend>
    <p>Number of Spaces in Database: <strong><?php echo count($spaces); ?></strong></p>
    <legend></legend>

    <?php if (!empty($spaces)) : ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="sort-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Door</th>
                    <th>Shelf Level</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <th>Rented Until</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($spaces as $space) : ?>
                    <tr id="space-<?php echo (int)$space['id']; ?>">
                        <td><?php echo (int)$space['id']; ?></td>
                        <td><span class="badge badge-info">Door <?php echo (int)$space['door']; ?></span></td>
                        <td><span class="badge badge-secondary">Level <?php echo (int)$space['shelf_level']; ?></span></td>
                        <td><strong><?php echo htmlspecialchars($space['name']); ?></strong></td>
                        <td><?php echo ucfirst(htmlspecialchars($space['type'])); ?></td>
                        <td>$<?php echo number_format((float)$space['price'], 2); ?></td>
                        <td>
                            <?php if ($space['available']) : ?>
                                <span class="badge badge-success">Available</span>
                            <?php else : ?>
                                <span class="badge badge-danger">Rented</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo !empty($space['rented_until'])
                                    ? date('m/d/Y', strtotime($space['rented_until']))
                                    : '<em>—</em>'; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-sm btn-primary view-space"
                                   data-id="<?php echo (int)$space['id']; ?>"
                                   href="#"
                                   title="View Details">
                                    <i class="fa fa-search"></i> View
                                </a>
                                <a class="btn btn-sm btn-warning"
                                   href="/user/edit/fridge-space?id=<?php echo (int)$space['id']; ?>">
                                    <i class="fa fa-pencil"></i> Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p><em>No refrigerator spaces found in the database.</em></p>
    <?php endif; ?>
</div>

<!-- Modal Container (AJAX-loaded details) -->
<div id="view-space-modal-container"></div>

<script>
    $(document).ready(function () {
        // Handle the "View" button click
        $('.view-space').on('click', function (e) {
            e.preventDefault();
            const id = $(this).data('id');

            $.ajax({f
                url: '/user/view/fridge/fridge-space-view.php',
                method: 'GET',
                data: { id: id },
                success: function (response) {
                    $('#view-space-modal-container').html(response);
                    $('#view-space-modal').modal('show');
                },
                error: function () {
                    alert('Unable to load space details.');
                }
            });
        });
    });
</script>
