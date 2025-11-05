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
	
	class Request {
		/**
		 * @return Method
		 */
		public static function GetMethod(): Method {
			return match (filter_input(INPUT_SERVER, 'REQUEST_METHOD')) {
				'DELETE' => Method::DELETE,
				'GET'    => Method::GET,
				'HEAD'   => Method::HEAD,
				'PATCH'  => Method::PATCH,
				'POST'   => Method::POST,
				'PUT'    => Method::PUT,
				default  => Method::ERROR
			};
		}
		
		/**
		 * @return null|string
		 */
		public static function GetUri(): ?string {
			return strtok(filter_input(INPUT_SERVER, 'REQUEST_URI'), '?') ?: NULL;
		}
	}