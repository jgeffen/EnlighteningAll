<?php
	$image_order = (empty($options['image_position']) || $options['image_position'] == 'left')
		? 'first'
		: 'last';
	
	$breakout_classes = array(
		!empty($options['background_color']) ? 'component-background-' . $options['background_color'] : '',
		!empty($options['background_image']) ? 'background-image' : '',
		!empty($options['background_image_scroll']) && !empty($options['background_image']) ? $options['background_image_scroll'] : ''
	);
	
	$breakout_styles = array(
		!empty($options['background_image']) ? 'background-image: url(' . $options['background_image'] . ')' : '',
		!empty($options['height']) ? 'height: ' . $options['height'] : ''
	);
	
	$text_wrap_classes = array(
		empty($options['full_width_content']) ? 'container' : '',
		empty($options['image']) && empty($options['map']) ? 'no-inset-image' : '',
		!empty($options['text_color']) ? 'component-text-' . $options['text_color'] : ''
	);
	
	//Set component ID variable for targeting
	$content_breakout_id = $options['content_breakout_id'] ?? 1;

?>

<section id="content-breakout-id-<?php echo $content_breakout_id; ?>" class="container-fluid content-breakout <?php echo implode(' ', $breakout_classes); ?>" style="<?php echo implode(';', $breakout_styles); ?>">
	<div class="row no-gutters">
		<?php if(!empty($options['image']) && empty($options['map'])): ?>
			<div class="order-first col-lg-5 order-lg-<?php echo ($image_order == 'first') ? 'first' : 'last'; ?>">
				<div class="img-wrap equal-content" style="background-image: url(<?php echo $options['image']; ?>);"></div>
			</div>
		<?php endif; ?>
		<?php if(!empty($options['map'])): ?>
			<div class="order-first col-lg-5 order-lg-<?php echo ($image_order == 'first') ? 'first' : 'last'; ?>">
				<div class="map-wrap equal-content"><?php echo $options['map']; ?></div>
			</div>
		<?php endif; ?>
		<div class="order-last <?php echo (!empty($options['image']) || !empty($options['map'])) ? 'col-lg-7' : 'col'; ?> order-lg-<?php echo ($image_order == 'first') ? 'last' : 'first'; ?>">
			<div class="text-wrap equal-content <?php echo implode(' ', $text_wrap_classes); ?>">
				<?php if(!empty($options['html'])): ?>
					<?php echo $options['html']; ?>
				<?php else: ?>
					<?php if(!empty($options['title'])): ?>
						<h2 class="<?php echo !empty($options['super_title']) ? 'title-super-' . $options['super_title'] : 'breakout-title'; ?>">
							<?php echo $options['title']; ?>
						</h2>
						<hr>
					<?php endif; ?>
					<?php if(!empty($options['content'])): ?>
						<p><?php echo nl2br($options['content']); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php if(!empty($options['background_image_scroll'])) : ?>
	<?php if($options['background_image_scroll'] == 'parallax' || $options['background_image_scroll'] == 'fixed'): ?>
		<script>
			(function() {
				//Apply fixed positioning to the background image if it's not mobile or IE
				if(!/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Trident.*rv[ :]*11\.|Opera Mini/i.test(navigator.userAgent)) {
					var x, i;
					x = document.querySelectorAll("#content-breakout-id-<?php echo $content_breakout_id; ?>");
					for(i = 0; i < x.length; i++) {
						x[i].style.backgroundAttachment = 'fixed';
					}
					
				}
			})();
		</script>
	<?php endif; ?>
<?php endif; ?>

