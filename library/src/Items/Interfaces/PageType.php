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
	
	namespace Items\Interfaces;
	
	use DateTime;
	use Router;
	
	interface PageType extends Item {
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
		 * @return string
		 */
		public function getContentPreview(): string;
		
		/**
		 * @return bool
		 */
		public function isPublished(): bool;
		
		/**
		 * @return DateTime
		 */
		public function getPublishedDate(): DateTime;
		
		/**
		 * @return null|string
		 */
		public function getYoutubeId(): ?string;
		
		/**
		 * @return string
		 */
		public function getYoutubeEmbed(): string;
		
		/**
		 * @return null|string
		 */
		public function getFilename(): ?string;
		
		/**
		 * @return array
		 */
		public function getImages(): array;
		
		/**
		 * @return void
		 */
		public function setImages(): void;
		
		/**
		 * @return null|string
		 */
		public function getImage(): ?string;
		
		/**
		 * @return bool
		 */
		public function hasImage(): bool;
		
		/**
		 * @return null|string
		 */
		public function getSquareImage(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getSquareThumb(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getLandscapeImage(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getLandscapeThumb(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getPortraitImage(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getPortraitThumb(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getFilenameAlt(): ?string;
		
		/**
		 * @return string
		 */
		public function getPageUrl(): string;
		
		/**
		 * @return null|int
		 */
		public function getPosition(): ?int;
		
		/**
		 * @return null|Router\Route
		 */
		public function getRoute(): ?Router\Route;
		
		/**
		 * @return null|string
		 */
		public function getLink(): ?string;
		
		/**
		 * @param string $property
		 *
		 * @return mixed
		 */
		public function getValue(string $property): mixed;
	}