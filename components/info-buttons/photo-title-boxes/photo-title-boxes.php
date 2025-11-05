<div class="container-fluid photo-title-boxes">
	<div class="row no-gutters">
		<?php foreach($options['data'] as $button): ?>
			<div class="col-6 col-sm-6 col-md-3">
				<a href="<?php echo $button['link']; ?>" class="btn-wrap" aria-label="<?php echo $button['title']; ?>">
					<img src="<?php echo $button['image']; ?>" alt="<?php echo $button['alt']; ?>">
					<h3 class="btn-title <?php echo !empty($options['longer-titles']) ? 'equal-title' : ''; ?> <?php echo !empty($button['icon']) ? 'icon' : ''; ?>">
						<?php echo !empty($button['icon']) ? $button['icon'] : ''; ?>
						<?php echo $button['title']; ?>
					</h3>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>