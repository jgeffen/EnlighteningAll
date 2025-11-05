<?php
	/* Turn On Output Buffering */
	ob_start();
?>

<div class="text-center">
	<i class="fal fa-check icon-lg" style="margin: 10px 0 30px;" aria-hidden="true"></i>
	<h2>Thank You!</h2>
	<p class="mb-4">We greatly appreciate you booking this photography package with us. We'll be in touch to finalize the details before processing your payment.</p>
</div>

<?php
	/* Return Current Buffer Contents and Delete Current Output Buffer */
	return ob_get_clean();
?>

