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
	
	namespace Items\Members;
	
	use DateTime;
	
	class Block {
		private int    $member_1;
		private int    $member_2;
		private int    $initiated_by;
		private string $timestamp;
		
		/**
		 * @return int
		 */
		public function getMember1(): int {
			return $this->member_1;
		}
		
		/**
		 * @return int
		 */
		public function getMember2(): int {
			return $this->member_2;
		}
		
		/**
		 * @return int
		 */
		public function getInitiatedBy(): int {
			return $this->initiated_by;
		}
		
		/**
		 * @return DateTime
		 */
		public function getTimestamp(): DateTime {
			return date_create($this->timestamp);
		}
		
		/**
		 * @param string $property
		 * @param mixed  $value
		 *
		 * @return void
		 */
		public function __set(string $property, mixed $value): void {
			error_log(sprintf("Unhandled property %s for class %s", $property, get_class($this)));
		}
	}