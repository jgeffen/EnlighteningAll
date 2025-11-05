<?php

/**
 * Showdata Reports Data of Var
 *
 * @param bool|array $array
 * @param bool       $exit
 */
function showdata(bool|array $array = FALSE, bool $exit = TRUE): void {
	$debug = debug_backtrace();

	if ($array !== FALSE) {
		echo 'Printing array on line ' . $debug[0]['line'] . ' in file ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $debug[0]['file']);
		echo '<pre>';
		print_r($array);
		echo '</pre>';
		echo (isset($debug[1]['function'])) ? 'This data could have originated from "' . $debug[1]['function'] . '"' : '';
		echo (isset($debug[1]['class'])) ? ' in the class "' . $debug[1]['class'] . '"' : '';
		echo '<br />';
		if ($exit !== FALSE) exit();
	} else {
		echo 'Printing array on line ' . $debug[0]['line'] . ' in file ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $debug[0]['file']) . '<br /><br />';
		echo 'Q: What is the difference between Roast Beef and Pea Soup?<br />';
		echo 'A: Anyone can Roast Beef, but no one can Pea Soup! LOL!<br /><br />';
		echo 'Daerik says, "Maybe use an array next time?"';
		exit();
	}
}

/**
 * Remove tags from content and shorten with non-alphanumeric characters trimmed on end.
 *
 * @param string|null $content
 * @param int         $length
 * @param array       $options
 *
 * @return string|null
 */
function shortdesc(?string $content, int $length = 150, array $options = array()): ?string {
	// Check Content
	if (is_null($content)) return NULL;

	// Strip Headers
	$content = preg_replace('/<h(.*)<\/h[0-9]>/', '', $content);

	// Format Content
	$ellipsis = '...';
	$length   -= strlen($ellipsis);
	$content  = strip_tags(preg_replace('/\s+/', ' ', $content));
	$pos      = (strlen($content) > $length) ? strpos(wordwrap($content, $length, md5('[|]')), md5('[|]')) : strlen($content);
	if ($pos !== FALSE && strlen($content) > $length) {
		$content = substr($content, 0, $pos);
		$content = preg_replace('/[^a-z0-9]$/i', '', trim($content)) . $ellipsis;
	} elseif (strlen($content) > $length) {
		$content = substr($content, 0, $length) . $ellipsis;
	}

	// Handle Options
	if (count($options) > 0) {
		switch ($options) {
			case array_key_exists('link', $options):
				$content = $content . ' <a href="' . $options['link'] . '">' . ((!empty($options['linkTitle'])) ? $options['linkTitle'] : 'Read More') . '</a>';
				break;
			default:
				break;
		}
	}

	return $content;
}

/**
 * Fetch current page URL.
 *
 * @return string
 */
function curPageURL(): string {
	$http = !strcasecmp(filter_input(INPUT_SERVER, 'HTTPS'), 'on') ? 'https://' : 'http://';
	return $http . filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI');
}

/**
 * Fetch current website URL.
 *
 * @param string $uri OPTIONAL: append string to end of current site URL
 *
 * @return string
 */
function curSiteURL(string $uri = ''): string {
	$http = !strcasecmp(filter_input(INPUT_SERVER, 'HTTPS'), 'on') ? 'https://' : 'http://';
	return $http . filter_input(INPUT_SERVER, 'HTTP_HOST') . $uri;
}

/**
 * Initiate rpHash calculation for CAPTCHA.
 *
 * @param string $value
 *
 * @return int
 */
function rpHash(string $value): int {
	$hash  = 5381;
	$value = strtoupper($value);

	for ($i = 0; $i < strlen($value); $i++) {
		$hash = (leftShift32($hash, 5) + $hash) + ord(substr($value, $i));
	}

	return $hash;
}

/**
 * Perform a 32bit left shift for CAPTCHA.
 *
 * @param int $number
 * @param int $steps
 *
 * @return float|int
 */
