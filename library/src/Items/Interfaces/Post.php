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
	
	/** @noinspection PhpUnused */
	
	namespace Items\Interfaces;
	
	use Items\Enums\Types;
	use Items\Enums\Options;
	
	interface Post extends Item {
		/**
		 * @return string
		 */
		public function getTitle(): string;
		
		/**
		 * @return null|string
		 */
		public function getDescription(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getHeading(): ?string;
		
		/**
		 * @return string
		 */
		public function getAlt(): string;
		
		/**
		 * @return null|string
		 */
		public function getContent(): ?string;
		
		/**
		 * @return null|Types\Post
		 */
		public function getType(): ?Types\Post;
		
		/**
		 * @return null|Options\Visibility
		 */
		public function getVisibility(): ?Options\Visibility;
		/**
		 * @return int
		 */
		public function getMemberId(): int;
		
		/**
		 * @return null|string
		 */
		public function getFilename(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getImageSource(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getImage(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getThumb(): ?string;
		
		/**
		 * @return bool
		 */
		public function isApproved(): bool;
		
		/**
		 * @return int
		 */
		public function getPosition(): int;
		
		/**
		 * @return string
		 */
		public function getHash(): string;
		
		/**
		 * @return string
		 */
		public function getLink(): string;
	}