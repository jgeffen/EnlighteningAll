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

namespace TravelAffiliateMembers;

use Exception;
use Items\Members\Ticket;
use TravelAffiliateMembership;
use Options;
use Render as RenderDefault;
use Router;
use Router\Dispatcher;

class Render {
	/**
	 * @param Router\Dispatcher $dispatcher
	 */
	public static function Ajax(Router\Dispatcher $dispatcher): never {
		$member    = new TravelAffiliateMembership();
		$dir_path  = sprintf("%s/app/ajax/travel-affiliate-members", dirname(__DIR__, 3));
		$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('script'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
			exit;
		}

		RenderDefault::ErrorDocument(404);
	}



	/**
	 * @param Router\Dispatcher $dispatcher
	 * @param string            $dir
	 *
	 * @return bool
	 */
	public static function Management(Router\Dispatcher $dispatcher, string $dir = 'members/posts'): bool {
		if (Options::Init('non_member_pages')->hasKey($dispatcher->getPageUrl())) {
			if (Options::Init('non_member_pages')->getValue($dispatcher->getPageUrl())) {
				TravelAffiliateMembership::CheckRedirect(FALSE, '/travel-affiliate-members/dashboard');
			}
		} else TravelAffiliateMembership::CheckRedirect(TRUE, '/travel-affiliate-members/login');

		$member    = new TravelAffiliateMembership();
		$dir_path  = sprintf("%s/app/%s", dirname(__DIR__, 3), $dir);
		$file_path = sprintf("%s/%s/%s", $dir_path, $dispatcher->getOption('type'), $dispatcher->getOption('action'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param Router\Dispatcher $dispatcher
	 * @param string            $dir
	 *
	 * @return never
	 */
	public static function Page(Router\Dispatcher $dispatcher, string $dir = 'travel-affiliate-members'): never {
		if (Options::Init('non_member_pages')->hasKey($dispatcher->getPageUrl())) {
			if (Options::Init('non_member_pages')->getValue($dispatcher->getPageUrl())) {
				TravelAffiliateMembership::CheckRedirect(FALSE, '/travel-affiliate-members/dashboard');
			}
		} else TravelAffiliateMembership::CheckRedirect(TRUE, '/travel-affiliate-members/login');

		$member  = new TravelAffiliateMembership();
		$dir_path  = sprintf("%s/app/%s", dirname(__DIR__, 3), $dir);
		$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getPageUrl());
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
			exit;
		}

		RenderDefault::ErrorDocument(404);
	}
}
