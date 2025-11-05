<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	namespace Items\Traits;
	
	use DateTime;
	use Helpers;
	use Items;
	use Router;
	
	trait Page {
		protected ?Router\Route $route;
		
		protected ?string $content;
		protected string  $page_url;
		protected ?string $page_description;
		protected string  $page_title;
		protected int     $position;
		protected bool    $published;
		protected string  $published_date;
		protected ?string $heading;
		protected ?string $youtube_id;
		
		/**
		 * @param null|int $length
		 *
		 * @return string
		 */
		public function getTitle(?int $length = NULL): string {
			return is_null($length) ? $this->page_title : Helpers::Truncate($this->page_title, $length);
		}
		
		/**
		 * @return null|string
		 */
		public function getDescription(): ?string {
			return $this->page_description;
		}
		
		/**
		 * @return null|string
		 */
		public function getHeading(): ?string {
			return $this->heading;
		}
		
		/**
		 * @return string
		 */
		public function getAlt(): string {
			return htmlspecialchars($this->getTitle(), ENT_COMPAT);
		}
		
		/**
		 * @param null|int $length
		 *
		 * @return null|string
		 */
		public function getContent(?int $length = NULL): ?string {
			return is_null($length) ? $this->content : Helpers::Truncate($this->content, $length);
		}
		
		/**
		 * @param int $length
		 *
		 * @return string
		 */
		public function getContentPreview(int $length = 450): string {
			return Helpers::Truncate($this->getContent(), $length);
		}
		
		/**
		 * @return bool
		 */
		public function isPublished(): bool {
			return $this->published;
		}
		
		/**
		 * @return DateTime
		 */
		public function getPublishedDate(): DateTime {
			return date_create($this->published_date);
		}
		
		/**
		 * @return null|string
		 */
		public function getYoutubeId(): ?string {
			return $this->youtube_id;
		}
		
		/**
		 * @return string
		 */
		public function getYoutubeEmbed(): string {
			return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $this->youtube_id . '" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		}
		
		/**
		 * @return string
		 */
		public function getPageUrl(): string {
			return $this->page_url;
		}
		
		/**
		 * @return null|int
		 */
		public function getPosition(): ?int {
			return $this->position ?? NULL;
		}
		
		/**
		 * @return null|Router\Route
		 */
		public function getRoute(): ?Router\Route {
			return $this->route ??= Router\Route::Init($this->table->getValue(), $this->getId());
		}
		
		/**
		 * @return null|string
		 */
		public function getLink(): ?string {
			return $this->getRoute()?->getLink();
		}
		
		/**
		 * @param string $property
		 *
		 * @return mixed
		 */
		public function getValue(string $property): mixed {
			return match ($property) {
				'published_date' => $this->getPublishedDate()->format('F jS, Y'),
				default          => $this->$property ?? NULL
			};
		}
	}