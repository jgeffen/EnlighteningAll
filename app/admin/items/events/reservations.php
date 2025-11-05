<?php
/*
    Copyright (c) 2021, 2022 FenclWebDesign.com
*/
$page_title = 'View Reservations';
include('includes/header.php');
?>

<main class="page-content">
    <section id="view-table" role="region">
        <div id="page-title-btn">
            <h1><?php echo $page_title; ?></h1>
        </div>

        <span class="d-block mb-3">
            <strong class="text-muted">Legend:</strong>
            <span class="badge bg-success text-light">Paid</span>
            <span class="badge bg-danger text-light">Unpaid</span>
        </span>

        <div class="row">
            <div class="col-12 table-responsive">
                <table id="data-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Name on Pass</th>
                        <th>Event</th>
                        <th>Package</th>
                        <th>Items</th>
                        <th>Song</th>
                        <th>Seat#</th>
                        <th>Timestamp</th>
                        <th style="max-width: 200px">Notes</th>
                        <th>Edit</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</main>

<!-- ===========================
     EDIT MODAL
=========================== -->
<div class="modal fade" id="editReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl"> <!-- ✅ Wider & Centered -->
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Reservation</h5>
            </div>

            <div class="modal-body">
                <form id="editReservationForm" class="container-fluid">
                    <input type="hidden" name="id" id="edit-id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit-name" class="form-label fw-bold">Name on Pass</label>
                            <input type="text" class="form-control" name="name_on_pass" id="edit-name">
                        </div>
                        <div class="col-md-3">
                            <label for="edit-seat" class="form-label fw-bold">Seat #</label>
                            <input type="text" class="form-control" name="seat_selected" id="edit-seat">
                        </div>
                        <div class="col-md-3">
                            <label for="edit-song" class="form-label fw-bold">Song</label>
                            <input type="text" class="form-control" name="song_selected" id="edit-song">
                        </div>

                        <div class="col-12">
                            <label for="edit-notes" class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" name="notes" id="edit-notes" rows="5" placeholder="Add notes here..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" id="cancelEditBtn">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditBtn">
                    <i class="fa-solid fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* ✅ Modal Mobile Adjustments */
    @media (max-width: 767px) {
        .modal-dialog {
            max-width: 95%;
            margin: 0.5rem auto;
        }
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    }
</style>

<?php include('includes/footer.php'); ?>

<script>
    $(function() {
        var table = $('#data-table').DataTable({
            ajax: {
                type: 'GET',
                dataSrc: function(json) {
                    if (json.status === 'success') return json.data;
                    console.error(json.message);
                    return [];
                },
                error: function(xhr) {
                    console.error('AJAX Load Error:', xhr.responseText);
                    alert('Error loading reservations.');
                }
            },
            columns: [
                { data: 'id' },
                { data: 'status.display' },
                { data: 'total_amount' },
                { data: 'name_on_pass' },
                { data: 'event.display' },
                { data: 'package.display' },
                { data: 'item_count' },
                { data: 'item.song_selected' },
                { data: 'item.seat_selected' },
                { data: 'timestamp' },
                { data: 'notes' },
                {
                    data: null,
                    render: function(d, t, r) {
                        return `<button class="btn btn-sm btn-primary edit-btn" data-id="${r.id}">
                                    <i class="fa fa-edit"></i> Edit
                                </button>`;
                    }
                }
            ]
        });

        // ================================
        // Open modal and fill data
        // ================================
        $(document).on('click', '.edit-btn', function() {
            var data = table.row($(this).closest('tr')).data();
            $('#edit-id').val(data.id);
            $('#edit-name').val(data.item.name_on_pass);
            $('#edit-seat').val(data.item.seat_selected);
            $('#edit-song').val(data.item.song_selected);
            $('#edit-notes').val(data.notes);
            $('#editReservationModal').modal('show');
        });

        // ================================
        // Save changes
        // ================================
        $('#saveEditBtn').on('click', function() {
            var formData = $('#editReservationForm').serialize() + '&action=update';
            $.ajax({
                method: 'POST',
                dataType: 'json',
                data: formData,
                success: function(res) {
                    if (res.status === 'success') {
                        $('#editReservationModal').modal('hide');
                        table.ajax.reload(null, false);
                        alert('Reservation updated!');
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    alert('Update failed.');
                    console.error(xhr.responseText);
                }
            });
        });

        // =============================
        // ✅ Force Close Modal — Hybrid Fix
        // =============================
        $(document).on('click', '#cancelEditBtn', function () {
            var $modal = $('#editReservationModal');

            // Try Bootstrap 5 native API first
            try {
                var bsModal = bootstrap.Modal.getInstance(document.getElementById('editReservationModal'));
                if (bsModal) {
                    bsModal.hide();
                    return;
                }
            } catch(e) {
                console.warn('Bootstrap 5 modal instance not found, trying jQuery fallback...');
            }

            // Fallback for Bootstrap 4 (jQuery-based modals)
            if ($modal.modal) {
                $modal.modal('hide');
            } else {
                $modal.removeClass('show').fadeOut();
            }
        });

        // =============================
        // ⌨ Keyboard Shortcuts
        // =============================
        $(document).on('keydown', function(e) {
            var modal = $('#editReservationModal');
            if (modal.is(':visible')) {
                if (e.key === 'Escape') {
                    // ESC = close modal
                    try {
                        var bsModal = bootstrap.Modal.getInstance(document.getElementById('editReservationModal'));
                        if (bsModal) bsModal.hide();
                        else modal.modal('hide');
                    } catch {
                        modal.modal('hide');
                    }
                } else if (e.key === 'Enter' && !$(e.target).is('textarea')) {
                    // ENTER = save changes (unless typing in textarea)
                    e.preventDefault();
                    $('#saveEditBtn').trigger('click');
                }
            }
        });
    });
</script>

<?php include('includes/body-close.php'); ?>
