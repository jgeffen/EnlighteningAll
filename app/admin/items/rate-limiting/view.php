<?php
	//phpinfo();
	
	// Set Title
	$page_title = 'Members Exceeding Maximums';
	
	// Start Header
	include('includes/header.php');
?>

<main class="page-content">
	<section id="view-table" role="region">
		<div id="page-title-btn">
			<h1><?php echo $page_title; ?></h1>
		</div>
		
		<div class="row">
			<div class="col-12">
				<table id="data-table" class="table table-bordered nowrap" style="width:100%;">
					<thead>
						<tr>
							<th>Member ID</th>
							<th>Username</th>
							<th>Name</th>
							<th>Email</th>
							<th>User Agent</th>
							<th>IP Address</th>
							<th>Exceeded</th>
							<th>Options</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</section>
</main>

<?php include('includes/footer.php'); ?>

<script>

</script>