<?php
	/*
	Copyright (c) 2020, 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single web site may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/
	
	use JetBrains\PhpStorm\NoReturn;
	
	class System extends Database {
		/**
		 * System constructor.
		 *
		 * @param string|null $config_path
		 * @param array       $options
		 *
		 * @throws Exception
		 */
		public function __construct(?string $config_path = NULL, array $options = array()) {
			$config_path ??= sprintf("%s/settings/database.json", dirname(__DIR__));
			
			parent::__construct($config_path, $options);
		}
		
		/**
		 * Checks basic authentication.
		 *
		 * @param string|null $user
		 * @param string|null $pass
		 *
		 * @return bool
		 */
		static function BasicAuth(?string $user, ?string $pass): bool {
			if(empty($_SERVER['PHP_AUTH_USER'])) return FALSE;
			
			if($_SERVER['PHP_AUTH_USER'] != $user) {
				error_log(sprintf("user %s not found: %s", $_SERVER['PHP_AUTH_USER'], filter_input(INPUT_SERVER, 'REQUEST_URI')));
				return FALSE;
			}
			
			if(empty($_SERVER['PHP_AUTH_PW'])) return FALSE;
			
			if($_SERVER['PHP_AUTH_PW'] != $pass) {
				error_log(sprintf("user %s: authentication failure for \"%s/\": Password Mismatch", $_SERVER['PHP_AUTH_USER'], filter_input(INPUT_SERVER, 'REQUEST_URI')));
				return FALSE;
			}
			
			return TRUE;
		}
		
		/**
		 * Redirects user.
		 *
		 * @param string $url
		 */
		#[NoReturn] static function Redirect(string $url): void {
			header(sprintf("Location: %s", $url));
			exit;
		}
		
		/**
		 * Removes file from directory recursively.
		 *
		 * @param string|null $filename
		 * @param string      $table_name
		 * @param int         $table_id
		 * @param string|null $column
		 * @param string|null $directory
		 * @param bool        $rmdir
		 * @param string|null $document_root
		 *
		 * @throws Exception
		 * @noinspection SqlResolve
		 */
		static function RemoveFile(?string $filename, string $table_name, int $table_id, ?string $column = 'filename', ?string $directory = NULL, bool $rmdir = TRUE, ?string $document_root = NULL) {
			try {
				$document_root ??= filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ?? dirname(__DIR__, 3);
				$directory     ??= sprintf("%s/files/%s", $document_root, $table_name);
				
				if(!is_null($filename)) {
					foreach(new DirectoryIterator($directory) as $file) {
						if(!$file->isDot()) {
							if($file->isDir()) {
								if($rmdir && self::EmptyDirectory($file->getPathname())) {
									self::RemoveDirectory($file->getPathname());
								} elseif(!self::EmptyDirectory($file->getPathname())) {
									self::RemoveFile($filename, $table_name, $table_id, NULL, $file->getPathname(), $rmdir, $document_root);
								}
							} elseif(!strcmp($filename, $file->getBasename())) {
								unlink($file->getPathname());
							}
						}
					}
				}
				
				if($rmdir && self::EmptyDirectory($directory)) {
					self::RemoveDirectory($directory);
				}
				
				if(!is_null($column)) {
					self::Action(sprintf("UPDATE `%s` SET `%s` = '' WHERE `id` = %d", $table_name, $column, $table_id));
					
					self::LogAction('Delete', $table_name, $table_id, $filename);
				}
			} catch(Exception $exception) {
				error_log($exception->getMessage());
				throw new Exception($exception->getMessage());
			}
		}
		
		/**
		 * Checks if directory is empty.
		 *
		 * @param string $directory
		 *
		 * @return bool
		 */
		static function EmptyDirectory(string $directory): bool {
			if(is_dir($directory)) {
				try {
					$files = new DirectoryIterator($directory);
					
					foreach($files as $file) {
						if(!$file->isDot()) return FALSE;
					}
				} catch(Exception $exception) {
					error_log($exception->getMessage());
				}
			}
			
			return TRUE;
		}
		
		/**
		 * Removes directory recursively.
		 *
		 * @param string $directory
		 */
		static function RemoveDirectory(string $directory) {
			if(is_dir($directory)) {
				try {
					$files = new DirectoryIterator($directory);
					
					foreach($files as $file) {
						if(!$file->isDot()) {
							if($file->isDir()) {
								self::RemoveDirectory($file->getPathname());
							} else {
								unlink($file->getPathname());
							}
						}
					}
				} catch(Exception $exception) {
					error_log($exception->getMessage());
				}
				
				rmdir($directory);
			}
		}
		
		/**
		 * Adds log to the database.
		 *
		 * @param string      $action
		 * @param string|null $table_name
		 * @param int|null    $table_id
		 * @param string|null $filename
		 */
		public static function LogAction(string $action, ?string $table_name = NULL, ?int $table_id = NULL, ?string $filename = NULL): void {
			try {
				self::Action("INSERT INTO `users_logs` SET `action` = :action, `table_name` = :table_name, `table_id` = :table_id, `filename` = :filename, `user_id` = :user_id, `data` = :data, `user_agent` = :user_agent, `ip_address` = :ip_address, `timestamp` = :timestamp", array(
					'action'     => $action,
					'table_name' => $table_name,
					'table_id'   => $table_id,
					'filename'   => $filename,
					'user_id'    => 0,
					'data'       => json_encode(array_diff_key($_POST, array_flip(array('password')))),
					'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
					'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
					'timestamp'  => date('Y-m-d H:i:s')
				));
			} catch(Exception $exception) {
				error_log($exception->getMessage());
			}
		}
	}