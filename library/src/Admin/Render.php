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
	
	use Debug;
	use Exception;
	use Mimey\MimeTypes;
	use Router;
	
	class Render {
		/**
		 * @param Router\Dispatcher $dispatcher
		 */
		public static function Ajax(Router\Dispatcher $dispatcher): never {
			$admin     = new User();
			$dir_path  = sprintf("%s/app/ajax/admin", dirname(__DIR__, 3));
			$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('script'));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			static::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 */
		public static function Asset(Router\Dispatcher $dispatcher): never {
			try {
				$file_path = sprintf("%s/app/admin/assets/%s", dirname(__DIR__, 3), ltrim($dispatcher->getOption('asset'), '/'));
				
				if(!is_file($file_path)) throw new Exception(sprintf("MISSING ASSET: %s", $dispatcher->getOption('asset')));
				if(headers_sent()) throw new Exception(sprintf("HEADERS ALREADY SENT: %s", $dispatcher->getOption('asset')));
				
				$contents = file_get_contents($file_path);
				
				header(sprintf("Etag: %s", md5($contents)));
				header(sprintf("Last-Modified: %s", sprintf("%sGMT", gmdate('D, d M Y H:i:s ', filemtime($file_path)))));
				header(sprintf("Content-Type: %s", (new MimeTypes())->getMimeType(pathinfo($file_path, PATHINFO_EXTENSION))));
				header(sprintf("Content-Disposition: inline; filename=\"%s\"", basename($file_path)));
				header(sprintf("Content-Length: %s", filesize($file_path)));
				
				echo $contents;
			} catch(Exception $exception) {
				Debug::Exception($exception);
			}
			
			exit;
		}
		
		/**
		 * @return never
		 */
		public static function Dashboard(): never {
			$admin = new User();
			require(sprintf("%s/app/admin/dashboard.php", dirname(__DIR__, 3)));
			exit;
		}
		
		/**
		 * @return never
		 */
		public static function Login(): never {
			require(sprintf("%s/app/ajax/admin/login.php", dirname(__DIR__, 3)));
			exit;
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return bool
		 *
		 * @throws Exception
		 */
		public static function Management(Router\Dispatcher $dispatcher): bool {
			$admin    = new User();
			$dir_path = sprintf("%s/app/admin/items", dirname(__DIR__, 3));
			
			// Check Standard File path
			$file_path = match (Router\Request::GetMethod()) {
				Router\Method::DELETE => sprintf("%s/%s/_ajax/delete", $dir_path, $dispatcher->getOption('type')),
				Router\Method::GET    => sprintf("%s/%s/%s", $dir_path, $dispatcher->getOption('type'), $dispatcher->getOption('action')),
				Router\Method::POST   => sprintf("%s/%s/_ajax/%s", $dir_path, $dispatcher->getOption('type'), $dispatcher->getOption('action')),
				default               => throw new Exception('Method not implemented.')
			};
			
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			// Check Non-Standard File Path
			$file_path = match (Router\Request::GetMethod()) {
				Router\Method::DELETE => sprintf("%s/%s/_ajax/delete", $dir_path, str_replace('-', '_', $dispatcher->getOption('type'))),
				Router\Method::GET    => sprintf("%s/%s/%s", $dir_path, str_replace('-', '_', $dispatcher->getOption('type')), $dispatcher->getOption('action')),
				Router\Method::POST   => sprintf("%s/%s/_ajax/%s", $dir_path, str_replace('-', '_', $dispatcher->getOption('type')), $dispatcher->getOption('action')),
				default               => throw new Exception('Method not implemented.')
			};
			
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			static::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 * @param string            $dir
		 *
		 * @return never
		 *
		 * @throws Exception
		 */
		public static function Modal(Router\Dispatcher $dispatcher, string $dir = 'admin'): never {
			$admin     = new User();
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
		 * @return bool
		 */
		public static function Page(Router\Dispatcher $dispatcher, string $dir = 'admin'): bool {
			$admin     = new User();
			$dir_path  = sprintf("%s/app/%s", dirname(__DIR__, 3), $dir);
			$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getPageUrl());
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			static::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return bool
		 *
		 * @throws Exception
		 */
		public static function Report(Router\Dispatcher $dispatcher): bool {
			$admin     = new User();
			$dir_path  = sprintf("%s/app/admin/items/member-reports", dirname(__DIR__, 3));
			$file_path = match (Router\Request::GetMethod()) {
				Router\Method::GET  => sprintf("%s/%s/%s", $dir_path, $dispatcher->getOption('action'), $dispatcher->getOption('table_name')),
				Router\Method::POST => sprintf("%s/_ajax/%s/%s", $dir_path, $dispatcher->getOption('action'), $dispatcher->getOption('table_name')),
				default             => throw new Exception('Method not implemented.')
			};
			
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			static::ErrorDocument(404);
		}
		
		/**
		 * @param Router\Dispatcher $dispatcher
		 *
		 * @return bool
		 *
		 * @throws Exception
		 */
		public static function Toggle(Router\Dispatcher $dispatcher): bool {
			$admin     = new User();
			$dir_path  = sprintf("%s/app/admin/items", dirname(__DIR__, 3));
			$file_path = sprintf("%s/%s/_ajax/toggle/%s", $dir_path, $dispatcher->getOption('type'), $dispatcher->getOption('action'));
			$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));
			
			if($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				exit;
			}
			
			static::ErrorDocument(404);
		}
		
		/**
		 * Displays response status code HTML.
		 *
		 * @param null|int       $status_code
		 * @param null|Exception $exception
		 *
		 * @return never
		 *
		 * @noinspection PhpUnusedParameterInspection
		 */
		public static function ErrorDocument(?int $status_code, ?Exception $exception = NULL): never {
			$admin       ??= new User();
			$status_code ??= 306;
			require(sprintf("%s/show_error.php", dirname(__DIR__, 3)));
			exit;
		}
	}