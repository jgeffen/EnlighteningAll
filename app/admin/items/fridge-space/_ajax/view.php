<?php
/*
    AJAX View — Fridge Space Details
    Author: JGeffen for EnlighteningAll
*/

use Helpers\{Html, Render};

// Validate ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo '<div class="alert alert-danger">Invalid fridge space ID.</div>';
    exit;
}

// Fetch fridge space
$space = Database::Fetch("SELECT * FROM `fridge_spaces` WHERE `id` = :id", ['id' => $id]);
if (!$space) {
    echo '<div class="alert alert-danger">Fridge space not found.</div>';
    exit;
}
?>

<div id="view-space-modal" class="modal fade" aria-hidden="true" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" type="button" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa-solid fa-snowflake"></i>
                <h3 class="modal-title">Viewing Space: <?php echo htmlspecialchars($space['name']); ?></h3>
            </div>
            <div class="modal-body">
                <p><strong>ID:</strong> <?php echo $space['id']; ?></p>
                <p><strong>Door:</strong> <?php echo $space['door']; ?></p>
                <p><strong>Shelf Level:</strong> <?php echo $space['shelf_level']; ?></p>
                <p><strong>Type:</strong> <?php echo ucfirst($space['type']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($space['price'], 2); ?></p>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($space['description'])); ?></p>
                <p><strong>Available:</strong>
                    <?php echo $space['available']
                        ? '<span class="badge badge-success">Yes</span>'
                        : '<span class="badge badge-danger">No</span>'; ?>
                </p>
                <p><strong>Rented Until:</strong>
                    <?php echo !empty($space['rented_until'])
                        ? date('m/d/Y', strtotime($space['rented_until']))
                        : '<em>—</em>'; ?>
                </p>
                <p><strong>Created:</strong> <?php echo date('m/d/Y h:i A', strtotime($space['created_at'])); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
