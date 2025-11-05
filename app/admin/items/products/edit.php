<?php
/*
    Copyright (c) 2021–2025 FenclWebDesign.com
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Admin\User        $admin
 */

// Variable Defaults
$page_title  = 'Edit Product';
$image_sizes = array(
        'landscape' => array('width' => 900, 'height' => 600),
        'portrait'  => array('width' => 600, 'height' => 900),
        'square'    => array('width' => 900, 'height' => 900)
);

// Fetch Product
$item = Items\Product::Fetch(Database::Action("
    SELECT * FROM `products` WHERE `id` = :table_id
", array('table_id' => $dispatcher->getTableId())));

if (is_null($item)) Admin\Render::ErrorDocument(404);

// Fetch Fridge Spaces with full info
$fridge_spaces = Database::Action("
    SELECT id, door, shelf_level, name, type, price, description, available
    FROM fridge_spaces
    ORDER BY door ASC, shelf_level DESC
")->fetchAll(PDO::FETCH_ASSOC);

$template = reset($image_sizes);

include('includes/header.php');
?>

<main class="page-content">
    <div id="page-title-btn">
        <h1><?php echo $page_title; ?></h1>
    </div>

    <div id="ajax-wrapper">
        <form class="form-horizontal content-module">

            <?php if (!empty($categories)): ?>
                <div class="form-group">
                    <label for="category-id">Category</label>
                    <div class="select-wrap form-control">
                        <select id="category-id" name="category_id" data-value="<?php echo $item->getCategoryId(); ?>">
                            <?php foreach ($categories as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-box"></div>
                    </div>
                </div>
            <?php endif; ?>

            <h2>Search Engine Optimization:</h2>

            <div class="form-group">
                <label for="page-title-input">Page Title</label>
                <input id="page-title-input" class="form-control" type="text" name="page_title" maxlength="255"
                       value="<?php echo $item->getEncoded('page_title'); ?>">
            </div>

            <div class="form-group">
                <label for="page-description">Page Description</label>
                <input id="page-description" class="form-control" type="text" name="page_description" maxlength="255"
                       value="<?php echo $item->getEncoded('page_description'); ?>">
            </div>

            <div class="form-group">
                <label for="heading">Heading</label>
                <input id="heading" class="form-control" type="text" name="heading" maxlength="255"
                       value="<?php echo $item->getEncoded('heading'); ?>">
            </div>

            <h2>Page Content:</h2>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" class="form-control" name="content" rows="20"><?php echo $item->getContent(); ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fal fa-dollar-sign"></i></span>
                    </div>
                    <input id="price" class="form-control" type="text" name="price"
                           value="<?php echo $item->getEncoded('price'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="stock-quantity">Stock Quantity</label>
                <input id="stock-quantity" class="form-control" type="number" min="0" name="stock_quantity"
                       value="<?php echo (int)$item->getStockQuantity(); ?>">
            </div>

            <div class="form-group">
                <label for="published">Published?</label>
                <select id="published" class="form-control" name="published">
                    <?php foreach ([1 => 'Yes', 0 => 'No'] as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php echo ($item->isPublished() == $val) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-btns text-right mt-4">
                <button class="btn btn-success">
                    <i class="fal fa-save"></i> Save
                </button>
                <a class="btn btn-danger" href="/user/view/products">
                    <i class="fal fa-ban"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<?php include('includes/footer.php'); ?>

<script>
    $(function() {
        var ajaxForm = $('#ajax-wrapper');
        var item = <?php echo $item->toJson(); ?>;

        // Toggle fridge space section
        $('#is_refrigerated').on('change', function() {
            if ($(this).val() == '1') {
                $('#fridge-options-wrapper').slideDown();
            } else {
                $('#fridge-options-wrapper').slideUp();
                $('#fridge_space_id').val('');
                $('#fridge-space-details').hide();
            }
        });

        // Auto-fill fridge info
        $('#fridge_space_id').on('change', function() {
            const opt = $(this).find('option:selected');
            const descBox = $('#fridge-space-details');
            const priceInput = $('#fridge_price');

            if (opt.val()) {
                $('#fridge-type').text(opt.data('type'));
                $('#fridge-door').text(opt.data('door'));
                $('#fridge-name').text(opt.data('name'));
                $('#fridge-shelf').text(opt.data('shelf'));
                $('#fridge-price-display').text(opt.data('price'));
                $('#fridge-desc').text(opt.data('desc'));
                descBox.fadeIn();

                // Autofill price & description
                priceInput.val(opt.data('price'));
                $('#fridge_description').val(opt.data('desc'));

                // Update hidden fields for fridge name/type
                $('#fridge_space_name').val(opt.data('name'));
                $('#fridge_space_type').val(opt.data('type'));
            } else {
                descBox.hide();
                priceInput.val('');
                $('#fridge_description').val('');
                $('#fridge_space_name').val('');
                $('#fridge_space_type').val('');
            }
        });

        // Submit form via AJAX
        ajaxForm.on('submit', 'form', function(event) {
            event.preventDefault();
            $.ajax({
                data: Object.assign($(this).serializeObject(), { item: item }),
                dataType: 'json',
                method: 'post',
                async: false,
                beforeSend: showLoader,
                complete: hideLoader,
                success: function(response) {
                    switch (response.status) {
                        case 'success':
                            location.href = '/user/view/products';
                            break;
                        case 'error':
                            displayMessage(response.message || Object.keys(response.errors).map(function(k) {
                                return response.errors[k];
                            }).join('<br>'), 'alert', null);
                            break;
                        default:
                            displayMessage(response.message || 'Something went wrong.', 'alert');
                    }
                }
            });
        });
    });
</script>

<?php include('includes/body-close.php'); ?>
