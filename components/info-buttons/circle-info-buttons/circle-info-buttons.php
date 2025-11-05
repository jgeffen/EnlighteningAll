<div class="circle-info-buttons container-fluid">
	<div class="container">
		<div class="row">
			<?php foreach($options as $button): ?>
				<div class="col-sm-6 col-lg-3">
					<a href="<?php echo $button['link']; ?>" class="circle-button" style="background: url(<?php echo $button['image']; ?>)">
						<div class="overlay <?php echo !empty($button['overlay']) ? 'overlay-' . $button['overlay'] : ''; ?>"></div>
						<h2 class="circle-title"><?php echo $button['title']; ?></h2>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<script>
	
</script>