<?php
	/*
	Copyright (c) 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	/** @noinspection PhpUnused */
	
	// TODO: Show country flag with IP [https://github.com/chrislim2888/IP2Location-PHP-Module]
	
	use Items\Enums\Tables;
	use Items\Interfaces;
	use Jenssegers\Agent\Agent;
	use JetBrains\PhpStorm\Pure;
	
	class Helpers {
		/**
		 * Formats currency into human-readable string.
		 *
		 * @param float  $amount   The numeric currency value.
		 * @param string $currency Locale in which the number would be formatted (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return string String representing the formatted currency value.
		 */
		#[Pure] static function FormatCurrency(float $amount, string $currency = 'USD', string $locale = 'en_US'): string {
			return (new NumberFormatter($locale, NumberFormatter::CURRENCY))->formatCurrency($amount, $currency);
		}
		
		/**
		 * Parse a string into a float and a currency using the current formatter.
		 *
		 * @param string $amount   The string currency value.
		 * @param string $currency Locale in which the number would be parsed (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return float Float representing the parsed currency value.
		 */
		public static function UnformatCurrency(string $amount, string $currency = 'USD', string $locale = 'en_US'): float {
			return (new NumberFormatter($locale, NumberFormatter::CURRENCY))->parseCurrency($amount, $currency) ?: preg_replace('/[^\d.]/', '', $amount);
		}
		
		
		/**
		 * Creates directory, if non-existing
		 *
		 * @param string      $directory
		 * @param string|null $document_root
		 *
		 * @return string
		 */
		public static function CreateDirectory(string $directory, ?string $document_root = NULL): string {
			$document_root ??= filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ?? dirname(__DIR__, 3);
			$directory     = sprintf("%s/%s", $document_root, ltrim(self::WebRelative($directory, $document_root), '/'));
			
			!is_dir($directory) && mkdir($directory, 0755, TRUE);
			
			return $directory;
		}
		
		/**
		 * Returns the web relative path from the absolute root.
		 *
		 * @param string      $path
		 * @param string|null $document_root
		 *
		 * @return string
		 */
		public static function WebRelative(string $path, ?string $document_root = NULL): string {
			$document_root ??= dirname(__DIR__, 2);
			
			return sprintf("/%s", ltrim(str_replace($document_root, '', $path), '/'));
		}
		
		/**
		 * Returns the web relative path from the absolute root.
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function PathRelative(string $path): string {
			$document_root = dirname(__DIR__, 2);
			
			return sprintf("/%s", ltrim(str_replace($document_root, '', $path), '/'));
		}
		
		/**
		 * Returns the web relative path from the absolute root.
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function PathAbsolute(string $path): string {
			$document_root = dirname(__DIR__, 2);
			
			return sprintf("%s%s", $document_root, static::PathRelative($path));
		}
		
		/**
		 * Returns the web relative path from the absolute root.
		 *
		 * @param string      $filepath
		 * @param null|string $default
		 * @param bool        $no_cache
		 * @param string|null $document_root
		 *
		 * @return null|string
		 */
		public static function WebRelativeFile(string $filepath, ?string $default = NULL, bool $no_cache = FALSE, ?string $document_root = NULL): ?string {
			$document_root ??= dirname(__DIR__, 2);
			
			if(!is_file($filepath)) return $default;
			
			$relative_path = sprintf("/%s", ltrim(str_replace($document_root, '', $filepath), '/'));
			
			return !$no_cache ? $relative_path : sprintf("%s?%d", $relative_path, time());
		}
		
		/**
		 * Fetch current website URL.
		 *
		 * @param string $uri OPTIONAL: append string to end of current site URL
		 *
		 * @return string
		 */
		public static function CurrentWebsite(string $uri = ''): string {
			$http = !strcasecmp(filter_input(INPUT_SERVER, 'HTTPS'), 'on') ? 'https://' : 'http://';
			return $http . filter_input(INPUT_SERVER, 'HTTP_HOST') . $uri;
		}
		
		/**
		 * Returns a pretty title from string.
		 *
		 * @param string $string
		 * @param string $regex
		 * @param string $glue
		 *
		 * @return string
		 */
		public static function PrettyTitle(string $string, string $regex = '/_|-/', string $glue = ' '): string {
			return ucwords(implode($glue, preg_split($regex, $string)));
		}
		
		/**
		 * Formats a string into a URL slug.
		 *
		 * @param string|null $page_url
		 * @param bool        $route
		 * @param string|null $ignore
		 *
		 * @return string|null
		 */
		public static function FormatPageURL(?string $page_url = NULL, bool $route = FALSE, ?string $ignore = NULL): ?string {
			if(is_null($page_url)) return NULL;
			
			$page_url = preg_replace('/[^A-Za-z0-9]/', ' ', $page_url);
			$page_url = preg_replace('/\s+/', ' ', $page_url);
			$page_url = trim($page_url);
			$page_url = str_replace(' ', '-', $page_url);
			$page_url = strtolower($page_url);
			
			if($route && !empty($page_url)) {
				$rowCount = Database::Action("SELECT * FROM `routes` WHERE `page_url` = :page_url AND `page_url` != :ignore", array(
					'page_url' => $page_url,
					'ignore'   => $ignore
				))->rowCount();
				
				if(!empty($rowCount)) {
					$page_url .= sprintf("-%d", Database::Action("SELECT COALESCE(MAX(CAST(REPLACE(`page_url`, CONCAT(:page_url, '-'), '') AS INT)), 1) + 1 FROM `routes` WHERE `page_url` REGEXP :regex AND `page_url` != :ignore", array(
						'page_url' => $page_url,
						'regex'    => sprintf('^%s(-[0-9]+)$', $page_url),
						'ignore'   => $ignore
					))->fetchColumn());
				}
			}
			
			return $page_url;
		}
		
		/**
		 * Extracts YouTube ID from URL
		 *
		 * @param string|null $youtube_url
		 *
		 * @return string
		 */
		public static function ExtractYouTubeID(?string $youtube_url = NULL): string {
			if(is_null($youtube_url)) return '';
			
			preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^?&"\'>]+)/', $youtube_url, $matches);
			
			return $matches[1] ?? $youtube_url;
		}
		
		/**
		 * Returns a formatted string based on the user agent.
		 *
		 * @param string|null $user_agent
		 * @param string[]    $options
		 * @param bool|string $separator
		 *
		 * @return null|array|string
		 */
		public static function FormatUserAgent(?string $user_agent, ?array $options = NULL, bool|string $separator = ' | '): array|string|null {
			if(is_null($user_agent)) return NULL;
			
			$options ??= array('browser', 'device', 'platform', 'language');
			
			$agent = new Agent(NULL, $user_agent);
			
			$browser_data = array_filter(array_intersect_key(array(
				'browser'  => implode(' ', array_filter(array($agent->browser(), $agent->version($agent->browser())))),
				'device'   => $agent->device(),
				'platform' => implode(' ', array_filter(array($agent->platform(), $agent->version($agent->platform())))),
				'language' => !empty($agent->languages()[0]) ? Locale::getDisplayLanguage($agent->languages()[0]) : ''
			), array_flip($options)));
			
			return $separator !== FALSE ? implode($separator, $browser_data) : $browser_data;
		}
		
		/**
		 * Gets total number of items in database for this entry.
		 *
		 * @param string $table_name
		 * @param array  ...$conditions [column, value], [column, value], ...
		 *
		 * @return int
		 *
		 * @noinspection SqlResolve
		 */
		public static function TotalItems(string $table_name, array ...$conditions): int {
			$conditions = implode(' AND ', array_map(fn($condition) => sprintf("`%s` = '%s'", $condition[0], $condition[1]), $conditions));
			
			return Database::Action("SELECT * FROM `$table_name` WHERE $conditions")->rowCount();
		}
		
		/**
		 * Remove tags from content and shorten with non-alphanumeric characters trimmed on end.
		 *
		 * @param string|null $content
		 * @param int         $length
		 * @param string      $ellipsis
		 *
		 * @return string
		 */
		public static function Truncate(?string $content, int $length = 150, string $ellipsis = '...'): string {
			// Check Null
			if(is_null($content)) return '';
			
			// Strip Tags
			$content = strip_tags($content);
			
			// Check Length
			if(strlen($content) <= $length) return $content;
			
			$length      -= strlen($ellipsis);
			$parts       = preg_split('/([\s\n\r]+)/u', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
			$parts_count = count($parts);
			$str_len     = 0;
			$last_part   = 0;
			
			for(; $last_part < $parts_count; ++$last_part) {
				$str_len += strlen($parts[$last_part]);
				if($str_len > $length) {
					break;
				}
			}
			
			return trim(implode(array_slice($parts, 0, $last_part))) . $ellipsis;
		}
		
		/**
		 * Remove tags from content and shorten with non-alphanumeric characters trimmed on end.
		 *
		 * @param string|null $content
		 * @param int         $length
		 *
		 * @return string
		 */
		public static function Truncate2(?string $content, int $length = 150): string {
			// Check Null
			if(is_null($content)) return '';
			
			// Check Length
			if(strlen($content) <= $length) return $content;
			
			// Strip Headers
			$content = preg_replace('/<h(.*)<\/h[0-9]>/', '', $content);
			
			// Format Content
			$ellipsis = '...';
			$length   -= strlen($ellipsis);
			$content  = strip_tags(preg_replace('/\s+/', ' ', $content));
			$pos      = (strlen($content) > $length) ? strpos(wordwrap($content, $length, md5('[|]')), md5('[|]')) : strlen($content);
			if($pos !== FALSE && strlen($content) > $length) {
				$content = substr($content, 0, $pos);
				$content = preg_replace('/[^a-z0-9]$/i', '', trim($content)) . $ellipsis;
			} elseif(strlen($content) > $length) {
				$content = substr($content, 0, $length) . $ellipsis;
			}
			
			return $content;
		}
		
		/**
		 * Correctly calculates end of months when we shift to a shorter or longer month
		 * workaround for http://php.net/manual/en/datetime.add.php#example-2489
		 *
		 * Makes the assumption that shifting from the 28th Feb +1 month is 31st March
		 * Makes the assumption that shifting from the 28th Feb -1 month is 31st Jan
		 * Makes the assumption that shifting from the 29, 30, 31 Jan +1 month is 28th (or 29th) Feb
		 *
		 * @param DateTime $date_time
		 * @param int      $months
		 *
		 * @return DateTime
		 */
		
		public static function MonthShifter(DateTime $date_time, int $months): DateTime {
			$date_a   = clone($date_time);
			$date_b   = clone($date_time);
			$date_mod = clone($date_a->modify(sprintf("%d Month", $months)));
			
			if($date_b == $date_a->modify(sprintf("%d Month", $months * -1))) {
				if($date_time == $date_b->modify('Last Day of This Month')) {
					$result = $date_mod->modify('Last Day of This Month');
				} else {
					$result = $date_mod;
				}
			} else {
				$result = $date_mod->modify('Last Day of Last Month');
			}
			
			return $result;
		}
		
		/**
		 * @param string      $type
		 * @param string|null $key
		 *
		 * @return null|array|int|string
		 */
		public static function Options(string $type, ?string $key = NULL): mixed {
			$array = match ($type) {
				'non_member_pages' => array(
					'change-email'        => TRUE,
					'change-password'     => TRUE,
					'forgot-password'     => FALSE,
					'login'               => FALSE,
					'register'            => FALSE,
					'resend-verification' => FALSE,
					'reset-password'      => FALSE,
					'verify-email'        => FALSE
				),
				default            => array()
			};
			
			return is_null($key) ? $array : $array[$key] ?? NULL;
		}
		
		/**
		 * @param string $directory
		 * @param string $filename
		 * @param bool   $rmdir
		 *
		 * @return void
		 */
		public static function RemoveFile(string $directory, string $filename, bool $rmdir = TRUE): void {
			if(is_dir($directory)) {
				try {
					$files = new DirectoryIterator($directory);
					
					foreach($files as $file) {
						if(!$file->isDot()) {
							if($file->isDir()) {
								if($rmdir && static::EmptyDirectory($file->getPathname())) {
									static::RemoveDirectory($file->getPathname());
								} elseif(!static::EmptyDirectory($file->getPathname())) {
									static::RemoveFile($file->getPathname(), $filename, $rmdir);
								}
							} elseif(!strcmp($filename, $file->getBasename())) {
								unlink($file->getPathname());
							}
						}
					}
					
					if($rmdir && static::EmptyDirectory($directory)) static::RemoveDirectory($directory);
				} catch(Exception $exception) {
					error_log($exception->getMessage());
				}
			}
		}
		
		/**
		 * @param string $directory
		 *
		 * @return bool
		 */
		public static function EmptyDirectory(string $directory): bool {
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
		 *
		 * @return void
		 */
		public static function RemoveDirectory(string $directory): void {
			if(is_dir($directory)) {
				try {
					$files = new DirectoryIterator($directory);
					
					foreach($files as $file) {
						if(!$file->isDot()) {
							if($file->isDir()) {
								static::RemoveDirectory($file->getPathname());
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
		 * @param mixed ...$url_parts
		 *
		 * @return string
		 */
		public static function LinkBuilder(...$url_parts): string {
			return sprintf("/%s", implode('/', array_filter($url_parts)));
		}
		
		/**
		 * @param string $url
		 * @param bool   $rel_link
		 *
		 * @return never
		 */
		public static function Redirect(string $url, bool $rel_link = FALSE): never {
			if($rel_link) {
				header(sprintf("Location: /%s?rel=%s", ltrim($url, '/'), Helpers::CurrentPage()));
			} else {
				header(sprintf("Location: /%s", ltrim($url, '/')));
			}
			exit;
		}
		
		/**
		 * Fetch current page URL.
		 *
		 * @return string
		 */
		public static function CurrentPage(): string {
			$http = !strcasecmp(filter_input(INPUT_SERVER, 'HTTPS'), 'on') ? 'https://' : 'http://';
			return $http . filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI');
		}
		
		/**
		 * @param null|string $table_name
		 *
		 * @return null|Interfaces\TableEnum
		 */
		public static function TableLookup(?string $table_name): ?Interfaces\TableEnum {
			$table_name = match ($table_name) {
				'swinkster-pages' => 'swinkster_pages',
				default           => $table_name
			};
			
			return Tables\Website::lookup($table_name) ??
			       Tables\Members::lookup($table_name) ??
			       Tables\Secrets::lookup($table_name) ??
			       Tables\Swinkster::lookup($table_name);
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
		public static function GetImageDimension(string $image_path, string $property, ?string $document_root = NULL): float|int|null {
			$document_root ??= filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
			
			try {
				$image = new Imagick(sprintf("%s/%s", $document_root, ltrim($image_path, '/')));
				
				return match ($property) {
					'aspect' => $image->getImageWidth() / $image->getImageHeight(),
					'height' => $image->getImageHeight(),
					'width'  => $image->getImageWidth(),
					default  => NULL
				};
			} catch(ImagickException $error) {
				error_log($error->getMessage());
				
				return NULL;
			}
		}
	}