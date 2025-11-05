<?php
	/*
	Copyright (c) 2023 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	namespace AuthorizeNet\AIM\Models;
	
	class Transaction {
		private array $data;
		
		protected ?int    $response_code                             = NULL;
		protected ?string $auth_code                                 = NULL;
		protected ?string $avs_result_code                           = NULL;
		protected ?string $cvv_result_code                           = NULL;
		protected ?string $cavv_result_code                          = NULL;
		protected ?string $trans_id                                  = NULL;
		protected ?string $ref_trans_id                              = NULL;
		protected ?string $trans_hash                                = NULL;
		protected ?string $test_request                              = NULL;
		protected ?string $account_number                            = NULL;
		protected ?string $account_type                              = NULL;
		protected array   $messages                                  = array();
		protected ?string $trans_hash_sha2                           = NULL;
		protected int     $supplemental_data_qualification_indicator = 0;
		protected ?string $network_trans_id                          = NULL;
		
		/**
		 * @param array $data
		 */
		public function __construct(array $data = array()) {
			$this->data = $data;
			
			if(!isset($data['transactionResponse']) || !is_array($data['transactionResponse'])) {
				return; // Prevent fatal error on invalid structure
			}
			
			foreach($data['transactionResponse'] as $key => $value) {
				$key = match ($key) {
					'responseCode'                           => 'response_code',
					'authCode'                               => 'auth_code',
					'avsResultCode'                          => 'avs_result_code',
					'cvvResultCode'                          => 'cvv_result_code',
					'cavvResultCode'                         => 'cavv_result_code',
					'transId'                                => 'trans_id',
					'refTransID'                             => 'ref_trans_id',
					'transHash'                              => 'trans_hash',
					'testRequest'                            => 'test_request',
					'accountNumber'                          => 'account_number',
					'accountType'                            => 'account_type',
					'messages'                               => 'messages',
					'transHashSha2'                          => 'trans_hash_sha2',
					'SupplementalDataQualificationIndicator' => 'supplemental_data_qualification_indicator',
					'networkTransId'                         => 'network_trans_id',
					default                                  => $key
				};
				
				if(property_exists($this, $key)) {
					$this->$key = match (gettype($this->$key)) {
						'boolean'        => (bool)$value,
						'integer'        => (int)$value,
						'double'         => (float)$value,
						'NULL', 'string' => is_array($value) ? (!empty($value) ? json_encode($value) : '') : (string)$value,
						'array'          => (array)$value,
						default          => $value
					};
				}
			}
		}
		
		/**
		 * @return array
		 */
		public function getData(): array {
			return $this->data;
		}
		
		/**
		 * @return null|int
		 */
		public function getResponseCode(): ?int {
			return $this->response_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getAuthCode(): ?string {
			return $this->auth_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getAvsResultCode(): ?string {
			return $this->avs_result_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getCvvResultCode(): ?string {
			return $this->cvv_result_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getCavvResultCode(): ?string {
			return $this->cavv_result_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getTransId(): ?string {
			return $this->trans_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getRefTransId(): ?string {
			return $this->ref_trans_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getTransHash(): ?string {
			return $this->trans_hash;
		}
		
		/**
		 * @return null|string
		 */
		public function getTestRequest(): ?string {
			return $this->test_request;
		}
		
		/**
		 * @return null|string
		 */
		public function getAccountNumber(): ?string {
			return $this->account_number;
		}
		
		/**
		 * @return null|string
		 */
		public function getAccountType(): ?string {
			return $this->account_type;
		}
		
		/**
		 * @return array
		 */
		public function getMessages(): array {
			return $this->messages;
		}
		
		/**
		 * @return null|string
		 */
		public function getTransHashSha2(): ?string {
			return $this->trans_hash_sha2;
		}
		
		/**
		 * @return int
		 */
		public function getSupplementalDataQualificationIndicator(): int {
			return $this->supplemental_data_qualification_indicator;
		}
		
		/**
		 * @return null|string
		 */
		public function getNetworkTransId(): ?string {
			return $this->network_trans_id;
		}
		
		/**         *
		 * @return string
		 */
		public function getPaymentStatus(): string {
			return match ($this->getResponseCode()) {
				1       => 'Approved',
				2       => 'Declined',
				3       => 'Error',
				4       => 'Action Required',
				default => 'Unknown'
			};
		}
		
		/**
		 * @param int $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 * @param int $depth Set the maximum depth. Must be greater than zero.
		 *
		 * @return string
		 */
		public function toJson(int $flags = JSON_ERROR_NONE, int $depth = 512): string {
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars(json_encode($this->toArray(), JSON_ERROR_NONE, $depth), ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars(json_encode($this->toArray(), JSON_ERROR_NONE, $depth), ENT_COMPAT),
				default       => json_encode($this->toArray(), $flags, $depth),
			};
		}
		
		/**
		 * @return array
		 */
		public function toArray(): array {
			return get_object_vars($this);
		}
	}