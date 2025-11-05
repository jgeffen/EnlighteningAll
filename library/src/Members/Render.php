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
	
	namespace Members;
	
	use Exception;
	use Items\Members\Ticket;
	use Membership;
	use Options;
	use Render as RenderDefault;
	use Router;
	use Router\Dispatcher;
	
	class Render {
		/**
		 * @param Router\Dispatcher $dispatcher
		 */
		public static function Ajax(Router\Dispatcher $dispatcher): never {
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/ajax/members", dirname(__DIR__, 3));
			$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('script'));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			RenderDefault::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return never
		 */
		public static function Contest(Router\Dispatcher $dispatcher): never {
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/members/contests", dirname(__DIR__, 3));
			$file_path = realpath(sprintf("%s/%s.php", $dir_path, $dispatcher->getIntent(1)));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			RenderDefault::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return never
		 */
		public static function FAQ(Router\Dispatcher $dispatcher): never {
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/members/faqs", dirname(__DIR__, 3));
			$file_path = realpath(sprintf("%s/%s.php", $dir_path, $dispatcher->getIntent(1)));
			
			if($file_path && str_contains($file_path, $dir_path)) {
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
			if(Options::Init('non_member_pages')->hasKey($dispatcher->getPageUrl())) {
				if(Options::Init('non_member_pages')->getValue($dispatcher->getPageUrl())) {
					Membership::CheckRedirect(FALSE, '/members/walls/public');
				}
			} else Membership::CheckRedirect(TRUE, '/members/login');
			
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/%s", dirname(__DIR__, 3), $dir);
			$file_path = sprintf("%s/%s/%s", $dir_path, $dispatcher->getOption('type'), $dispatcher->getOption('action'));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
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
		 *
		 * @throws Exception
		 */
		public static function Modal(Router\Dispatcher $dispatcher, string $dir = 'members'): never {
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/modals/%s", dirname(__DIR__, 3), $dir);
			$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('modal'));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
			} else throw new Exception('Invalid modal provided.');
			
			exit;
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 * @param string            $dir
		 *
		 * @return never
		 */
		public static function Page(Router\Dispatcher $dispatcher, string $dir = 'members'): never {
			if(Options::Init('non_member_pages')->hasKey($dispatcher->getPageUrl())) {
				if(Options::Init('non_member_pages')->getValue($dispatcher->getPageUrl())) {
					Membership::CheckRedirect(FALSE, '/members/walls/public');
				}
			} else Membership::CheckRedirect(TRUE, '/members/login');
			
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/%s", dirname(__DIR__, 3), $dir);
			$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getPageUrl());
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			RenderDefault::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return never
		 *
		 * @throws Exception
		 */
		public static function Popover(Router\Dispatcher $dispatcher): never {
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/popovers/members", dirname(__DIR__, 3));
			$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('popover'));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
			} else throw new Exception('Invalid popover provided.');
			
			exit;
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return true
		 *
		 * @noinspection PhpUnusedParameterInspection
		 */
		public static function Post(Router\Dispatcher $dispatcher): bool {
			$member    = new Membership();
			$file_path = sprintf("%s/app/members/show_post", dirname(__DIR__, 3));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			require($file_path);
			return TRUE;
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return true
		 *
		 * @noinspection PhpUnusedParameterInspection
		 */
		public static function Profile(Router\Dispatcher $dispatcher): bool {
			$member    = new Membership();
			$file_path = sprintf("%s/app/members/show_profile", dirname(__DIR__, 3));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			require($file_path);
			return TRUE;
		}
		
		/**
		 * @param Dispatcher  $dispatcher
		 * @param null|string $page_url
		 *
		 * @return never
		 */
		public static function Rooms(Router\Dispatcher $dispatcher, ?string $page_url = NULL): never {
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/members/rooms", dirname(__DIR__, 3));
			$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getPageUrl($page_url));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			RenderDefault::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return never
		 */
		public static function Ticket(Router\Dispatcher $dispatcher): never {
			$member    = new Membership();
			$ticket    = Ticket::Init($dispatcher->getId());
			$dir_path  = sprintf("%s/app/members/tickets", dirname(__DIR__, 3));
			$file_path = realpath(sprintf("%s/show_ticket.php", $dir_path));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			RenderDefault::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return bool
		 */
		public static function Wall(Router\Dispatcher $dispatcher): bool {
			Membership::CheckRedirect(TRUE, '/members/login', TRUE);
			
			$member    = new Membership();
			$dir_path  = sprintf("%s/app/members/walls", dirname(__DIR__, 3));
			$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getPageUrl());
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				return TRUE;
			}
			
			return FALSE;
		}
	}