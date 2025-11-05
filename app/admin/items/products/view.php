<?php
/*
    Copyright (c) 2021–2025 FenclWebDesign.com
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Admin\User        $admin
 */

// Variable Defaults
$page_title = 'View Products';

// Start Header
include('includes/header.php');
?>

<main class="page-content">
    <section id="view-table" role="region">
        <div id="page-title-btn">
            <h1><?php echo $page_title; ?></h1>

            <a class="btn btn-primary" href="/user/add/products">
                <i class="fa fa-plus"></i>
                <span class="d-none d-sm-inline">Add Product</span>
            </a>
        </div>

        <span class="d-block mb-3">
            <strong class="text-muted">Legend:</strong>
            <span class="badge bg-warning text-dark">Unpublished</span>
            <span class="badge bg-info text-light">Refrigerated</span>
        </span>

        <div class="row">
            <div class="col-12">
                <table id="data-table" class="table table-bordered nowrap"
                       data-table-options="<?php echo $dispatcher->toJson(JSON_HEX_QUOT); ?>">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Refrigerated</th>
                        <th>Fridge Space</th>
                        <th>Timestamp</th>
                        <th>Options</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</main>

<?php include('includes/footer.php'); ?>

<script>
    $(function() {
        var tableElement = $('#data-table');
        var tableOptions = tableElement.data('tableOptions');

        // Init DataTables (auto-detects ajax source internally)
        tableElement.DataTable({
            order: [],
            pageLength: 250,
            paging: true,
            searching: true,
            stateSave: true,
            info: true,
            ordering: false,
            rowReorder: { selector: 'td:not(.no-drag)', dataSrc: 'item.position', update: false },
            columns: [
                { data: 'id', className: 'all' },
                { data: { _: 'item.page_title', display: 'page_title' }, className: 'all' },
                { data: { _: 'item.content', display: 'content' }, className: 'all' },
                {
                    data: 'item.is_refrigerated',
                    className: 'text-center all',
                    render: function(val) {
                        return val == 1
                            ? '<span class="badge bg-info">Yes</span>'
                            : '<span class="badge bg-secondary">No</span>';
                    }
                },
                {
                    data: 'item.fridge_space_name',
                    className: 'all',
                    render: function(val, type, row) {
                        if (row.item.is_refrigerated == 1)
                            return val ? val : '<em>Unassigned</em>';
                        return '<span class="text-muted">N/A</span>';
                    }
                },
                { data: 'timestamp', className: 'all' },
                { data: 'options', className: 'all no-drag text-center' }
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function(row) {
                            var data = row.data();
                            return 'Details for Product ID #' + data.id;
                        }
                    }),
                    renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                        tableClass: 'table'
                    })
                }
            },
            rowCallback: function(row) {
                var tableRow = $(row);
                var currentTable = $(this);
                var dataTable = currentTable.DataTable();
                var rowData = dataTable.row(row).data();

                // Append ID
                tableRow.prop('id', rowData.id);

                // Highlight unpublished or refrigerated
                if (!rowData.published) tableRow.addClass('table-warning');
                if (rowData.item.is_refrigerated == 1) tableRow.addClass('table-info');

                // Bind delete button
                tableRow.off().on('click', '[data-action]', function(event) {
                    if (event) {
                        event.preventDefault();
                        var currentRow = event.delegateTarget;
                        var action = $(this).data('action');
                        var data = dataTable.row(currentRow).data();

                        switch (action) {
                            case 'delete':
                                if (confirm('Are you sure you want to delete this?')) {
                                    $.ajax('/user/delete/products/' + data.item.id, {
                                        dataType: 'json',
                                        method: 'delete',
                                        async: true,
                                        beforeSend: showLoader,
                                        complete: hideLoader,
                                        success: function(response) {
                                            switch (response.status) {
                                                case 'success':
                                                    console.info(response.message);
                                                    dataTable.ajax.reload();
                                                    break;
                                                case 'error':
                                                    displayMessage(
                                                        response.message ||
                                                        Object.keys(response.errors).map(function(key) {
                                                            return response.errors[key];
                                                        }).join('<br>'),
                                                        'alert'
                                                    );
                                                    break;
                                                default:
                                                    displayMessage(
                                                        response.message || 'Something went wrong.',
                                                        'alert'
                                                    );
                                            }
                                        }
                                    });
                                }
                                break;
                            default:
                                console.error('Unknown Action:', action);
                        }
                    }
                });
            }
        });
    });
</script>

<?php include('includes/body-close.php'); ?>
