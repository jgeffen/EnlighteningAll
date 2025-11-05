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
	
	namespace Items\Traits;
	
	use DateTime;
	use Helpers;
	
	trait Listed {
		protected ?string $heading;
		protected ?string $content;
		protected bool    $published;
		protected string  $published_date;
		
		/**
		 * @param null|int $length
		 *
		 * @return string
		 */
		public function getHeading(?int $length = NULL): string {
			return is_null($length) ? $this->heading : Helpers::Truncate($this->heading, $length);
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
	}