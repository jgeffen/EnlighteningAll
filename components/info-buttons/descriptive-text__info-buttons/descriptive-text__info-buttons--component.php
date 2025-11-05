<div class="container-fluid info-btns">
	<div class="row no-gutters">
		<?php foreach($options['data'] as $button): ?>
			<div class="col-12 col-sm-6 col-lg-3">
				<a href="<?php echo $button['link']; ?>" class="btn-wrap" aria-label="<?php echo $button['title']; ?>">
					<img src="<?php echo $button['image']; ?>" alt="<?php echo $button['alt']; ?>">
					<div class="btn-content">
						<h3 class="btn-title <?php echo !empty($options['longer-titles']) ? 'equal-title' : ''; ?> <?php echo !empty($button['icon']) ? 'icon' : ''; ?>">
							<?php echo !empty($button['icon']) ? $button['icon'] : ''; ?>
							<?php echo $button['title']; ?>
						</h3>
						<?php if(!empty($button['text'])): ?>
							<p class="btn-text equal-text"><?php echo $button['text']; ?></p>
						<?php endif; ?>
					</div>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>