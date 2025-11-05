<?php
	/**
	 * @var string $qd_name
	 * @var mixed  $qd_value
	 */
	
	// Reset Variables
	$qd_name  ??= '';
	$qd_value ??= '';
?>

<input type="hidden" name="<?php echo $qd_name; ?>" value="<?php echo $qd_value; ?>">