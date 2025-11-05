<div class="form-group">
	<?php if(!empty($options['label'])): ?>
		<?php if(!empty($options['tooltip'])): ?>
			<label for="<?php echo $options['id']; ?>">
				<a href="#" data-tool-tip="<?php echo htmlentities($options['tooltip'], ENT_QUOTES); ?>">
					<i class="fal fa-info-circle"></i>
					<span><?php echo $options['label']; ?></span>&nbsp;:
				</a>
			</label>
		<?php else: ?>
			<label for="<?php echo $options['id']; ?>">
				<span><?php echo $options['label']; ?></span>&nbsp;:
			</label>
		<?php endif; ?>
	<?php endif; ?>
	
	<input
		id="<?php echo $options['id']; ?>"
		class="<?php echo $options['class']; ?>"
		type="<?php echo $options['type']; ?>"
		name="<?php echo $options['name']; ?>"
		value="<?php echo htmlentities($options['value'], ENT_QUOTES); ?>"
		maxlength="<?php echo $options['maxlength']; ?>"
	>
</div>