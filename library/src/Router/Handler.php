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
	
	namespace Router;
	
	class Handler {
		private string $path;
		private string $section;
		private array  $intent;
		
		/**
		 * @param string $path
		 */
		public function __construct(string $path) {
			$this->path    = $path;
			$temp_array    = explode('/', $path);
			$this->section = array_shift($temp_array);
			$this->intent  = $temp_array;
		}
		
		/**
		 * @return string
		 */
		public function getPath(): string {
			return $this->path;
		}
		
		/**
		 * @return string
		 */
		public function getSection(): string {
			return $this->section;
		}
		
		/**
		 * @param int $index
		 *
		 * @return null|string
		 */
		public function getIntent(int $index = 0): ?string {
			return $this->intent[$index] ?? NULL;
		}
		
		/**
		 * @param string $intent
		 *
		 * @return $this
		 */
		public function setIntent(string $intent): self {
			$this->intent = explode('/', $intent);
			return $this;
		}
	}