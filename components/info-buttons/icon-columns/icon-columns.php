<div class="container-fluid icon-columns">
	<div class="container">
		<div class="row">
			<?php foreach($options as $button): ?>
				<div class="col-lg-4">
					<div class="icon-column">
						<a <?php echo !empty($button['link']) ? 'href="' . $button['link'] . '"' : ''; ?> title="View <?php echo $button['title']; ?>">
							<?php echo $button['icon']; ?>
						</a>
						<h2 class="equal-title icon-column-title title-underlined">
							<a <?php echo !empty($button['link']) ? 'href="' . $button['link'] . '"' : ''; ?> title="View <?php echo $button['title']; ?>">
								<?php echo $button['title']; ?>
							</a>
						</h2>
						<p class="equal-content"><?php echo $button['content']; ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>