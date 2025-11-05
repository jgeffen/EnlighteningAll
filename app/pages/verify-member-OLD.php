<?php

	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
try{

	$parsed = parse_url($_SERVER['REQUEST_URI']);

	if(isset($parsed['query'])){
		$qr_code = Database::Action("SELECT * FROM `member_verify_qr_codes` mvqc WHERE `mvqc`.`hash` = :hash AND TIMESTAMPDIFF(MINUTE, GREATEST(`mvqc`.`timestamp`, `mvqc`.`last_timestamp`), NOW()) <= :minutes", array(
					'hash'      => $parsed['query'],
					'minutes'   => 5
				))->fetchAll(PDO::FETCH_ASSOC);
	if($qr_code){
        $qr_code = Database::Action("SELECT * FROM `member_verify_qr_codes` mvqc WHERE `mvqc`.`hash` = :hash AND TIMESTAMPDIFF(MINUTE, GREATEST(`mvqc`.`timestamp`, `mvqc`.`last_timestamp`), NOW()) <= :minutes", array(
                'hash'      => $parsed['query'],
                'minutes'   => 5
        ))->fetchAll(PDO::FETCH_ASSOC);
		//$location = Database::Action("SELECT * FROM `member_location`  WHERE `member_id` = :member_id ", array(
		//			'member_id'      => $qr_code[0]['member_id']
		//		))->fetchAll(PDO::FETCH_ASSOC);
	}


		if(!$qr_code){

			//throw new Exception('This QR code has expired!');
			$expired = "This QR code has expired!";
		}
	}


} catch (Exception $exception) {

	Render::ErrorDocument(404);
}
// Search Engine Optimization
$page_title       = "verify QR Codes";
$page_description = "";

// Start Header
include('includes/header.php');
?>

<div class="container-fluid main-content ">
	<div class="container">
		<div class="row">
		<p><?php if(isset($expired)): echo $expired;  endif;?></p>
			<p><?php if(isset($success)): echo $success;  endif;?></p>

		</div>
	</div>
</div>



<?php include('includes/footer.php'); ?>
<script>
navigator.geolocation.getCurrentPosition(function(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;

    // Send this data to your server for verification
	Cookies.set('latitude', latitude, { expires: 1 }); // expires in 1 day
	Cookies.set('longitude', longitude, { expires: 1 });


});
</script>

<?php
$massage = 0;
if(isset($location)){
	// Get a specific cookie
	$latitude = $_COOKIE['latitude'];
	$longitude = $_COOKIE['longitude'];
	$lat2 = $location[0]['latitude'];
	$lon2 = $location[0]['longitude'];
	// Check if cookie exists before using
	if(isset($_COOKIE['longitude'])) {
		// Distance Calculation
		$earthRadius = 6371000; // Earth radius in meters

		$dLat = deg2rad($lat2 - $latitude);
		$dLon = deg2rad($lon2 - $longitude);

		$a = sin($dLat / 2) * sin($dLat / 2) +
			 cos(deg2rad($latitude)) * cos(deg2rad($lat2)) *
			 sin($dLon / 2) * sin($dLon / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$distance = $earthRadius * $c;

		if ($distance <= 50) {
			 //echo "Users are within proximity!";
			$massage = 1;
		} else {
			//echo "Users are too far apart.";
			$massage = 2;
		}


	} else {
		//echo "Cookie is not set";
		$massage = 3;
	}
}
?>

<?php if(isset($expired)): ?>
<script>
displayMessage("This QR code has expired!", "alert");
	</script>
<?php endif; ?>
<?php
switch ($massage) {
  case 1: ?>
    <script>
		displayMessage("Member proximity detected: You are within 50 meters of the designated location.", "success");
	</script>
 <?php break;
  case 2: ?>
    <script>
		displayMessage("Please enable location services in your browser settings to continue.", "alert");
	</script>
  <?php break;
  case 3: ?>
    <script>
		displayMessage("Member is too far away.", "alert");
	</script>
  <?php  break;
  default:
    //code block
}

?>
<?php include('includes/body-close.php'); ?>