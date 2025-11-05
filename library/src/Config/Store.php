<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	namespace Config;
	
	use Exception;
	use Helpers;
	use Debug;
	
	class Store {
		protected float  $sales_tax_rate;
		protected string $config_path;
		
		/**
		 * @throws Exception
		 */
		public function __construct() {
			$this->config_path = Helpers::PathAbsolute('/library/settings/store.json');
			
			if(!$config = json_decode(file_get_contents($this->config_path), TRUE)) throw new Exception(sprintf("Unable to open %s.", basename($this->config_path)));
			
			foreach($config as $key => $value) {
				if(property_exists($this, $key)) {
					$this->$key = $value;
				}
			}
		}
		
		/**
		 * @param bool $percentage
		 *
		 * @return float
		 */
		public function getSalesTaxRate(bool $percentage = FALSE): float {
			return !$percentage ? $this->sales_tax_rate : $this->sales_tax_rate * 100;
		}
		
		/**
		 * @return string
		 */
		public function getConfigPath(): string {
			return $this->config_path;
		}
		
		/**
		 * Converts special characters of property to HTML entities
		 *
		 * @param string $property
		 * @param int    $flags Available {@link https://www.php.net/manual/en/function.htmlspecialchars.php#refsect1-function.htmlspecialchars-parameters flags} constants
		 *
		 * @return null|string
		 */
		public function getEncoded(string $property, int $flags = ENT_COMPAT): ?string {
			try {
				if(property_exists($this, $property)) {
					return isset($this->$property) ? htmlspecialchars((string)$this->$property, $flags) : NULL;
				} else throw new Exception(sprintf("Invalid property %s for class %s", $property, get_class($this)));
			} catch(Exception $exception) {
				Debug::Exception($exception);
			}
			
			return NULL;
		}
	}