function leftShift32(int $number, int $steps): float|int {
	$binary = decbin($number);
	$binary = str_pad($binary, 32, '0', STR_PAD_LEFT);
	$binary = $binary . str_repeat('0', $steps);
	$binary = substr($binary, strlen($binary) - 32);
	return ($binary[0] == '0' ? bindec($binary) : - (pow(2, 31) - bindec(substr($binary, 1))));
}

/**
 * Verifies the hash value of the CAPTCHA.
 *
 * @param string $value
 *
 * @return bool
 */
function verifyHash(string $value): bool {
	//return !strcmp(rpHash($value), filter_input(INPUT_POST, 'captchaHash'));
    return strcmp((string)rpHash($value), (string)filter_input(INPUT_POST, 'captchaHash')) === 0;

}

/**
 * Get image dimensions.
 *
 * @param string      $image_path
 * @param string      $property "width" | "height" | "aspect"
 * @param string|null $document_root
 *
 * @return int|float|null
 */
function getImageDimension(string $image_path, string $property, ?string $document_root = NULL): float|int|null {
	$document_root ??= filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');

	try {
		$image = new Imagick(sprintf("%s/%s", $document_root, ltrim($image_path, '/')));

		return match ($property) {
			'aspect' => $image->getImageWidth() / $image->getImageHeight(),
			'height' => $image->getImageHeight(),
			'width'  => $image->getImageWidth(),
			default  => NULL
		};
	} catch (ImagickException $error) {
		error_log($error->getMessage());

		return NULL;
	}
}

/**
 * Return bytes from string size
 *
 * @param string $val
 *
 * @return int
 */
function returnBytes(string $val): int {
	$val  = trim($val);
	$last = strtolower($val[strlen($val) - 1]);
	$val  = (int)$val;

	switch ($last) {
		case 'g':
			$val *= 1024;
			$val *= 1024;
			$val *= 1024;
			break;
		case 'm':
			$val *= 1024;
			$val *= 1024;
			break;
		case 'k':
			$val *= 1024;
			break;
	}

	return $val;
}

/**
 * Format bytes from filesize
 *
 * @param int $bytes
 * @param int $precision
 * @param int $percent
 *
 * @return string
 */
function formatBytes(int $bytes, int $precision = 2, int $percent = 1): string {
	$units = array('B', 'KB', 'MB', 'GB', 'TB');

	$bytes = max($bytes, 0);
	$pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow   = min($pow, count($units) - 1);
	$bytes /= (1 << (10 * $pow));

	return round($bytes * $percent, $precision) . ' ' . $units[$pow];
}

/**
 * Get max upload size in bytes
 *
 * @param bool $format_bytes
 *
 * @return string
 */
function maxFileUpload(bool $format_bytes = TRUE): string {
	/* Upload Max File Size */
	$max_upload = returnBytes(ini_get('upload_max_filesize'));

	/* POST Max Size */
	$max_post = returnBytes(ini_get('post_max_size'));

	/* Memory Limit */
	$memory_limit = returnBytes(ini_get('memory_limit'));

	/* Return Minimal Value */
	return $format_bytes ? formatBytes(min($max_upload, $max_post, $memory_limit)) : min($max_upload, $max_post, $memory_limit);
}

/**
 * Creates the SEO description.
 *
 * @param array $page_information
 *
 * @return string
 */
function create_seo_description(array $page_information): string {
	if (!empty($page_information['seo_description'])) {
		return $page_information['seo_description'];
	} elseif (!empty($page_information['content'])) {
		return shortdesc($page_information['content'], 160);
	} else {
		return '';
	}
}

function create_seo_title(array $page_information) {
	return (!empty($page_information['seo_title']))
		? $page_information['seo_title']
		: $page_information['title'] . ' - ' . SITE_NAME;
}

/**
 * Creates directory, if non-existing
 *
 * @param string $directory
 *
 * @return string
 */
function create_directory(string $directory): string {
	!is_dir($directory) && mkdir($directory, 0755, TRUE);

	return $directory;
}

