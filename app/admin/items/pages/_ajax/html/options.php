<?php
	/* Turn On Output Buffering */
	ob_start();
?>

<nav id="nav-options">
	<ul>
		<li>
			<a class="icon-labeled" href="<?php echo sprintf("/user/edit/pages/%d", $event['id']); ?>">
				<i class="far fa-edit"></i>
				<span>Edit</span>
			</a>
		</li>
		<li>
			<a class="icon-labeled" href="<?php echo sprintf("/user/manage/images/pages/%d.html", $event['id']); ?>">
				<i class="fas fa-images"></i>
				<span>Images</span>
			</a>
		</li>
		<li>
			<a class="icon-labeled" href="<?php echo sprintf("/user/manage/pdfs/pages/%d.html", $event['id']); ?>">
				<i class="fas fa-file-pdf"></i>
				<span>PDFs</span>
			</a>
		</li>
		<li>
			<a class="icon-labeled" href="#" data-action="delete">
				<i class="far fa-trash"></i>
				<span>Delete</span>
			</a>
		</li>
	</ul>
</nav>

<?php
	/* Return Current Buffer Contents and Delete Current Output Buffer */
	return ob_get_clean();
?>

