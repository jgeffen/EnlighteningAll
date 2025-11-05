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
	
	use Admin;
	use Database;
	use DirectoryIterator;
	use Exception;
	use Items\Enums\Types;
	use Items\Interfaces;
	
	class File {
		/**
		 * @param string|null          $filename
		 * @param Interfaces\TableEnum $table_name
		 * @param int                  $table_id
		 * @param string|null          $column
		 * @param string|null          $directory
		 * @param bool                 $rmdir
		 *
		 * @throws Exception
		 * @noinspection SqlResolve
		 */
		public static function Remove(
			?string              $filename,
			Interfaces\TableEnum $table_name,
			int                  $table_id,
			?string              $column = 'filename',
			?string              $directory = NULL,
			bool                 $rmdir = TRUE
		): void {
			$admin = new Admin\User();
			
			try {
				$directory ??= sprintf("%s/files/%s", dirname(__DIR__, 3), $table_name->getValue());
				
				if(!is_null($filename) && is_dir($directory)) {
					foreach(new DirectoryIterator($directory) as $file) {
						if(!$file->isDot()) {
							if($file->isDir()) {
								if($rmdir && Directory::Empty($file->getPathname())) {
									Directory::Remove($file->getPathname());
								} elseif(!Directory::Empty($file->getPathname())) {
									File::Remove($filename, $table_name, $table_id, NULL, $file->getPathname(), $rmdir);
								}
							} elseif(!strcmp($filename, $file->getBasename())) {
								unlink($file->getPathname());
							}
						}
					}
				}
				
				if($rmdir && Directory::Empty($directory)) {
					Directory::Remove($directory);
				}
				
				if(!is_null($column)) {
					Database::Action(sprintf("UPDATE `%s` SET `%s` = '' WHERE `id` = %d", $table_name->getValue(), $column, $table_id));
					
					$admin->log(
						type         : Types\Log::UPDATE,
						table_name   : $table_name,
						table_id     : $table_id,
						table_column : 'filename',
						filename     : $filename,
						payload      : $_POST
					);
				}
			} catch(Exception $exception) {
				error_log($exception->getMessage());
				throw new Exception($exception->getMessage());
			}
		}
	}