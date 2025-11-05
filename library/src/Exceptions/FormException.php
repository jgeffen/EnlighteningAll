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
	
	use JetBrains\PhpStorm\Pure;
	
	class FormException extends Exception {
		private array $errors;
		
		/**
		 * FormException constructor.
		 *
		 * @param string[]       $errors
		 * @param string         $message
		 * @param int            $code
		 * @param Exception|null $previous
		 */
		#[Pure] public function __construct(array $errors, string $message = '', int $code = 0, ?Exception $previous = NULL) {
			parent::__construct($message, $code, $previous);
			
			$this->errors = $errors;
		}
		
		/**
		 * @return string[]
		 */
		public function getErrors(): array { return $this->errors; }
	}