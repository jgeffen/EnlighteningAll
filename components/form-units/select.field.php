<?php
	/**
	 * @var string $qd_column
	 * @var string $qd_form
	 * @var bool   $qd_horizontal
	 * @var string $qd_id
	 * @var string $qd_label
	 * @var array  $qd_options Values => Label
	 * @var mixed  $qd_value
	 */
	
	// Reset Variables
	$qd_form       ??= 'form';
	$qd_column     ??= '';
	$qd_label      ??= ucwords(implode(' ', explode('_', $qd_column)));
	$qd_id         ??= sprintf("%s-select-%s", $qd_form, $qd_column);
	$qd_options    ??= array('' => '- None -');
	$qd_horizontal ??= TRUE;
	$qd_default    ??= '';
	$qd_value      ??= $qd_default;
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
			<div class="select-wrap form-control">
				<select id="<?php echo $qd_id; ?>" name="<?php echo $qd_column; ?>" data-value="<?php echo is_bool($qd_value) ? (int)$qd_value : $qd_value; ?>">
					<?php foreach($qd_options as $option_value => $option_text): ?>
						<option value="<?php echo $option_value; ?>">
							<?php echo $option_text; ?>
						</option>
					<?php endforeach; ?>
				</select>
				<div class="select-box"></div>
			</div>
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
			
			<div class="select-wrap form-control">
				<select id="<?php echo $qd_id; ?>" name="<?php echo $qd_column; ?>" data-value="<?php echo is_bool($qd_value) ? (int)$qd_value : $qd_value; ?>">
					<?php foreach($qd_options as $option_value => $option_text): ?>
						<option value="<?php echo $option_value; ?>">
							<?php echo $option_text; ?>
						</option>
					<?php endforeach; ?>
				</select>
				<div class="select-box"></div>
			</div>
		</div>
	<?php endif; ?>
</div>