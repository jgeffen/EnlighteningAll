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
	
	use DateTime;
	
	interface Listed extends Item {
		/**
		 * @return null|string
		 */
		public function getHeading(): ?string;
		
		/**
		 * @return null|string
		 */
		public function getContent(): ?string;
		
		/**
		 * @return bool
		 */
		public function isPublished(): bool;
		
		/**
		 * @return DateTime
		 */
		public function getPublishedDate(): DateTime;
	}