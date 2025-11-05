<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	use JetBrains\PhpStorm\Pure;
	
	class Pagination {
		public const APPEND_FORMAT  = 1;
		public const PREPEND_FORMAT = 0;
		
		/**
		 * Total number of items matching query in database.
		 *
		 * @var int
		 */
		private int $totalItems;
		
		/**
		 * The statement template can contain zero or more question mark (?) parameter markers for which real values will be substituted when the statement is executed.
		 *
		 * @var string
		 */
		private string $query;
		
		/**
		 * The parameters to be used by the query.
		 *
		 * @var array
		 */
		private array $params = array();
		
		/**
		 * Paginating math.
		 *
		 * @link https://api.nette.org/utils/3.2/Nette/Utils/Paginator.html
		 * @var Nette\Utils\Paginator
		 *
		 */
		private Nette\Utils\Paginator $paginator;
		
		/**
		 * Original page URL to build links from.
		 *
		 * @var string
		 */
		private string $originalPageUrl;
		
		/**
		 * @template TValue
		 *
		 * @param class-string<TValue> $class
		 *
		 * @return array<int, TValue>
		 *
		 * @throws Exception
		 */
		public function getItems(string $class): array {
			return $class::FetchAll(Database::Action(sprintf("%s LIMIT :limit_int OFFSET :offset_int", $this->getQuery()),
				array_merge(
					$this->getParams(),
					array(
						'limit_int'  => $this->paginator->getLength(),
						'offset_int' => $this->paginator->getOffset()
					)
				)));
		}
		
		/**
		 * @return string
		 *
		 * @throws Exception
		 */
		public function getDebugQuery(): string {
			return Database::Debug(sprintf("%s LIMIT :limit_int OFFSET :offset_int", $this->getQuery()),
				array_merge(
					$this->getParams(),
					array(
						'limit_int'  => $this->paginator->getLength(),
						'offset_int' => $this->paginator->getOffset()
					)
				));
		}
		
		/**
		 * Formats text to append or prepend the page count format.
		 *
		 * @param string $text
		 * @param int    $location 0 = PREPEND_FORMAT | 1 = APPEND_FORMAT
		 * @param string $format
		 *
		 * @return string
		 */
		public function formatPageString(string $text = '', int $location = self::APPEND_FORMAT, string $format = '[ Page %d of %d ]'): string {
			try {
				return $this->getPaginator()->getPageCount() <= 1 ? $text : match ($location) {
					self::APPEND_FORMAT  => sprintf("%s %s", $text, sprintf($format, $this->getPaginator()->getPage(), $this->getPaginator()->getPageCount())),
					self::PREPEND_FORMAT => sprintf("%s %s", sprintf($format, $this->getPaginator()->getPage(), $this->getPaginator()->getPageCount()), $text)
				};
			} catch(Exception $exception) {
				error_log($exception->getMessage());
				
				return trim($text);
			}
		}
		
		/**
		 * @return Nette\Utils\Paginator
		 */
		public function getPaginator(): Nette\Utils\Paginator {
			return $this->paginator;
		}
		
		/**
		 * @param int      $items_per_page
		 * @param int|null $page
		 */
		public function setPaginator(int $items_per_page, ?int $page = NULL): void {
			$page ??= filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array('options' => array('default' => 1, 'min_range' => 1)));
			
			$this->paginator = new Nette\Utils\Paginator;
			$this->paginator->setItemCount($this->getTotalItems());
			$this->paginator->setItemsPerPage($items_per_page);
			$this->paginator->setPage($page);
		}
		
		/**
		 * Gets the total number of items matching query in database.
		 *
		 * @return int
		 */
		public function getTotalItems(): int {
			return $this->totalItems ??= Database::Action(preg_replace('/SELECT .* FROM/i', 'SELECT COUNT(*) FROM', $this->getQuery()), $this->getParams())->fetchColumn();
		}
		
		/**
		 * @return string
		 */
		public function getQuery(): string {
			return $this->query;
		}
		
		/**
		 * @param string $query
		 * @param array  $params
		 */
		public function setQuery(string $query, array $params = array()): void {
			$this->query = $query;
			
			if(!empty($params)) $this->setParams($params);
		}
		
		/**
		 * @return array
		 */
		public function getParams(): array {
			return $this->params;
		}
		
		/**
		 * @param array $params
		 */
		public function setParams(array $params): void {
			$this->params = $params;
		}
		
		/**
		 * Formats the current link with HTML paging.
		 *
		 * @param int    $page
		 * @param string $format
		 *
		 * @return string
		 */
		#[Pure] public function formatPageLink(int $page, string $format = 'page-%d.%s'): string {
			$link_parts = parse_url($this->getOriginalPageUrl());
			$path_parts = pathinfo($link_parts['path']);
			
			if($page == 1) return $this->getOriginalPageUrl();
			
			return rtrim(sprintf("%s/%s/%s", rtrim($path_parts['dirname'], '/'), $path_parts['filename'], sprintf($format, $page, $path_parts['extension'] ?? NULL)), '.');
		}
		
		/**
		 * @return string
		 */
		private function getOriginalPageUrl(): string {
			return $this->originalPageUrl;
		}
		
		/**
		 * @param string $originalPageUrl
		 */
		public function setOriginalPageUrl(string $originalPageUrl): void {
			$this->originalPageUrl = $originalPageUrl;
		}
		
		/**
		 * Simple pagination algorithm.
		 *
		 * @param int    $delta
		 * @param string $text
		 *
		 * @return array
		 */
		public function getButtons(int $delta = 2, string $text = '...'): array {
			$left     = $this->paginator->getPage() - $delta;
			$right    = $this->paginator->getPage() + $delta + 1;
			$range    = array();
			$response = array();
			$offset   = -1;
			
			for($i = 1; $i <= $this->paginator->getLastPage(); $i++) {
				if($i == 1 || $i == $this->paginator->getLastPage() || $i >= $left && $i < $right) {
					$range[] = $i;
				}
			}
			
			for($i = 0; $i < count($range); $i++) {
				if($offset != -1) {
					if($range[$i] - $offset === 2) {
						$response[] = $offset + 1;
					} elseif($range[$i] - $offset !== 1) {
						$response[] = $text;
					}
				}
				
				$response[] = $range[$i];
				
				$offset = $range[$i];
			}
			
			return $response;
		}
	}