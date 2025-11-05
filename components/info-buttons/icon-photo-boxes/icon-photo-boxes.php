<?php
	/**
	 * @var array{
	 *     image: string,
	 *     icon: string,
	 *     overlay: string,
	 *     title: string,
	 *     link: string
	 * } $qd_buttons
	 */
?>

<div class="icon-photo-boxes">
	<?php foreach($qd_buttons as $button): ?>
		<a href="<?php echo $button['link']; ?>" class="info-box" style="background: url(<?php echo $button['image']; ?>)">
			<div class="overlay overlay-<?php echo $button['overlay']; ?>"></div>
			<?php echo $button['icon']; ?>
			<h2 class="equal-title"><?php echo $button['title']; ?></h2>
		</a>
	<?php endforeach; ?>
	<div class="clear"></div>
</div>