<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single web site may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	class Suggestion extends Exception {
		private string $suggestion;
		
		/**
		 * @param string $message
		 * @param string $suggestion
		 */
		public function __construct(string $message, string $suggestion) {
			parent::__construct($message);
			
			$this->suggestion = $suggestion;
		}
		
		/**
		 * @return string
		 */
		public function getSuggestion(): string {
			return $this->suggestion;
		}
	}