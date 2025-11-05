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
	
	enum Method: string {
		case    ANY = '["GET", "DELETE", "HEAD", "PATCH", "POST", "PUT"]';
		case   BOTH = '["GET", "POST"]';
		case   CRUD = '["GET", "DELETE", "PATCH", "POST"]';
		case DELETE = '["DELETE"]';
		case  ERROR = '[]';
		case    GET = '["GET"]';
		case   HEAD = '["HEAD"]';
		case  PATCH = '["PATCH"]';
		case   POST = '["POST"]';
		case    PUT = '["PUT"]';
		
		/**
		 * @return null|array|string
		 */
		public function getValue(): null|array|string {
			$values = json_decode($this->value);
			return match (count($values)) {
				0       => NULL,
				1       => reset($values),
				default => $values
			};
		}
	}