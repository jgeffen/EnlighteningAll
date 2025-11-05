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
	
	use DirectoryIterator;
	use Exception;
	
	class Directory {
		/**
		 * @param string $directory
		 *
		 * @return bool
		 */
		public static function Empty(string $directory): bool {
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
		 * @param string $directory
		 */
		public static function Remove(string $directory): void {
			if(is_dir($directory)) {
				try {
					$files = new DirectoryIterator($directory);
					
					foreach($files as $file) {
						if(!$file->isDot()) {
							if($file->isDir()) {
								Directory::Remove($file->getPathname());
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
	}