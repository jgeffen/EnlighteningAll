<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	namespace MobiusPay;
	
	use Config\MobiusPay as Config;
	use DateTime;
	use Exception;
	use Helpers;
	use JetBrains\PhpStorm\Pure;
	
	/**
	 * MobiusPay Customer Vault
	 *
	 * @link https://secure.mobiusgateway.com/merchants/resources/integration/integration_portal.php Integration Portal
	 */
	class Vault {
		public const string TYPE_ADD_CUSTOMER    = 'add_customer';
		public const string TYPE_AUTH            = 'auth';
		public const string TYPE_CREDIT          = 'credit';
		public const string TYPE_DELETE_CUSTOMER = 'delete_customer';
		public const string TYPE_SALE            = 'sale';
		public const string TYPE_UPDATE_CUSTOMER = 'update_customer';
		
		private array $billing     = array();
		private array $credit_card = array();
		private array $order       = array();
		private array $shipping    = array();
		private array $transaction = array();
		private bool  $verbose     = FALSE;
		
		private Config $config;
		private float  $amount;
		private string $account_number;
		private int    $billing_id;
		private int    $shipping_id;
		private int    $vault_id;
		private string $type;
		
		/**
		 * @param string|null $mode "development" | "production"
		 * @param string|null $config_path
		 *
		 * @throws Exception
		 */
		public function __construct(?string $mode = NULL, ?string $config_path = NULL) {
			$this->config = new Config($mode, $config_path);
		}
		
		/**
		 * @param string      $mode "development" | "production"
		 * @param string|null $config_path
		 *
		 * @throws Exception
		 */
		public function setMode(string $mode, ?string $config_path = NULL): void {
			$this->config = new Config($mode, $config_path);
		}
		
		/**
		 * Set credit card to be used for transactions
		 *
		 * @param string      $account    String representing the credit card account
		 * @param string      $type       String representing the credit card type
		 * @param string      $expiration String representing the credit card expiration
		 * @param string|null $cvv        String representing the credit card cvv
		 *
		 * @return Vault
		 *
		 * @throws Exception
		 */
		public function setCreditCard(string $account, string $type, string $expiration, ?string $cvv = NULL): Vault {
			$account = preg_replace('/[^\d]/', '', $account);
			
			if(Client::ValidateAccount($account, $type)) throw new Exception('Invalid credit card number');
			if(Client::ValidateExpiration($expiration)) throw new Exception('Invalid expiration date');
			if(!is_null($cvv) && Client::ValidateCVV($cvv, $type)) throw new Exception('Invalid CVV');
			
			$this->account_number = Client::MaskCreditCard($account);
			$this->credit_card    = array(
				'account'    => $account,
				'expiration' => $expiration,
				'cvv'        => $cvv,
				'type'       => $type
			);
			
			return $this;
		}
		
		/**
		 * Gets the string equivalent of the payment status.
		 *
		 * @return string
		 */
		public function getPaymentStatus(): string {
			return Client::PaymentStatus($this->getTransaction('response'));
		}
		
		/**
		 * Gets the response from $this->process().
		 *
		 * @param string|null $key
		 *
		 * @return array|string|null array(
		 *      'authcode'               int,        // Transaction authorization code.
		 *      'avsresponse'            string|int, // AVS response code (See Appendix 1).
		 *      'billing_id'             int,        // Customer vault billing ID
		 *      'customer_vault_id'      int,        // Customer vault lookup ID
		 *      'cvvresponse'            string,     // CVV response code (See Appendix 2).
		 *      'emv_auth_response_data' string,     // This will optionally come back when any chip card data is provided on the authorization.
		 *      'orderid'                string|int, // The original order id passed in the transaction request.
		 *      'response'               int,        // 1 = Transaction Approved, 2 = Transaction Declined, 3 = Error in transaction data or system error.
		 *      'response_code'          int,        // Numeric mapping of processor responses (See Appendix 3).
		 *      'responsetext'           string,     // Textual response.
		 *      'shipping_id'            int,        // Customer vault shipping ID
		 *      'transactionid'          string|int, // Payment gateway transaction id.
		 * )
		 *
		 * @link https://secure.mobiusgateway.com/merchants/resources/integration/integration_portal.php#transaction_response_variables
		 */
		public function getTransaction(?string $key = NULL): array|string|null {
			$array = filter_var_array($this->transaction, array(
				'authcode'               => FILTER_VALIDATE_INT,
				'avsresponse'            => FILTER_DEFAULT,
				'billing_id'             => FILTER_VALIDATE_INT,
				'customer_vault_id'      => FILTER_VALIDATE_INT,
				'cvvresponse'            => FILTER_DEFAULT,
				'emv_auth_response_data' => FILTER_DEFAULT,
				'orderid'                => FILTER_DEFAULT,
				'response'               => FILTER_VALIDATE_INT,
				'response_code'          => FILTER_VALIDATE_INT,
				'responsetext'           => FILTER_DEFAULT,
				'shipping_id'            => FILTER_VALIDATE_INT,
				'transactionid'          => FILTER_DEFAULT
			)) ?: array();
			
			return is_null($key) ? $array : $array[$key] ?? NULL;
		}
		
		/**
		 * Transaction details to be stored in the database.
		 *
		 * @param array $form_values
		 *
		 * @return array
		 */
		public function getTransactionDetails(array $form_values = array()): array {
			return array_merge($form_values, array_combine(array_map(function($key) {
				return sprintf("MOBIUSPAY_%s", strtoupper(str_replace('[', '_', trim($key, ']'))));
			}, array_keys($this->transaction)), $this->transaction));
		}
		
		/**
		 * Gets the masked account number.
		 *
		 * @return string|null
		 */
		public function getAccountNumber(): ?string {
			return $this->account_number ?? NULL;
		}
		
		/**
		 * Gets the credit card account type.
		 *
		 * @return string|null
		 */
		public function getAccountType(): ?string {
			return $this->getCreditCard('type');
		}
		
		/**
		 * @param string|null $key
		 *
		 * @return array|string|null
		 */
		private function getCreditCard(?string $key = NULL): array|string|null {
			$array = filter_var_array($this->credit_card, array(
				'account'    => FILTER_DEFAULT,
				'cvv'        => FILTER_DEFAULT,
				'expiration' => FILTER_DEFAULT,
				'type'       => FILTER_DEFAULT
			)) ?: array();
			
			return is_null($key) ? $array : $array[$key] ?? NULL;
		}
		
		/**
		 * @param string $format
		 *
		 * @return string
		 * @throws \DateMalformedStringException
		 */
		public function getExpirationDate(string $format = 'Y-m-d'): string {
			return DateTime::createFromFormat('my', $this->getCreditCard('expiration'))->modify('Last Day of This Month')->format($format);
		}
		
		/**
		 * Do transaction of matching type.
		 *
		 * @param float|null $amount Float representing the amount to be validated.
		 *
		 * @throws Exception
		 */
		public function doTransaction(?float $amount = NULL): void {
			match ($this->getType()) {
				self::TYPE_ADD_CUSTOMER    => $this->addCustomer(),
				self::TYPE_AUTH            => $this->authTransaction($amount),
				self::TYPE_CREDIT          => $this->creditTransaction($amount),
				self::TYPE_DELETE_CUSTOMER => $this->deleteCustomer(),
				self::TYPE_SALE            => $this->saleTransaction($amount),
				self::TYPE_UPDATE_CUSTOMER => $this->updateCustomer(),
				default                    => throw new Exception('Unmatched transaction type. Please refer to documentation.'),
			};
		}
		
		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}
		
		/**
		 * Sets the transaction type to process.
		 *
		 * @param string $type See TYPE_* constants.
		 *
		 * @return Vault
		 *
		 * @link https://secure.mobiusgateway.com/merchants/resources/integration/integration_portal.php#transaction_types
		 */
		public function setType(string $type): Vault {
			$this->type = $type;
			
			return $this;
		}
		
		/**
		 * @throws Exception
		 */
		private function addCustomer(): void {
			$this->process(array(
				// Vault Information
				'customer_vault'     => 'add_customer',
				
				// Transaction Information
				'type'               => 'validate',
				'payment'            => 'creditcard',
				'amount'             => 0.00,
				'ccnumber'           => $this->getCreditCard('account'),
				'ccexp'              => $this->getCreditCard('expiration'),
				'cvv'                => $this->getCreditCard('cvv'),
				
				// Order Information
				'orderid'            => $this->getOrder('id'),
				'order_description'  => $this->getOrder('description'),
				
				// Billing Information
				'first_name'         => $this->getBilling('first_name'),
				'last_name'          => $this->getBilling('last_name'),
				'company'            => $this->getBilling('company'),
				'address1'           => $this->getBilling('address_line_1'),
				'address2'           => $this->getBilling('address_line_2'),
				'city'               => $this->getBilling('city'),
				'state'              => $this->getBilling('state'),
				'zip'                => $this->getBilling('zip_code'),
				'country'            => $this->getBilling('country'),
				'phone'              => $this->getBilling('phone'),
				'fax'                => $this->getBilling('fax'),
				'email'              => $this->getBilling('email'),
				
				// Shipping Information
				'shipping_firstname' => $this->getShipping('first_name'),
				'shipping_lastname'  => $this->getShipping('last_name'),
				'shipping_company'   => $this->getShipping('company'),
				'shipping_address1'  => $this->getShipping('address_line_1'),
				'shipping_address2'  => $this->getShipping('address_line_2'),
				'shipping_city'      => $this->getShipping('city'),
				'shipping_state'     => $this->getShipping('state'),
				'shipping_zip'       => $this->getShipping('zip_code'),
				'shipping_country'   => $this->getShipping('country'),
				'shipping_phone'     => $this->getShipping('phone'),
				'shipping_fax'       => $this->getShipping('fax'),
				'shipping_email'     => $this->getShipping('email')
			));
		}
		
		/**
		 * Send NVP string to MobiusPay and return response
		 *
		 * @param array $payload
		 *
		 * @throws Exception
		 *
		 * @link https://secure.mobiusgateway.com/merchants/resources/integration/integration_portal.php#transaction_variables
		 */
		private function process(array $payload): void {
			$payload += array('security_key' => $this->config->getPrivateKey());
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_POSTFIELDS     => urldecode(http_build_query($payload)),
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_SSLVERSION     => 6,
				CURLOPT_TIMEOUT        => 45,
				CURLOPT_URL            => Client::MERCHANT_ENDPOINT,
				CURLOPT_USERAGENT      => Client::MERCHANT_USERAGENT,
				CURLOPT_VERBOSE        => $this->isVerbose()
			));
			
			$this->transaction = Client::ParseResponse(curl_exec($curl));
			
			if(!$this->transaction) {
				$curl_errno = curl_errno($curl);
				$curl_error = curl_error($curl);
				
				throw new Exception(sprintf("cURL error: [%s] %s", $curl_errno, $curl_error));
			}
		}
		
		/**
		 * @return bool
		 */
		public function isVerbose(): bool {
			return $this->verbose;
		}
		
		/**
		 * @param bool $verbose
		 *
		 * @return Vault
		 */
		public function setVerbose(bool $verbose): Vault {
			$this->verbose = $verbose;
			return $this;
		}
		
		/**
		 * @param string|null $key
		 *
		 * @return array|string|null
		 */
		public function getOrder(?string $key = NULL): array|string|null {
			$array = filter_var_array($this->order, array(
				'description' => FILTER_DEFAULT,
				'id'          => FILTER_DEFAULT,
				'ip_address'  => FILTER_DEFAULT,
				'po_number'   => FILTER_DEFAULT,
				'shipping'    => FILTER_DEFAULT,
				'tax'         => FILTER_VALIDATE_FLOAT,
				'comments'    => FILTER_DEFAULT
			)) ?: array();
			
			return is_null($key) ? $array : $array[$key] ?? NULL;
		}
		
		/**
		 * Set order information
		 *
		 * @param array $order Array representing the order information
		 *
		 * @return Vault
		 *
		 */
		public function setOrder(array $order): Vault {
			$this->order = filter_var_array($order, array(
				'description' => FILTER_DEFAULT,
				'id'          => FILTER_DEFAULT,
				'ip_address'  => FILTER_DEFAULT,
				'po_number'   => FILTER_DEFAULT,
				'shipping'    => FILTER_DEFAULT,
				'tax'         => FILTER_VALIDATE_FLOAT,
				'comments'    => FILTER_DEFAULT
			)) ?: array();
			
			return $this;
		}
		
		/**
		 * @param string|null $key
		 *
		 * @return array|string|null
		 */
		public function getBilling(?string $key = NULL): array|string|null {
			$array = filter_var_array($this->billing, array(
				'address_line_1' => FILTER_DEFAULT,
				'address_line_2' => FILTER_DEFAULT,
				'city'           => FILTER_DEFAULT,
				'company'        => FILTER_DEFAULT,
				'country'        => FILTER_DEFAULT,
				'email'          => FILTER_VALIDATE_EMAIL,
				'fax'            => FILTER_DEFAULT,
				'first_name'     => FILTER_DEFAULT,
				'last_name'      => FILTER_DEFAULT,
				'phone'          => FILTER_DEFAULT,
				'state'          => FILTER_DEFAULT,
				'website'        => FILTER_VALIDATE_URL,
				'zip_code'       => FILTER_DEFAULT
			)) ?: array();
			
			return is_null($key) ? $array : $array[$key] ?? NULL;
		}
		
		/**
		 * Set billing information.
		 *
		 * @param array $billing Array representing the customer's billing information
		 *
		 * @return Vault
		 *
		 */
		public function setBilling(array $billing): Vault {
			$this->billing = filter_var_array($billing, array(
				'address_line_1' => FILTER_DEFAULT,
				'address_line_2' => FILTER_DEFAULT,
				'city'           => FILTER_DEFAULT,
				'company'        => FILTER_DEFAULT,
				'country'        => FILTER_DEFAULT,
				'email'          => FILTER_VALIDATE_EMAIL,
				'fax'            => FILTER_DEFAULT,
				'first_name'     => FILTER_DEFAULT,
				'last_name'      => FILTER_DEFAULT,
				'phone'          => FILTER_DEFAULT,
				'state'          => FILTER_DEFAULT,
				'website'        => FILTER_VALIDATE_URL,
				'zip_code'       => FILTER_DEFAULT
			)) ?: array();
			
			return $this;
		}
		
		/**
		 * @param string|null $key
		 *
		 * @return array|string|null
		 */
		public function getShipping(?string $key = NULL): array|string|null {
			$array = filter_var_array($this->shipping, array(
				'address_line_1' => FILTER_DEFAULT,
				'address_line_2' => FILTER_DEFAULT,
				'city'           => FILTER_DEFAULT,
				'company'        => FILTER_DEFAULT,
				'country'        => FILTER_DEFAULT,
				'email'          => FILTER_VALIDATE_EMAIL,
				'first_name'     => FILTER_DEFAULT,
				'last_name'      => FILTER_DEFAULT,
				'state'          => FILTER_DEFAULT,
				'zip_code'       => FILTER_DEFAULT
			)) ?: array();
			
			return is_null($key) ? $array : $array[$key] ?? NULL;
		}
		
		/**
		 * Set shipping information.
		 *
		 * @param array $shipping Array representing the customer's shipping information
		 *
		 * @return Vault
		 *
		 */
		public function setShipping(array $shipping): Vault {
			$this->shipping = filter_var_array($shipping, array(
				'address_line_1' => FILTER_DEFAULT,
				'address_line_2' => FILTER_DEFAULT,
				'city'           => FILTER_DEFAULT,
				'company'        => FILTER_DEFAULT,
				'country'        => FILTER_DEFAULT,
				'email'          => FILTER_VALIDATE_EMAIL,
				'first_name'     => FILTER_DEFAULT,
				'last_name'      => FILTER_DEFAULT,
				'state'          => FILTER_DEFAULT,
				'zip_code'       => FILTER_DEFAULT
			)) ?: array();
			
			return $this;
		}
		
		/**
		 * Transaction authorizations are authorized immediately but are not flagged for settlement. These transactions
		 * must be flagged for settlement using the capture transaction type.
		 *
		 * @param float $amount Float representing the amount to be authorized.
		 *
		 * @throws Exception
		 */
		private function authTransaction(float $amount): void {
			$this->setAmount($amount);
			$this->process(array(
				// Transaction Information
				'type'              => 'auth',
				'amount'            => $this->getAmount(),
				'customer_vault_id' => $this->getVaultId(),
				
				// Order Information
				'ipaddress'         => $this->getOrder('ip_address'),
				'orderid'           => $this->getOrder('id'),
				'order_description' => $this->getOrder('description'),
				'tax'               => $this->getOrder('tax'),
				'shipping'          => $this->getOrder('shipping'),
				'ponumber'          => $this->getOrder('po_number')
			));
		}
		
		/**
		 * @param float|string $amount The numeric currency value.
		 */
		private function setAmount(float|string $amount): void {
			$dot_pos   = strrpos($amount, '.');
			$comma_pos = strrpos($amount, ',');
			$separator = $dot_pos > $comma_pos && $dot_pos ? $dot_pos : ($comma_pos > $dot_pos && $comma_pos ? $comma_pos : FALSE);
			
			$this->amount = !$separator
				? floatval(preg_replace('/[^0-9]/', '', $amount))
				: floatval(sprintf("%d.%d", preg_replace('/[^0-9]/', '', substr($amount, 0, $separator)), preg_replace('/[^0-9]/', '', substr($amount, $separator + 1, strlen($amount)))));
		}
		
		/**
		 * @param bool $formatted Set true to return a USD formatted string.
		 *
		 * @return float|string
		 */
		#[Pure]
		public function getAmount(bool $formatted = FALSE): float|string {
			return !$formatted ? $this->amount : Helpers::FormatCurrency($this->amount);
		}
		
		/**
		 * @return int
		 */
		public function getVaultId(): int {
			return $this->vault_id;
		}
		
		/**
		 * @param int $vault_id
		 */
		public function setVaultId(int $vault_id): void {
			$this->vault_id = $vault_id;
		}
		
		/**
		 * Transaction credits apply an amount to the cardholder's card that was not originally processed through the
		 * Gateway. In most situations credits are disabled as transaction refunds should be used instead.
		 *
		 * @param float $amount Float representing the amount to be credited.
		 *
		 * @throws Exception
		 */
		private function creditTransaction(float $amount): void {
			$this->setAmount($amount);
			$this->process(array(
				// Transaction Information
				'type'              => 'credit',
				'amount'            => $this->getAmount(),
				'customer_vault_id' => $this->getVaultId(),
				
				// Order Information
				'ipaddress'         => $this->getOrder('ip_address'),
				'orderid'           => $this->getOrder('id'),
				'order_description' => $this->getOrder('description'),
				'tax'               => $this->getOrder('tax'),
				'shipping'          => $this->getOrder('shipping'),
				'ponumber'          => $this->getOrder('po_number')
			));
		}
		
		/**
		 * @throws Exception
		 */
		private function deleteCustomer(): void {
			$this->process(array(
				// Vault Information
				'customer_vault'    => 'delete_customer',
				'customer_vault_id' => $this->getVaultId()
			));
		}
		
		/**
		 * Transaction sales are submitted and immediately flagged for settlement.
		 *
		 * @param float $amount Float representing the amount to be processed.
		 *
		 * @throws Exception
		 */
		private function saleTransaction(float $amount): void {
			$this->setAmount($amount);
			$this->process(array(
				// Transaction Information
				'type'              => 'sale',
				'amount'            => $this->getAmount(),
				'customer_vault_id' => $this->getVaultId(),
				
				// Order Information
				'ipaddress'         => $this->getOrder('ip_address'),
				'orderid'           => $this->getOrder('id'),
				'order_description' => $this->getOrder('description'),
				'tax'               => $this->getOrder('tax'),
				'shipping'          => $this->getOrder('shipping'),
				'ponumber'          => $this->getOrder('po_number')
			));
		}
		
		/**
		 * @throws Exception
		 */
		private function updateCustomer(): void {
			$this->process(array(
				// Vault Information
				'customer_vault'     => 'update_customer',
				'customer_vault_id'  => $this->getVaultId(),
				'billing_id'         => $this->getBillingId(),
				'shipping_id'        => $this->getShippingId(),
				
				// Transaction Information
				'type'               => 'validate',
				'payment'            => 'creditcard',
				'amount'             => 0.00,
				'ccnumber'           => $this->getCreditCard('account'),
				'ccexp'              => $this->getCreditCard('expiration'),
				'cvv'                => $this->getCreditCard('cvv'),
				
				// Order Information
				'orderid'            => $this->getOrder('id'),
				'order_description'  => $this->getOrder('description'),
				
				// Billing Information
				'first_name'         => $this->getBilling('first_name'),
				'last_name'          => $this->getBilling('last_name'),
				'company'            => $this->getBilling('company'),
				'address1'           => $this->getBilling('address_line_1'),
				'address2'           => $this->getBilling('address_line_2'),
				'city'               => $this->getBilling('city'),
				'state'              => $this->getBilling('state'),
				'zip'                => $this->getBilling('zip_code'),
				'country'            => $this->getBilling('country'),
				'phone'              => $this->getBilling('phone'),
				'fax'                => $this->getBilling('fax'),
				'email'              => $this->getBilling('email'),
				
				// Shipping Information
				'shipping_firstname' => $this->getShipping('first_name'),
				'shipping_lastname'  => $this->getShipping('last_name'),
				'shipping_company'   => $this->getShipping('company'),
				'shipping_address1'  => $this->getShipping('address_line_1'),
				'shipping_address2'  => $this->getShipping('address_line_2'),
				'shipping_city'      => $this->getShipping('city'),
				'shipping_state'     => $this->getShipping('state'),
				'shipping_zip'       => $this->getShipping('zip_code'),
				'shipping_country'   => $this->getShipping('country'),
				'shipping_phone'     => $this->getShipping('phone'),
				'shipping_fax'       => $this->getShipping('fax'),
				'shipping_email'     => $this->getShipping('email')
			));
		}
		
		/**
		 * @return int|null
		 */
		public function getBillingId(): ?int {
			return $this->billing_id;
		}
		
		/**
		 * @param int $billing_id
		 */
		public function setBillingId(int $billing_id): void {
			$this->billing_id = $billing_id;
		}
		
		/**
		 * @return int
		 */
		public function getShippingId(): int {
			return $this->shipping_id;
		}
		
		/**
		 * @param int $shipping_id
		 */
		public function setShippingId(int $shipping_id): void {
			$this->shipping_id = $shipping_id;
		}
		
		/**
		 * @return bool
		 */
		#[Pure]
		public function isSandbox(): bool {
			return $this->config->getMode() == Config::DEVELOPMENT_MODE;
		}
	}