<?php
/*
	Copyright (c) 2020, 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/

use MatthiasMullie\Minify;
use Mimey\MimeTypes;
use ScssPhp\ScssPhp;

class Render {
	/**
	 * Serves asset.
	 *
	 * @param string $asset
	 */
	public static function Asset(string $asset): never {
		try {
			$file_path = sprintf("%s/%s", dirname(__DIR__, 2), ltrim($asset, '/'));

			if (!is_file($file_path)) throw new Exception(sprintf("MISSING ASSET: %s", $asset));
			if (headers_sent()) throw new Exception(sprintf("HEADERS ALREADY SENT: %s", $asset));

			$contents = file_get_contents($file_path);

			header(sprintf("Etag: %s", md5($contents)));
			header(sprintf("Last-Modified: %s", sprintf("%sGMT", gmdate('D, d M Y H:i:s ', filemtime($file_path)))));
			header(sprintf("Content-Type: %s", (new MimeTypes())->getMimeType(pathinfo($file_path, PATHINFO_EXTENSION))));
			header(sprintf("Content-Disposition: inline; filename=\"%s\"", basename($file_path)));
			header(sprintf("Content-Length: %s", filesize($file_path)));

			echo $contents;
		} catch (Exception $exception) {
			Debug::Exception($exception);
		}

		exit;
	}

	/**
	 * @param Router\Dispatcher $dispatcher
	 */
	public static function Ajax(Router\Dispatcher $dispatcher): never {
		$member    = Membership::Init();
		$dir_path  = sprintf("%s/app/ajax", dirname(__DIR__, 2));
		$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('script'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
			exit;
		}

		Render::ErrorDocument(404);
	}

	/**
	 * @param null|Router\Dispatcher $dispatcher
	 *
	 * @return never
	 */
	public static function Event(?Router\Dispatcher $dispatcher = NULL): never {
		$member    = Membership::Init();
		$file_path = sprintf("%s/app/items/events/item", dirname(__DIR__, 2));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		$dispatcher->overrideRoute(Router\Route::Init('events', $dispatcher->getTableId()));

		require($file_path);

		exit;
	}

	/**
	 * @param string                 $dir
	 * @param null|Router\Dispatcher $dispatcher
	 *
	 * @return bool
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public static function Form(string $dir, ?Router\Dispatcher $dispatcher = NULL): bool {
		$member    = Membership::Init();
		$dir_path  = sprintf("%s/%s", dirname(__DIR__, 2), $dir);
		$file_path = $dir_path;
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param string                 $route
	 * @param string                 $dir
	 * @param null|Router\Dispatcher $dispatcher
	 *
	 * @return bool
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public static function Page(string $route, string $dir = 'app', ?Router\Dispatcher $dispatcher = NULL): bool {
		$member    = Membership::Init();
		$dir_path  = sprintf("%s/%s", dirname(__DIR__, 2), $dir);
		$file_path = sprintf("%s/%s", $dir_path, rtrim($route, '/'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param Router\Dispatcher $dispatcher
	 *
	 * @return bool
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public static function FormPage(Router\Dispatcher $dispatcher): bool {
		$member    = Membership::Init();
		$dir_path  = sprintf("%s/app/forms", dirname(__DIR__, 2));
		$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('form'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param Router\Dispatcher $dispatcher
	 *
	 * @return bool
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public static function FormAjax(Router\Dispatcher $dispatcher): bool {
		$member    = Membership::Init();
		$dir_path  = sprintf("%s/app/forms", dirname(__DIR__, 2));
		$file_path = sprintf("%s/_ajax/%s", $dir_path, $dispatcher->getOption('form'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param Router\Dispatcher $dispatcher
	 *
	 * @return never
	 *
	 * @throws Exception
	 */
	public static function Modal(Router\Dispatcher $dispatcher): never {
		$member    = Membership::Init();
		$dir_path  = sprintf("%s/app/modals", dirname(__DIR__, 2));
		$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('modal'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
		} else throw new Exception('Invalid modal provided.');

		exit;
	}

	/**
	 * @param Router\Dispatcher $dispatcher
	 *
	 * @return never
	 *
	 * @throws Exception
	 */
	public static function Popover(Router\Dispatcher $dispatcher): never {
		$dir_path  = sprintf("%s/app/popovers", dirname(__DIR__, 2));
		$file_path = sprintf("%s/%s", $dir_path, $dispatcher->getOption('popover'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		if ($file_path && str_contains($file_path, $dir_path)) {
			require($file_path);
		} else throw new Exception('Invalid popover provided.');

		exit;
	}

	/**
	 * @param null|Router\Dispatcher $dispatcher
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public static function Route(?Router\Dispatcher $dispatcher = NULL): bool {
		if (!is_null($dispatcher->getRoute())) {
			$member    = Membership::Init();
			$dir_path  = sprintf("%s/app/items/%s", dirname(__DIR__, 2), match ($dispatcher->getRoute()->getTableName()) {
				'cateories'   => throw new Exception('Categories has yet to be implemented.'),
				'club_states' => 'clubs',
				default       => $dispatcher->getRoute()->getTableName()
			});
			$file_path = realpath(sprintf("%s/%s.php", $dir_path, match (TRUE) {
				$dispatcher->getRoute()->hasCategories() => 'categories',
				$dispatcher->getRoute()->isCategory()    => 'category',
				$dispatcher->getRoute()->hasItems()      => 'items',
				$dispatcher->getRoute()->isItem()        => 'item'
			}));

			if ($file_path && str_contains($file_path, $dir_path)) {
				require($file_path);
				return TRUE;
			}
		}

		return FALSE;
	}
	
	/**
	 * @param \Router\Dispatcher $dispatcher
	 *
	 * @return bool
	 */
	public static function RouteHandler(Router\Dispatcher $dispatcher): bool {
		$route_path = sprintf("%s/router", dirname(__DIR__, 2));
		$file_path  = realpath(sprintf("%s/%s/handler.php", $route_path, $dispatcher->getSection()));
		
		if($file_path && str_contains($file_path, $route_path)) {
			require($file_path);
			return TRUE;
		}
		
		return static::Page($dispatcher->getSection());
	}

	/**
	 * Draws component directly on to the page.
	 *
	 * @param string $route
	 * @param array  $payload
	 */
	public static function Component(string $route, array $payload = array()): void {
		echo static::GetComponent($route, $payload);
	}

	/**
	 * Returns component as a string.
	 *
	 * @param string $route
	 * @param array  $payload
	 *
	 * @return string
	 */
	public static function GetComponent(string $route, array $payload = array()): string {
		$options   = $payload;
		$dir_path  = sprintf("%s/components", dirname(__DIR__, 2));
		$file_path = sprintf("%s/%s", $dir_path, rtrim($route, '/'));
		$file_path = realpath($file_path . (is_dir($file_path) ? '/index.php' : '.php'));

		extract($payload, EXTR_PREFIX_ALL, 'qd');

		ob_start();

		if ($file_path && str_contains($file_path, $dir_path)) {
			include($file_path);
		}

		return ob_get_clean();
	}

	/**
	 * @param string $template
	 * @param array  $context
	 *
	 * @return void
	 */
	public static function Template(string $template, array $context = array()): void {
		echo static::GetTemplate($template, $context);
	}

	/**
	 * @param string $template
	 * @param array  $context
	 *
	 * @return string
	 */
	public static function GetTemplate(string $template, array $context = array()): string {
		return Template::Render($template, $context);
	}

	/**
	 * @param string      $route
	 * @param array       $data_source
	 * @param string|null $document_root
	 *
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public static function Structure(string $route, array $data_source = array(), ?string $document_root = NULL): void {
		global $options;
		global $settings;

		$document_root ??= dirname(__DIR__, 2);
		$file_path     = sprintf("%s/library/structures/%s.php", $document_root, $route);

		if (!is_file($file_path)) return;

		include($file_path);
	}

	/**
	 * @param string      $table_name
	 * @param int         $table_id
	 * @param string|null $alt
	 * @param string      $type ["landscape"] | "portrait" | "square"
	 *
	 * @return array
	 */
	public static function Gallery(string $table_name, int $table_id, ?string $alt = NULL, string $type = 'landscape'): array {
		return array_filter(array_map(function ($image) use ($type, $alt) {
			return array_merge($image, array(
				'alt'      => htmlentities($image['filename_alt'] ?: $alt, ENT_QUOTES),
				'source'   => Render::Images(sprintf("/files/images/%s", $image['filename'])),
				'featured' => Render::Images(sprintf("/files/images/%s/%s", $type, $image['filename'])),
				'thumb'    => Render::Images(sprintf("/files/images/%s/thumbs/%s", $type, $image['filename']))
			));
		}, Database::Action("SELECT * FROM `images` WHERE `table_name` = :table_name AND `table_id` = :table_id ORDER BY `position` DESC", array(
			'table_name' => $table_name,
			'table_id'   => $table_id
		))->fetchAll(PDO::FETCH_ASSOC)), function ($image) {
			return !empty($image['source']);
		});
	}
	
	/**
	 * @param string      $table_name
	 * @param int         $table_id
	 * @param string|null $alt
	 * @param string      $type ["landscape"] | "portrait" | "square"
	 *
	 * @return array
	 */
	public static function StaffGallery(string $table_name, int $table_id, ?string $alt = NULL, string $type = 'square'): array {
		return array_filter(array_map(function ($image) use ($type, $alt) {
			return array_merge($image, array(
				'alt'      => htmlentities($image['filename_alt'] ?: $alt, ENT_QUOTES),
				'source'   => Render::Images(sprintf("/files/images/%s", $image['filename'])),
				'featured' => Render::Images(sprintf("/files/images/%s/%s", $type, $image['filename'])),
				'thumb'    => Render::Images(sprintf("/files/images/%s/thumbs/%s", $type, $image['filename']))
			));
		}, Database::Action("SELECT * FROM `images` WHERE `table_name` = :table_name AND `table_id` = :table_id ORDER BY `position` DESC", array(
			'table_name' => $table_name,
			'table_id'   => $table_id
		))->fetchAll(PDO::FETCH_ASSOC)), function ($image) {
			return !empty($image['source']);
		});
	}

	/**
	 * @param string|string[] $paths   Single image path or array of image paths
	 * @param string|null     $default Default image to be returned, if image does not exist
	 * @param string|null     $document_root
	 *
	 * @return array|string|null
	 */
	public static function Images(array|string $paths, ?string $default = NULL, ?string $document_root = NULL): array|string|null {
		$document_root ??= dirname(__DIR__, 2);

		if (!is_array($paths)) {
			$file_path = sprintf("%s/%s", $document_root, ltrim($paths, '/'));

			return is_file($file_path) ? $paths : $default;
		}

		return array_filter(array_map(function ($path) use ($default, $document_root) {
			$file_path = sprintf("%s/%s", $document_root, ltrim($path, '/'));

			return is_file($file_path) ? $path : $default;
		}, $paths));
	}

	/**
	 * @param string      $table_name
	 * @param int         $table_id
	 * @param string      $type ALLOWED: pdfs
	 * @param string|null $document_root
	 *
	 * @return array
	 */
	public static function Files(string $table_name, int $table_id, string $type, ?string $document_root = NULL): array {
		$document_root ??= dirname(__DIR__, 2);

		return match ($type) {
			'pdfs'  => array_filter(array_map(function ($file) {
				return array_merge($file, array(
					'alt'  => htmlentities($file['title'], ENT_QUOTES),
					'link' => sprintf("/files/pdfs/%s", $file['filename']),
					'text' => !is_null($file['description']) ? nl2br($file['description']) : NULL
				));
			}, Database::Action("SELECT * FROM `pdfs` WHERE `table_name` = :table_name AND `table_id` = :table_id ORDER BY `position` DESC", array(
				'table_name' => $table_name,
				'table_id'   => $table_id
			))->fetchAll(PDO::FETCH_ASSOC)), function ($file) use ($document_root) {
				return is_file(sprintf("%s/%s", $document_root, ltrim($file['link'], '/')));
			}),
			default => array(),
		};
	}

	/**
	 * Minify and combine js/css/scss.
	 *
	 * @param string      $output_file File to be saved and cached.
	 * @param array       $assets      Array of assets to be combined and minified.
	 * @param bool|int    $filemtime   Time of calling file.
	 * @param array       $imports     Array of imports to check timestamp.
	 * @param bool        $debug       Toggles debug mode, check error_log.
	 * @param string|null $document_root
	 *
	 * @return void
	 *
	 * @throws ScssPhp\Exception\SassException
	 */
	public static function Minify(string $output_file, array $assets, bool|int $filemtime = FALSE, array $imports = array(), bool $debug = FALSE, ?string $document_root = NULL): void {
		$debug = $debug ? (new Debug())->start() : NULL;

		$document_root ??= dirname(__DIR__, 2);

		$output_file = sprintf("%s/%s", $document_root, ltrim($output_file));
		$mTime       = is_file($output_file) ? filemtime($output_file) : 0;
		$rebuild     = $filemtime > $mTime;
		$assets      = array_filter(array_map(function ($asset) use ($document_root): string {
			$file_path = sprintf("%s/%s", $document_root, ltrim($asset, '/'));
			if (is_file($file_path)) return $file_path;
			if (!empty($asset)) error_log(sprintf("MISSING ASSET: %s IN %s", $asset, debug_backtrace()[2]['file'] ?? 'Unknown'));
			return '';
		}, $assets));

		if (!$rebuild) foreach ($assets as $asset) {
			if (filemtime($asset) > $mTime) {
				$rebuild = TRUE;
				break;
			}
		}

		$imports = array_filter(array_map(function ($import) use ($document_root): string {
			$file_path = sprintf("%s/%s", $document_root, ltrim($import, '/'));
			if (is_file($file_path)) return $file_path;
			if (!empty($import)) error_log(sprintf("MISSING ASSET: %s IN %s", $import, debug_backtrace()[2]['file'] ?? 'Unknown'));
			return '';
		}, $imports));

		if (!$rebuild) foreach ($imports as $import) {
			if (filemtime($import) > $mTime) {
				$rebuild = TRUE;
				break;
			}
		}

		if ($rebuild || $debug) {
			if (!in_array(pathinfo($output_file, PATHINFO_EXTENSION), array('css'))) {
				$minifier = new Minify\JS();
				$minifier->add($assets);
			} else {
				$minifier = new Minify\CSS();
				$minifier->add(array_map(function ($asset) use ($output_file, $document_root, $minifier) {
					if (ScssPhp\Compiler::isCssImport($asset)) return $asset;

					try {
						$compiler = new ScssPhp\Compiler();
						$compiler->addImportPath(function ($file_path) use ($document_root, $asset): string|null {
							$abs_path = sprintf("%s/%s", $document_root, ltrim($file_path, '/'));

							if (!file_exists($abs_path)) {
								return sprintf("%s/%s", dirname($asset), ltrim($file_path, '/'));
							}

							return $abs_path;
						});

						return $compiler->compileString(sprintf("\$source: '%s';\n%s", basename($output_file, '.min.css'), file_get_contents($asset)), $document_root)->getCss();
					} catch (Exception $exception) {
						error_log(sprintf("%s (%s) %s: %s", __FILE__, __LINE__, $exception->getMessage(), $asset));

						return (new ScssPhp\Compiler())
							->compileString(sprintf("#render-error { content: \"%s\" !important; }", htmlentities($exception->getMessage(), ENT_QUOTES)))
							->getCss();
					}
				}, $assets));
			}

			$minifier->minify($output_file);

			$debug?->tick();
		}

		$debug?->stop();
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
		$status_code ??= 306;
		require(sprintf("%s/show_error.php", dirname(__DIR__, 2)));
		exit;
	}

	/**
	 * Displays response status code HTML.
	 *
	 * @param int $status_code
	 */
	public static function ErrorCode(int $status_code): never {
		http_response_code($status_code);
		exit;
	}
}
