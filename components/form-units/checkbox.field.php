<?php
	/**
	 * @var string $qd_column
	 * @var string $qd_form
	 * @var bool   $qd_horizontal
	 * @var string $qd_id
	 * @var string $qd_label
	 * @var array  $qd_options Values => Label
	 * @var array  $qd_values
	 */
	
	// Reset Variables
	$qd_form       ??= 'form';
	$qd_column     ??= '';
	$qd_label      ??= ucwords(implode(' ', explode('_', $qd_column)));
	$qd_id         ??= sprintf("%s-checkbox-%s", $qd_form, $qd_column);
	$qd_options    ??= array('' => '- None -');
	$qd_horizontal ??= TRUE;
	$qd_values     ??= array();
?>

<div class="form-group row">
	<?php if($qd_horizontal): ?>
		<?php if(!empty($qd_label)): ?>
			<label id="<?php echo $qd_id; ?>" class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1">
				<?php echo $qd_label; ?>
				<?php if(!str_ends_with($qd_label, '?')): ?>
					:
				<?php endif; ?>
			</label>
		<?php endif; ?>
		
		<div class="col-lg-9">
			<?php foreach($qd_options as $value => $label): ?>
				<?php $checked = in_array($value, $qd_values) ? 'checked' : ''; ?>
				<div class="checkbox">
					<span class="check-btn">
						<input class="form-control" type="checkbox" name="<?php echo $qd_column; ?>[]" value="<?php echo htmlspecialchars($value, ENT_COMPAT); ?>" aria-labelby="<?php echo $qd_id; ?>" <?php echo $checked; ?>>
					</span>
					<span aria-hidden="true"><?php echo $label; ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<div class="col-lg-12">
			<?php if(!empty($qd_label)): ?>
				<label id="<?php echo $qd_id; ?>" class="col-form-label">
					<?php echo $qd_label; ?>
					<?php if(!str_ends_with($qd_label, '?')): ?>
						:
					<?php endif; ?>
				</label>
			<?php endif; ?>
			
			<?php foreach($qd_options as $value => $label): ?>
				<?php $checked = in_array($value, $qd_values) ? 'checked' : ''; ?>
				<div class="checkbox">
					<span class="check-btn">
						<input class="form-control" type="checkbox" name="<?php echo $qd_column; ?>[]" value="<?php echo $value; ?>" aria-labelby="<?php echo $qd_id; ?>" <?php echo $checked; ?>>
					</span>
					<span aria-hidden="true"><?php echo $label; ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>