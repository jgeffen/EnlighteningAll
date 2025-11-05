<?php
	/**
	 * @var string $qd_column
	 * @var string $qd_form
	 * @var bool   $qd_horizontal
	 * @var string $qd_id
	 * @var string $qd_label
	 * @var string $qd_mask
	 * @var int    $qd_max_length
	 * @var bool   $qd_readonly
	 * @var string $qd_type
	 * @var string $qd_validate
	 * @var mixed  $qd_value
	 */
	
	// Reset Variables
	$qd_column     ??= '';
	$qd_form       ??= 'form';
	$qd_horizontal ??= TRUE;
	$qd_id         ??= sprintf("%s-input-%s", $qd_form, $qd_column);
	$qd_label      ??= ucwords(implode(' ', explode('_', $qd_column)));
	$qd_mask       ??= '';
	$qd_max_length ??= 255;
	$qd_readonly   ??= FALSE;
	$qd_type       ??= 'text';
	$qd_validate   ??= '';
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
			<input id="<?php echo $qd_id; ?>" class="form-control" type="<?php echo $qd_type; ?>" name="<?php echo $qd_column; ?>" maxlength="<?php echo $qd_max_length ?>" value="<?php echo htmlentities($qd_value); ?>" <?php echo (!empty($qd_mask)) ? 'data-format="' . $qd_mask . '"' : ''; ?> <?php echo (!empty($qd_validate)) ? 'required data-type="' . $qd_validate . '" placeholder="* Required"' : ''; ?> <?php echo $qd_readonly ? 'readonly' : ''; ?>>
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
			
			<input id="<?php echo $qd_id; ?>" class="form-control" type="<?php echo $qd_type; ?>" name="<?php echo $qd_column; ?>" maxlength="<?php echo $qd_max_length ?>" value="<?php echo htmlentities($qd_value); ?>" <?php echo (!empty($qd_mask)) ? 'data-format="' . $qd_mask . '"' : ''; ?> <?php echo (!empty($qd_validate)) ? 'required data-type="' . $qd_validate . '" placeholder="* Required"' : ''; ?>>
		</div>
	<?php endif; ?>
</div>