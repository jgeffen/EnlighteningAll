<?php

	$component_classes = array(
		!empty($options['background_color']) ? 'component-background-' . $options['background_color'] : '',
		!empty($options['text_color']) ? 'component-text-' . $options['text_color'] : '',
		!empty($options['text_align']) ? 'text-md-' . $options['text_align'] : '',
		!empty($options['sticky']) ? 'component-fixed' : '',
		!empty($options['position']) ? 'position-' . $options['position'] : ''
	);
	
	$button_classes = array(
		!empty($options['btn_style']) ? 'btn-' . $options['btn_style'] : 'btn-primary',
		!empty($options['btn_size']) ? 'btn-' . $options['btn_size'] : ''
	);	
	
?>

<?php if(!empty($options['sticky'])): ?>
	<script>	
		document.addEventListener('DOMContentLoaded', function(){
			var alertBar = $("#alert-bar");
			var mainWrapper = $("#main-wrapper");
			$(window).on('load resize', function(){
				var alertHeight = alertBar.outerHeight();
				mainWrapper.css('padding-<?php echo $options['position']; ?>', alertHeight + 'px');
			});
		});
	</script>
<?php endif; ?>

<?php if(!empty($options['enabled'])): ?>
	<section class="container-fluid <?php echo implode(' ', $component_classes); ?>" id="alert-bar" aria-label="<?php echo $options['title']; ?>">
		<div class="container">
			<div class="row align-items-center">
				<div class="<?php echo !empty($options['btn_text']) ? 'col-md-8 col-lg-9' : 'col'; ?>">
					<div class="content-wrap" style="<?php echo !empty($options['max_width']) ? 'max-width: ' . $options['max_width'] . 'px' : ''; ?>">
						<?php if(!empty($options['html'])): ?>
							<?php echo $options['html']; ?>
						<?php else: ?>
							<?php if(!empty($options['title'])): ?>
								<h2 class="<?php echo !empty($options['content']) ? 'title-underlined' : ''; ?>"><?php echo $options['title']; ?></h2>
							<?php endif; ?>
							<?php if(!empty($options['content'])): ?>
								<p><?php echo nl2br($options['content']); ?></p>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<?php if(!empty($options['btn_text'])): ?>
					<div class="col-md-4 col-lg-3">
						<a href="<?php echo !empty($options['btn_link']) ? $options['btn_link'] : ''; ?>" class="btn btn-block mt-3 mt-md-0 <?php echo implode(' ', $button_classes); ?>">
							<?php echo $options['btn_text']; ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>