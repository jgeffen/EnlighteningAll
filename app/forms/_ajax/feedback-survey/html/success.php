<?php
	/* Turn On Output Buffering */
	ob_start();
?>

<div class="text-center">
	<i class="fa-light fa-check icon-lg" style="margin: 10px 0 30px;" aria-hidden="true"></i>
	<h2>Thank You!</h2>
	<p class="mb-4">We greatly appreciate your feedback.</p>
</div>

<?php
	/* Return Current Buffer Contents and Delete Current Output Buffer */
	return ob_get_clean();
?>

