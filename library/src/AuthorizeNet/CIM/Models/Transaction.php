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
	
	namespace AuthorizeNet\CIM\Models;
	
	class Transaction {
		protected ?int    $response_code                             = NULL;
		protected ?string $auth_code                                 = NULL;
		protected ?string $avs_result_code                           = NULL;
		protected ?string $cvv_result_code                           = NULL;
		protected ?string $cavv_result_code                          = NULL;
		protected ?string $trans_id                                  = NULL;
		protected ?string $ref_trans_id                              = NULL;
		protected ?string $trans_hash                                = NULL;
		protected ?string $test_request                              = NULL;
		protected ?string $status                                    = NULL;
		protected ?string $account_number                            = NULL;
		protected ?string $account_type                              = NULL;
		protected array   $messages                                  = array();
		protected ?string $trans_hash_sha2                           = NULL;
		protected int     $supplemental_data_qualification_indicator = 0;
		protected ?string $network_trans_id                          = NULL;
		protected ?string $customer_vault_id                         = NULL;
		protected ?string $customer_profile_id                       = NULL;
		protected ?string $payment_profile_id                        = NULL;
		protected ?string $billing_id                                = NULL;
		protected ?string $expiration_date                           = NULL;
		private array     $data;
		
		/**
		 * @param array $data
		 */
		public function __construct(array $data = array()) {
			$this->data = $data;
			
			// 1) Standard transactionResponse (sale/auth/capture/refund/etc.)
			if(!empty($data['transactionResponse']) && is_array($data['transactionResponse'])) {
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
			
			// 2) CIM profile responses (createCard/updateCard/etc.)
			//    Prefer flat fields you pass in first, then fall back to nested paymentProfile.*
			//    IDs as strings is fine (opaque identifiers); expiration normalized to Y-m-d.
			$flat_map = array(
				'status'              => 'status',
				'account_number'      => 'account_number',
				'account_type'        => 'account_type',
				'customer_vault_id'   => 'customer_vault_id',
				'customer_profile_id' => 'customer_profile_id',
				'payment_profile_id'  => 'payment_profile_id',
				'billing_id'          => 'billing_id',
				'expiration_date'     => 'expiration_date',
				'response'            => 'test_request' // optional: if you want to keep a textual "Ok"/"Error" somewhere; remove if not desired
			);
			
			foreach($flat_map as $in_key => $prop) {
				if(array_key_exists($in_key, $data) && property_exists($this, $prop)) {
					$val = $data[$in_key];
					if($prop === 'expiration_date' && is_string($val)) {
						// Accept already-normalized Y-m-d or try to repair from YYYY-MM
						if(preg_match('/^\d{4}-\d{2}$/', $val)) {
							$val = $val . '-01';
						}
					}
					// Assign with safe casting consistent with your earlier logic
					$this->$prop = match (gettype($this->$prop)) {
						'boolean'        => (bool)$val,
						'integer'        => (int)$val,
						'double'         => (float)$val,
						'NULL', 'string' => is_array($val) ? (!empty($val) ? json_encode($val) : '') : (string)$val,
						'array'          => (array)$val,
						default          => $val
					};
				}
			}
			
			// 3) Pull from nested paymentProfile if present and not already set by flat fields
			if(!empty($data['paymentProfile']) && is_array($data['paymentProfile'])) {
				$pp = $data['paymentProfile'];
				
				// IDs
				if(property_exists($this, 'customer_profile_id') && empty($this->customer_profile_id) && !empty($pp['customerProfileId'])) {
					$this->customer_profile_id = (string)$pp['customerProfileId'];
				}
				if(property_exists($this, 'payment_profile_id') && empty($this->payment_profile_id) && !empty($pp['customerPaymentProfileId'])) {
					$this->payment_profile_id = (string)$pp['customerPaymentProfileId'];
				}
				if(property_exists($this, 'billing_id') && empty($this->billing_id) && !empty($pp['customerPaymentProfileId'])) {
					$this->billing_id = (string)$pp['customerPaymentProfileId'];
				}
				
				// Card summary
				if(!empty($pp['payment']['creditCard']) && is_array($pp['payment']['creditCard'])) {
					$cc = $pp['payment']['creditCard'];
					if(property_exists($this, 'account_number') && empty($this->account_number) && !empty($cc['cardNumber'])) {
						$this->account_number = (string)$cc['cardNumber']; // masked
					}
					if(property_exists($this, 'account_type') && empty($this->account_type) && !empty($cc['cardType'])) {
						$this->account_type = strtolower($cc['cardType']);
					}
					if(property_exists($this, 'expiration_date') && empty($this->expiration_date) && !empty($cc['expirationDate'])) {
						// cc['expirationDate'] often "YYYY-MM" or "XXXX" in some responses
						if(preg_match('/^\d{4}-\d{2}$/', $cc['expirationDate'])) {
							$this->expiration_date = $cc['expirationDate'] . '-01';
						}
					}
				}
			}
			
			// 4) Ensure arrays default properly
			if(!is_array($this->messages)) {
				$this->messages = array();
			}
		}
		
		/**
		 * @return array
		 */
		public function getData(): array {
			return $this->data;
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
		 * @return null|int
		 */
		public function getResponseCode(): ?int {
			return $this->response_code;
		}
		
		/**
		 * @return ?string
		 */
		public function getStatus(): ?string {
			return $this->status;
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
		
		/**
		 * @return null|string
		 */
		public function getCustomerVaultId(): ?string {
			return $this->customer_vault_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getCustomerProfileId(): ?string {
			return $this->customer_profile_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getPaymentProfileId(): ?string {
			return $this->payment_profile_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingId(): ?string {
			return $this->billing_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getExpirationDate(): ?string {
			return $this->expiration_date;
		}
	}