function setAffiliateCookies() {
	// Check for both 'TravelAffiliate' and 'travelaffiliate' keys, regardless of case
	$affiliateKey = null;
	if (isset($_GET['TravelAffiliate'])) {
		$affiliateKey = 'TravelAffiliate';
	} elseif (isset($_GET['travelaffiliate'])) {
		$affiliateKey = 'travelaffiliate';
	} elseif (isset($_GET['TeamMember'])) {
		$affiliateKey = 'TeamMember';
	} elseif (isset($_GET['teammember'])) {
		$affiliateKey = 'teammember';
	}

	// If either key is present, proceed with setting the cookies
	if ($affiliateKey !== null) {
		// Extract and normalize the value of the affiliate ID
		$affiliateID = strtolower($_GET[$affiliateKey]);

		// Calculate the expiration time for the cookie (30 days from now)
		$expirationTime = time() + (30 * 24 * 60 * 60); // 30 days in seconds

		// Set the cookies
		setcookie('AffiliateEventCookie', $affiliateID, $expirationTime, "/");
		setcookie('AffiliateRoomCookie', $affiliateID, $expirationTime, "/");
	}
}
setAffiliateCookies();


function AffiliateTransactionSubMenu($member) { ?>
	<span class="d-block mb-3">

		<a href="/user/view/travel-affiliate-banned-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/travel-affiliate-banned-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Banned Transactions
		</a>
		<a href="/user/view/travel-affiliate-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/travel-affiliate-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Pending Event Transactions
		</a>
		<a href="/user/view/travel-affiliate-room-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/travel-affiliate-room-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Pending Room Transactions
		</a>
		<a href="/user/view/travel-affiliate-approved-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/travel-affiliate-approved-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Approved Transactions
		</a>
		<a href="/user/view/travel-affiliate-paid-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/travel-affiliate-paid-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Paid Transactions
		</a>


	</span>
<?php
}

function TeamMemberTransactionSubMenu($member) { ?>
	<span class="d-block mb-3">

		<a href="/user/view/team-member-banned-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/team-member-banned-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Banned Transactions
		</a>
		<a href="/user/view/team-member-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/team-member-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Pending Event Transactions
		</a>
		<a href="/user/view/team-member-room-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/team-member-room-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Pending Room Transactions
		</a>
		<a href="/user/view/team-member-approved-transactions/<?php echo $member->getId(); ?>" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/user/view/team-member-approved-transactions/" . $member->getId() ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Approved Transactions
		</a>



	</span>
<?php
}

function AffiliateDashBoardTransactionSubMenu($member) { ?>
	<span class="d-block mb-3">

		<a href="/travel-affiliate-members/settings" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/travel-affiliate-members/settings" ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Affiliate Info
		</a>
		<a href="/travel-affiliate-members/banned-transactions" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/travel-affiliate-members/banned-transactions" ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Banned Transactions
		</a>
		<a href="/travel-affiliate-members/dashboard" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/travel-affiliate-members/dashboard" ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Pending Transactions
		</a>
		<a href="/travel-affiliate-members/approved-transactions" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/travel-affiliate-members/approved-transactions" ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Approved Transactions
		</a>
		<a href="/travel-affiliate-members/paid-transactions" class="badge badge-light" style="border: solid .5px; padding: .5rem; <?php echo $_SERVER['REQUEST_URI'] === "/travel-affiliate-members/paid-transactions" ? "background-color:#dae0e5; border: solid 2px black" : "" ?>">
			Paid Transactions
		</a>

	</span>
<?php
}


// Function to group transactions by Year-Month
function groupTransactionsByMonth($transactions) {
	$grouped = [];
	foreach ($transactions as $transaction) {
		$yearMonth = date('F Y', strtotime($transaction['date_end']));
		if (!isset($grouped[$yearMonth])) {
			$grouped[$yearMonth] = ['events' => [], 'rooms' => []];
		}
		if ($transaction['type'] === 'event') {
			$grouped[$yearMonth]['events'][] = $transaction;
		} else if ($transaction['type'] === 'room') {
			$grouped[$yearMonth]['rooms'][] = $transaction;
		}
	}

	// Sort the grouped transactions by year and month
	uksort($grouped, function ($a, $b) {
		return strtotime($a) - strtotime($b);
	});

	return $grouped;
}
