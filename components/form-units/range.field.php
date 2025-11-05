<?php
	/**
	 * @var string $qd_column
	 * @var string $qd_form
	 * @var bool   $qd_horizontal
	 * @var string $qd_id
	 * @var string $qd_label
	 * @var int    $qd_max
	 * @var int    $qd_min
	 * @var string $qd_prefix
	 * @var int    $qd_step
	 * @var string $qd_suffix
	 */
	
	// Reset Variables
	$qd_form       ??= 'form';
	$qd_column     ??= '';
	$qd_label      ??= ucwords(implode(' ', explode('_', $qd_column)));
	$qd_min        ??= 0;
	$qd_max        ??= 100;
	$qd_step       ??= 1;
	$qd_prefix     ??= '';
	$qd_suffix     ??= '';
	$qd_id         ??= sprintf("%s-range-%s", $qd_form, $qd_column);
	$qd_horizontal ??= TRUE;
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
			<div class="range-slider">
				<input id="<?php echo $qd_id; ?>" class="range-slider__range custom-range" type="range" name="<?php echo $qd_column; ?>" data-prefix="<?php echo $qd_prefix; ?>" data-suffix="<?php echo $qd_suffix; ?>" max="<?php echo $qd_max; ?>" min="<?php echo $qd_min; ?>" step="<?php echo $qd_step; ?>">
				<span class="range-slider__value">0</span>
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
			
			<div class="range-slider">
				<input id="<?php echo $qd_id; ?>" class="range-slider__range custom-range" type="range" name="<?php echo $qd_column; ?>" data-prefix="<?php echo $qd_prefix; ?>" data-suffix="<?php echo $qd_suffix; ?>" max="<?php echo $qd_max; ?>" min="<?php echo $qd_min; ?>" step="<?php echo $qd_step; ?>">
				<span class="range-slider__value">0</span>
			</div>
		</div>
	<?php endif; ?>
</div>
