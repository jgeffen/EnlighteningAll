<?php
	/**
	 * @var string $qd_column
	 * @var string $qd_form
	 * @var bool   $qd_horizontal
	 * @var string $qd_id
	 * @var string $qd_label
	 * @var int    $qd_max_length
	 * @var int    $qd_rows
	 * @var string $qd_validate
	 * @var mixed  $qd_value
	 */
	
	// Reset Variables
	$qd_form       ??= 'form';
	$qd_column     ??= '';
	$qd_label      ??= ucwords(implode(' ', explode('_', $qd_column)));
	$qd_validate   ??= '';
	$qd_id         ??= sprintf("%s-textarea-%s", $qd_form, $qd_column);
	$qd_horizontal ??= TRUE;
	$qd_rows       ??= 4;
	$qd_max_length ??= 255;
	$qd_value      ??= '';
?>

<div class="form-group row">
	<?php if($qd_horizontal): ?>
		<?php if(!empty($qd_label)): ?>
			<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="<?php echo $qd_id; ?>">
				<?php echo $qd_label; ?>
				<?php if(!str_ends_with($qd_label, '?')): ?>
					:
				<?php endif; ?>
			</label>
		<?php endif; ?>
		
		<div class="col-lg-9">
			<textarea id="<?php echo $qd_id; ?>" class="form-control" name="<?php echo $qd_column; ?>" maxlength="<?php echo $qd_max_length ?>" rows="<?php echo $qd_rows; ?>" <?php echo !empty($qd_validate) ? sprintf("required data-type=\"%s\" placeholder=\"* Required\"", $qd_validate) : ''; ?>><?php echo $qd_value; ?></textarea>
		</div>
	<?php else: ?>
		<div class="col-lg-12">
			<?php if(!empty($qd_label)): ?>
				<label class="col-form-label" for="<?php echo $qd_id; ?>">
					<?php echo $qd_label; ?>
					<?php if(!str_ends_with($qd_label, '?')): ?>
						:
					<?php endif; ?>
				</label>
			<?php endif; ?>
			
			<textarea id="<?php echo $qd_id; ?>" class="form-control" name="<?php echo $qd_column; ?>" maxlength="<?php echo $qd_max_length ?>" rows="<?php echo $qd_rows; ?>" <?php echo !empty($qd_validate) ? sprintf("required data-type=\"%s\" placeholder=\"* Required\"", $qd_validate) : ''; ?>><?php echo $qd_value; ?></textarea>
		</div>
	<?php endif; ?>
</div>