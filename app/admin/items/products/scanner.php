<?php include('includes/header.php'); ?>

<select id="camera-select" class="form-control mb-3"></select>
<video id="preview-video" width="400" height="300" autoplay playsinline style="border:1px solid #ccc;"></video>
<p id="scan-result" class="mt-2"></p>

<input type="file" id="file-input" accept="image/*" class="form-control mt-3">
<input type="hidden" id="upc_code" name="upc_code">

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>
