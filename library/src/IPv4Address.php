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
	
	use GeoIp2\Database\Reader;
	use GeoIp2\Model\City as Model;
	
	class IPv4Address implements Stringable {
		private Reader  $reader;
		private ?Model  $model        = NULL;
		private ?string $ip_address   = NULL;
		private ?string $country_name = NULL;
		private ?string $country_code = NULL;
		private ?string $state_name   = NULL;
		private ?string $state_code   = NULL;
		private ?string $city_name    = NULL;
		private ?string $postal_code  = NULL;
		private ?float  $latitude     = NULL;
		private ?float  $longtitude   = NULL;
		
		/**
		 * @param null|string $ip_address
		 */
		public function __construct(?string $ip_address) {
			if(filter_var($ip_address, FILTER_VALIDATE_IP)) {
				$data = Database::Action("SELECT * FROM `ip_addresses` WHERE `ip_address` = :ip_address AND TIMESTAMPDIFF(MONTH, GREATEST(`timestamp`, `last_timestamp`), NOW()) <= :months", array(
					'ip_address' => $ip_address,
					'months'     => 3
				))->fetch(PDO::FETCH_ASSOC);
				
				if(!empty($data)) {
					$this->ip_address   = $ip_address;
					$this->country_name = $data['country_name'];
					$this->country_code = $data['country_code'];
					$this->state_name   = $data['state_name'];
					$this->state_code   = $data['state_code'];
					$this->city_name    = $data['city_name'];
					$this->postal_code  = $data['postal_code'];
					$this->latitude     = $data['latitude'];
					$this->longtitude   = $data['longtitude'];
				} else {
					$this->reader = new Reader('/usr/share/GeoIP/GeoLite2-City.mmdb');
					
					try {
						$this->ip_address   = $ip_address;
						$this->model        = $this->reader->city($this->ip_address);
						$this->country_name = $this->model->country->name;
						$this->country_code = $this->model->country->isoCode;
						$this->state_name   = $this->model->mostSpecificSubdivision->name;
						$this->state_code   = $this->model->mostSpecificSubdivision->isoCode;
						$this->city_name    = $this->model->city->name;
						$this->postal_code  = $this->model->postal->code;
						$this->latitude     = $this->model->location->latitude;
						$this->longtitude   = $this->model->location->longitude;
					} catch(Exception) {
						$this->model = NULL;
					}
					
					Database::Action("INSERT INTO `ip_addresses` SET `ip_address` = :ip_address, `country_name` = :country_name, `country_code` = :country_code, `state_name` = :state_name, `state_code` = :state_code, `city_name` = :city_name, `postal_code` = :postal_code, `latitude` = :latitude, `longtitude` = :longtitude ON DUPLICATE KEY UPDATE `country_name` = :country_name, `country_code` = :country_code, `state_name` = :state_name, `state_code` = :state_code, `city_name` = :city_name, `postal_code` = :postal_code, `latitude` = :latitude, `longtitude` = :longtitude", array(
						'ip_address'   => $this->ip_address,
						'country_name' => $this->country_name,
						'country_code' => $this->country_code,
						'state_name'   => $this->state_name,
						'state_code'   => $this->state_code,
						'city_name'    => $this->city_name,
						'postal_code'  => $this->postal_code,
						'latitude'     => $this->latitude,
						'longtitude'   => $this->longtitude
					));
					
					$this->reader->close();
				}
			}
		}
		
		/**
		 * @return null|Rinvex\Country\Country
		 */
		public function getCountry(): ?Rinvex\Country\Country {
			try {
				return !is_null($this->country_code) ? Rinvex\Country\CountryLoader::country($this->country_code) : NULL;
			} catch(Rinvex\Country\CountryLoaderException) {
				return NULL;
			}
		}
		
		/**
		 * @return null|string
		 */
		public function getCountryName(): ?string {
			return $this->country_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getCountryCode(): ?string {
			return $this->country_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getStateName(): ?string {
			return $this->state_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getStateCode(): ?string {
			return $this->state_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getCityName(): ?string {
			return $this->city_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getPostalCode(): ?string {
			return $this->postal_code;
		}
		
		/**
		 * @return null|float
		 */
		public function getLatitude(): ?float {
			return $this->latitude;
		}
		
		/**
		 * @return null|float
		 */
		public function getLongtitude(): ?float {
			return $this->longtitude;
		}
		
		/**
		 * @return null|string
		 */
		public function getValue(): ?string {
			return $this->ip_address;
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return sprintf("https://ipinfo.io/%s", $this->getValue());
		}
		
		/**
		 * @return string
		 */
		public function __toString(): string {
			return $this->ip_address ?? '';
		}
	}