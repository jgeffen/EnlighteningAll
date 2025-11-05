<?php
/*
	Copyright (c) 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/

namespace Admin;

use Database;

const LOGGED_IN  = TRUE;
const LOGGED_OUT = FALSE;

/**
 * Checks basic authentication.
 *
 * @param string|null $user
 * @param string|null $pass
 * @param bool        $report
 *
 * @return bool
 */
function BasicAuth(?string $user, ?string $pass, bool $report = TRUE): bool {
	if (empty($_SERVER['PHP_AUTH_USER'])) return FALSE;

	if ($_SERVER['PHP_AUTH_USER'] != $user) {
		$report && error_log(sprintf("user %s not found: %s (%s)", $_SERVER['PHP_AUTH_USER'], filter_input(INPUT_SERVER, 'REQUEST_URI'), $user ?? 'NULL'));
		return FALSE;
	}

	if (empty($_SERVER['PHP_AUTH_PW'])) return FALSE;

	if ($_SERVER['PHP_AUTH_PW'] != $pass) {
		$report && error_log(sprintf("user %s: authentication failure for \"%s/\": Password Mismatch", $_SERVER['PHP_AUTH_USER'], filter_input(INPUT_SERVER, 'REQUEST_URI')));
		return FALSE;
	}

	return TRUE;
}

/**
 * Checks if categories are enabled.
 *
 * @param string $table_name
 *
 * @return bool
 */
function Categories(string $table_name): bool {
	return !empty($_SESSION['admin']['settings']['tables'][$table_name]['categories']);
}

/**
 * Returns text set by SetMessage()
 *
 * @return string|null
 */
function GetMessage(): ?string {
	return $_SESSION['admin']['message']['text'] ?? NULL;
}

/**
 * Checks if member is logged in.
 *
 * @param bool $status TRUE (Logged In) || FALSE (Logged Out)
 *
 * @return bool
 */
function LoggedIn(bool $status = TRUE): bool {
	return !empty($_SESSION['admin']) === $status;
}

/**
 * Checks privilege based on user type.
 *
 * @param int|int[] $user_types
 * @param bool      $inherit
 *
 * @return bool
 */
function Privilege(array|int $user_types, bool $inherit = TRUE): bool {
	$admin     = new User();
	$user_type = $admin->getUserType()?->getUserType() ?: 9999;

	if (is_array($user_types)) return in_array($user_type, $user_types);
	if ($inherit) return $user_type <= $user_types;

	return $user_type == $user_types;
}

/**
 * Redirects user.
 *
 * @param string $url
 */
function Redirect(string $url): never {
	header(sprintf("Location: %s", $url));
	exit;
}

/**
 * Returns the number of unanswered reports.
 *
 * @param string $type OPTIONS: 'all', 'comments', 'messages', 'posts', 'profiles'
 *
 * @return int
 */
function Reports(string $type = 'all'): int {
	return match ($type) {
		'all'      => Reports('comments') + Reports('messages') + Reports('posts') + Reports('profiles') + Reports('tickets'),
		'comments' => Database::Action("SELECT COUNT(`id`) FROM `member_post_comment_reports` WHERE `status` = 'PENDING'")->fetchColumn(),
		'messages' => Database::Action("SELECT COUNT(`id`) FROM `member_message_reports` WHERE `status` = 'PENDING'")->fetchColumn(),
		'posts'    => Database::Action("SELECT COUNT(`id`) FROM `member_post_reports` WHERE `status` = 'PENDING'")->fetchColumn(),
		'profiles' => Database::Action("SELECT COUNT(`id`) FROM `member_reports` WHERE `status` = 'PENDING'")->fetchColumn(),
		'tickets'  => Database::Action("SELECT COUNT(`id`) FROM `member_tickets` WHERE `read` = FALSE AND `initiated_by` = 'member' GROUP BY COALESCE(`member_ticket_id`, `id`)")->fetchColumn(),
		default    => 0
	};
}

/**
 * Sets message to be used by JavaScript function alertMessage()
 *
 * @param string $text
 * @param string $type
 * @param string $url
 */
function SetMessage(string $text, string $type = 'info', string $url = ''): void {
	$_SESSION['admin']['message'] = array('text' => $text, 'type' => $type);

	if (!empty($url)) Redirect($url);
}
