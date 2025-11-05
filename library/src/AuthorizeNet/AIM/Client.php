<?php
	/*
	Copyright (c) 2020, 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	namespace AuthorizeNet\AIM;
	
	use AuthorizeNet\AIM;
	use Config\AuthorizeNet as Config;
	use DateTime;
	use Exception;
	use Freelancehunt\Validators;
	use Helpers;
	use Omnipay\AuthorizeNet as AuthNet;
	use Omnipay\Common;
	
	/**
	 * AuthorizeNet AIM Client
	 *
	 * @Link: https://developer.authorize.net/api/reference/index.html#payment-transactions
	 */
	class Client {
		public const string MERCHANT_NAME      = 'AuthorizeNet';
		public const string MERCHANT_USERAGENT = 'AuthorizeNetAIMClient-PHP';
		public const int    RESPONSE_OK        = 1;
		public const int    RESPONSE_DECLINED  = 2;
		public const int    RESPONSE_ERROR     = 3;
		public const int    RESPONSE_HELD      = 4;
		
		public const string TYPE_AUTHORIZE = 'authorize';
		public const string TYPE_CAPTURE   = 'capture';
		public const string TYPE_CREDIT    = 'credit';
		public const string TYPE_PURCHASE  = 'purchase';
		public const string TYPE_REFUND    = 'refund';
		public const string TYPE_UPDATE    = 'update';
		public const string TYPE_VALIDATE  = 'validate';
		public const string TYPE_VOID      = 'void';
		
		private AIM\Models\Billing          $billing;
		private AIM\Models\CreditCard       $credit_card;
		private AIM\Models\Order            $order;
		private AIM\Models\Shipping         $shipping;
		private AIM\Models\Transaction      $transaction;
		private AuthNet\Message\AIMResponse $response;
		
		private Config  $config;
		private float   $amount;
		private string  $account_number;
		private ?string $invoice;
		private ?string $transaction_id;
		private string  $type;
		private bool    $verbose = FALSE;
		
		/**
		 * @param string|null $mode "development" | "production"
		 *
		 * @throws Exception
		 */
		public function __construct(?string $mode = NULL) {
			$this->config = new Config($mode);
		}
		
		/**
		 * Gets form options for MobiusPay.
		 *
		 * @param string|null $type
		 * @param string|null $sub_type
		 * @param string|null $key
		 *
		 * @return array|string|null
		 */
		public static function FormOptions(?string $type = NULL, ?string $sub_type = NULL, ?string $key = NULL): array|string|null {
			$array = match ($type) {
				'countries'         => array(
					'US' => 'United States'
				),
				'credit_card_types' => array(
					'Visa'       => 'Visa',
					'MasterCard' => 'Master Card',
					'Discover'   => 'Discover',
					'Amex'       => 'American Express'
				),
				'expiration_months' => array_reduce(range(1, 12), function($items, $item) {
					$items[str_pad($item, 2, '0', STR_PAD_LEFT)] = str_pad($item, 2, '0', STR_PAD_LEFT) . ' - ' . date('F', mktime(0, 0, 0, $item, 10));
					return $items;
				}),
				'expiration_years'  => array_reduce(range(date('Y'), date('Y', strtotime('+15 Years'))), function($items, $item) {
					$items[DateTime::createFromFormat('Y', $item)->format('y')] = $item;
					return $items;
				}),
				'states'            => match ($sub_type) {
					'US'    => array(
						'AL' => 'Alabama',
						'AK' => 'Alaska',
						'AZ' => 'Arizona',
						'AR' => 'Arkansas',
						'CA' => 'California',
						'CO' => 'Colorado',
						'CT' => 'Connecticut',
						'DE' => 'Delaware',
						'DC' => 'District of Columbia',
						'FL' => 'Florida',
						'GA' => 'Georgia',
						'HI' => 'Hawaii',
						'ID' => 'Idaho',
						'IL' => 'Illinois',
						'IN' => 'Indiana',
						'IA' => 'Iowa',
						'KS' => 'Kansas',
						'KY' => 'Kentucky',
						'LA' => 'Louisiana',
						'ME' => 'Maine',
						'MD' => 'Maryland',
						'MA' => 'Massachusetts',
						'MI' => 'Michigan',
						'MN' => 'Minnesota',
						'MS' => 'Mississippi',
						'MO' => 'Missouri',
						'MT' => 'Montana',
						'NE' => 'Nebraska',
						'NV' => 'Nevada',
						'NH' => 'New Hampshire',
						'NJ' => 'New Jersey',
						'NM' => 'New Mexico',
						'NY' => 'New York',
						'NC' => 'North Carolina',
						'ND' => 'North Dakota',
						'OH' => 'Ohio',
						'OK' => 'Oklahoma',
						'OR' => 'Oregon',
						'PA' => 'Pennsylvania',
						'RI' => 'Rhode Island',
						'SC' => 'South Carolina',
						'SD' => 'South Dakota',
						'TN' => 'Tennessee',
						'TX' => 'Texas',
						'UT' => 'Utah',
						'VT' => 'Vermont',
						'VA' => 'Virginia',
						'WA' => 'Washington',
						'WV' => 'West Virginia',
						'WI' => 'Wisconsin',
						'WY' => 'Wyoming'
					),
					default => array(),
				},
				default             => array(),
			};
			
			return is_null($key) ? $array : $array[$key] ?? NULL;
		}
		
		/**
		 * Validate the expiration date
		 *
		 * @param string $expiration String representing 2-digit month and 2/4 digit year of credit card expiration
		 *
		 * @return bool
		 */
		public static function ValidateExpiration(string $expiration): bool {
			list($month, $year) = strlen($expiration) == 6
				? array(substr($expiration, 0, 2), substr($expiration, -4))
				: array(substr($expiration, 0, 2), '20' . substr($expiration, -2));
			
			return !Validators\CreditCard::validDate($year, $month);
		}
		
		/**
		 * Validate the CVV
		 *
		 * @param string $cvv  String representing the credit card cvv
		 * @param string $type String representing the credit card type
		 *
		 * @return bool
		 */
		public static function ValidateCVV(string $cvv, string $type): bool {
			return !Validators\CreditCard::validCvc($cvv, strtolower($type));
		}
		
		/**
		 * Converts Name-Value pair to array
		 *
		 * @param string $string String representing a Name-Value pair
		 *
		 * @return array
		 */
		public static function ParseResponse(string $string): array {
			$array = array();
			
			while(strlen($string)) {
				$key_pos         = strpos($string, '=');
				$key_val         = substr($string, 0, $key_pos);
				$val_pos         = strpos($string, '&') ?: strlen($string);
				$val_val         = substr($string, $key_pos + 1, $val_pos - $key_pos - 1);
				$array[$key_val] = urldecode($val_val);
				$string          = substr($string, $val_pos + 1, strlen($string));
			}
			
			return $array;
		}
		
		/**
		 * Generates an invoice number based on the parameters sent.
		 *
		 * @param string $prefix
		 * @param mixed  ...$details
		 *
		 * @return string
		 */
		public static function GenerateInvoice(string $prefix = 'AN', ...$details): string {
			return sprintf("%s-%s", $prefix, substr(strtoupper(md5(implode($details) . time())), 0, 7));
		}
		
		/**
		 * Normalize MM + YY/ YYYY into Authorize.Net format "YYYY-MM".
		 *
		 * @param string $mm Two-digit month ("01".."12")
		 * @param string $yy Two-digit ("25") or four-digit ("2025") year
		 *
		 * @return string "YYYY-MM"
		 */
		public static function FormatExpiration(string $mm, string $yy): string {
			$mm = sprintf('%02d', (int)preg_replace('/\D+/', '', $mm));
			$yr = preg_replace('/\D+/', '', $yy);
			
			$yyyy = (strlen($yr) === 2) ? (2000 + (int)$yr) : (int)$yr;
			
			return sprintf('%04d-%02d', $yyyy, (int)$mm);
		}
		
		/**
		 * @param bool       $formatted Set true to return a USD formatted string.
		 * @param float|null $discount  Optional discount amount to subtract.
		 *
		 * @return float|string
		 */
		public function getTotal(bool $formatted = FALSE, ?float $discount = NULL): float|string {
			$total = $this->amount + $this->getOrder()->getShipping() + $this->getOrder()->getSalesTax();
			
			if(!is_null($discount) && $discount > 0) {
				$total -= $discount;
			}
			
			return !$formatted ? $total : Helpers::FormatCurrency($total);
		}
		
		/**
		 * @return \AuthorizeNet\AIM\Models\Shipping
		 */
		public function getShipping(): AIM\Models\Shipping {
			return $this->shipping ??= new AIM\Models\Shipping();
		}
		
		/**
		 * Set shipping information.
		 *
		 * @param null|string $address_line_1
		 * @param null|string $address_line_2
		 * @param null|string $city
		 * @param null|string $company
		 * @param null|string $country
		 * @param null|string $email
		 * @param null|string $first_name
		 * @param null|string $last_name
		 * @param null|string $state
		 * @param null|string $zip_code
		 *
		 * @return Client
		 *
		 */
		public function setShipping(?string $address_line_1, ?string $address_line_2, ?string $city, ?string $company, ?string $country, ?string $email, ?string $first_name, ?string $last_name, ?string $state, ?string $zip_code): Client {
			$this->shipping = new AIM\Models\Shipping(
				address_line_1 : filter_var($address_line_1),
				address_line_2 : filter_var($address_line_2),
				city           : filter_var($city),
				company        : filter_var($company),
				country        : filter_var($country),
				email          : filter_var($email, FILTER_VALIDATE_EMAIL),
				first_name     : filter_var($first_name),
				last_name      : filter_var($last_name),
				state          : filter_var($state),
				zip_code       : filter_var($zip_code)
			);
			
			return $this;
		}
		
		/**
		 * @return \AuthorizeNet\AIM\Models\Order
		 */
		public function getOrder(): AIM\Models\Order {
			return $this->order ??= new AIM\Models\Order();
		}
		
		/**
		 * Set order information
		 *
		 * @param float       $amount
		 * @param null|string $description
		 * @param null|string $id
		 * @param null|string $ip_address
		 * @param null|string $po_number
		 * @param float       $shipping
		 * @param float       $sales_tax
		 * @param float       $discount
		 * @param null|string $comments
		 * @param null|string $invoice
		 * @param null|int    $duplicateWindow
		 *
		 * @return Client
		 */
		public function setOrder(float $amount, ?string $description, ?string $id, ?string $ip_address, ?string $po_number, float $shipping, float $sales_tax, float $discount, ?string $comments, ?string $invoice, ?int $duplicateWindow): Client {
			$this->invoice = $invoice;
			$this->order   = new AIM\Models\Order(
				amount          : filter_var($amount, FILTER_VALIDATE_FLOAT),
				description     : filter_var($description),
				id              : filter_var($id),
				ip_address      : filter_var($ip_address, FILTER_VALIDATE_IP),
				po_number       : filter_var($po_number),
				shipping        : filter_var($shipping, FILTER_VALIDATE_FLOAT),
				sales_tax       : filter_var($sales_tax, FILTER_VALIDATE_FLOAT),
				discount        : filter_var($discount, FILTER_VALIDATE_FLOAT),
				comments        : filter_var($comments),
				invoice         : filter_var($invoice),
				duplicateWindow : filter_var($duplicateWindow)
			);
			
			return $this;
		}
		
		/**
		 * @param string $mode "development" | "production"
		 *
		 * @throws Exception
		 */
		public function setMode(string $mode): void {
			$this->config = new Config($mode);
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
				return sprintf("%s_%s", strtoupper(self::MERCHANT_NAME), strtoupper(str_replace('[', '_', trim($key, ']'))));
			}, array_keys($this->getTransaction()->toArray())), $this->getTransaction()->toArray()));
		}
		
		/**
		 * @return \AuthorizeNet\AIM\Models\Transaction
		 */
		public function getTransaction(): AIM\Models\Transaction {
			return $this->transaction;
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
			return $this->getCreditCard()->getType();
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
		 * @return Client
		 *
		 * @link https://secure.mobiusgateway.com/merchants/resources/integration/integration_portal.php#transaction_types
		 */
		public function setType(string $type): Client {
			$this->type = $type;
			
			return $this;
		}
		
		/**
		 * @return \AuthorizeNet\AIM\Models\CreditCard
		 */
		public function getCreditCard(): AIM\Models\CreditCard {
			return $this->credit_card ??= new AIM\Models\CreditCard();
		}
		
		/**
		 * Set credit card to be used for transactions
		 *
		 * @param string $account    String representing the credit card account
		 * @param string $expiration String representing the credit card expiration
		 * @param string $cvv        String representing the credit card cvv
		 *
		 * @return Client
		 *
		 * @throws Exception
		 */
		public function setCreditCard(string $account, string $expiration, string $cvv): Client {
			$credit_card = self::ValidateCard(preg_replace('/\D/', '', $account));
			$expiration  = preg_replace('/\D/', '', $expiration);
			
			if(!$credit_card['valid']) throw new Exception('Invalid credit card number');
			
			$this->account_number = self::MaskCreditCard($credit_card['number']);
			$this->credit_card    = new AIM\Models\CreditCard(
				account    : filter_var($credit_card['number']),
				expiration : filter_var($expiration),
				cvv        : filter_var($cvv),
				type       : filter_var($credit_card['type'])
			);
			
			return $this;
		}
		
		/**
		 * Validate a credit card
		 *
		 * @param string $account String representing the credit card number
		 *
		 * @return array{ valid: bool, number: string, type: string }
		 */
		public static function ValidateCard(string $account): array {
			return Validators\CreditCard::validCreditCard($account);
		}
		
		/**
		 * Masks credit card number for PCI compliance.
		 *
		 * @param $credit_card
		 *
		 * @return string
		 */
		public static function MaskCreditCard($credit_card): string {
			$credit_card = (string)preg_replace('/[^0-9]/', '', $credit_card);
			$length      = strlen($credit_card);
			
			return substr($credit_card, 0, 1) . str_repeat('X', $length - 5) . substr($credit_card, $length - 4, 4);
		}
		
		/**
		 * @param string $format
		 *
		 * @return string
		 * @throws \DateMalformedStringException
		 */
		public function getExpirationDate(string $format = 'Y-m-d'): string {
			return DateTime::createFromFormat('my', $this->getCreditCard()->getExpiration())->modify('Last Day of This Month')->format($format);
		}
		
		/**
		 * Is the response successful?
		 *
		 * @return boolean
		 */
		public function isSuccessful(): bool {
			return strtoupper($this->getResponse()->getResultCode() ?? '') === 'OK';
		}
		
		/**
		 * @return null|string
		 */
		public function getResultCode(): ?string {
			// Map it to whatever makes sense in MobiusPay
			return isset($this->data['response'])
				? (string)$this->data['response']
				: NULL;
		}
		
		/**
		 * @return AuthNet\Message\AIMResponse
		 */
		public function getResponse(): AuthNet\Message\AIMResponse {
			return $this->response;
		}
		
		/**
		 * Response Message
		 *
		 * @return null|string A response message from the payment gateway
		 */
		public function getMessage(): ?string {
			return $this->data['responsetext'] ?? NULL;
		}
		
		/**
		 * @return null|string
		 */
		public function getReasonCode(): ?string {
			// Map to MobiusPay's 'response_code' field
			return isset($this->data['response_code'])
				? (string)$this->data['response_code']
				: NULL;
		}
		
		/**
		 * @return string
		 */
		public function getPaymentStatus(): string {
			return match ($this->response->getResultCode()) {
				1       => 'Approved',
				2       => 'Declined',
				3       => 'Error',
				4       => 'Action Required',
				default => 'Unknown'
			};
		}
		
		/**
		 * Do transaction of matching type.
		 *
		 * @param null|float|string $amount Float representing the amount to be validated.
		 *
		 * @return void
		 *
		 * @throws Exception
		 */
		public function doTransaction(null|float|string $amount = NULL): void {
			match ($this->getType()) {
				self::TYPE_AUTHORIZE => $this->doAuthorization($amount),
				self::TYPE_CAPTURE   => $this->doCapture($amount),
				self::TYPE_CREDIT    => $this->doCredit($amount),
				self::TYPE_PURCHASE  => $this->doPurchase($amount),
				self::TYPE_REFUND    => $this->doRefund($amount),
				self::TYPE_UPDATE    => $this->doUpdate(),
				self::TYPE_VALIDATE  => $this->doValidate($amount),
				self::TYPE_VOID      => $this->doVoid(),
				default              => throw new Exception('Unmatched transaction type. Please refer to documentation.'),
			};
		}
		
		/**
		 * Transaction authorizations are authorized immediately but are not flagged for settlement. These transactions
		 * must be flagged for settlement using the capture transaction type.
		 *
		 * @param float|string $amount Float representing the amount to be authorized.
		 *
		 * @return void
		 *
		 * @throws Exception
		 */
		private function doAuthorization(float|string $amount): void {
			$this->setAmount($amount)->process(array(
				// Transaction Information
				'type'                => $this->getType(),
				'amount'              => $this->getAmount(),
				'ccnumber'            => $this->getCreditCard()->getAccount(),
				'ccexp'               => $this->getCreditCard()->getExpiration(),
				'cvv'                 => $this->getCreditCard()->getCvv(),
				
				// Order Information
				'ipaddress'           => $this->getOrder()->getIpAddress(),
				'orderid'             => $this->getOrder()->getId(),
				'order_description'   => $this->getOrder()->getDescription(),
				'sales_tax'           => $this->getOrder()->getSalesTax(),
				'shipping'            => $this->getOrder()->getShipping(),
				'ponumber'            => $this->getOrder()->getPoNumber(),
				
				// Billing Information
				'first_name'          => $this->getBilling()->getFirstName(),
				'last_name'           => $this->getBilling()->getLastName(),
				'company'             => $this->getBilling()->getCompany(),
				'address1'            => $this->getBilling()->getAddressLine1(),
				'address2'            => $this->getBilling()->getAddressLine2(),
				'city'                => $this->getBilling()->getCity(),
				'state'               => $this->getBilling()->getState(),
				'zip'                 => $this->getBilling()->getZipCode(),
				'country'             => $this->getBilling()->getCountry(),
				'phone'               => $this->getBilling()->getPhone(),
				'fax'                 => $this->getBilling()->getFax(),
				'email'               => $this->getBilling()->getEmail(),
				'website'             => $this->getBilling()->getWebsite(),
				
				// Shipping Information
				'shipping_first_name' => $this->getShipping()->getFirstName(),
				'shipping_last_name'  => $this->getShipping()->getLastName(),
				'shipping_company'    => $this->getShipping()->getCompany(),
				'shipping_address1'   => $this->getShipping()->getAddressLine1(),
				'shipping_address2'   => $this->getShipping()->getAddressLine2(),
				'shipping_city'       => $this->getShipping()->getCity(),
				'shipping_state'      => $this->getShipping()->getState(),
				'shipping_zip'        => $this->getShipping()->getZipCode(),
				'shipping_country'    => $this->getShipping()->getCountry(),
				'shipping_email'      => $this->getShipping()->getEmail()
			));
		}
		
		/**
		 * @param array $payload
		 *
		 * @return void
		 *
		 * @link https://github.com/thephpleague/omnipay
		 */
		private function process(array $payload): void {
			$gateway = new AuthNet\AIMGateway();
			$gateway->setDeveloperMode($this->config->isSandbox());
			$gateway->setApiLoginId($this->config->getApiLoginId());
			$gateway->setTransactionKey($this->config->getTransactionKey());
			
			/** @var AuthNet\Message\AIMResponse $response */
			$this->response    = $gateway->{$this->type}($payload)->send();
			$this->transaction = new AIM\Models\Transaction(json_decode(json_encode($this->response->getData()), TRUE));
		}
		
		/**
		 * @return bool
		 */
		public function isSandbox(): bool {
			return $this->config->getMode() == Config::DEVELOPMENT_MODE;
		}
		
		/**
		 * @param float|string $amount The numeric currency value.
		 *
		 * @return self
		 */
		private function setAmount(float|string $amount): self {
			$this->amount = is_string($amount) ? Helpers::UnformatCurrency($amount) : $amount;
			
			return $this;
		}
		
		/**
		 * @param bool $formatted Set true to return a USD formatted string.
		 *
		 * @return float|string
		 */
		public function getAmount(bool $formatted = FALSE): float|string {
			return !$formatted ? $this->amount : Helpers::FormatCurrency($this->amount);
		}
		
		/**
		 *
		 * @return \AuthorizeNet\AIM\Models\Billing
		 */
		public function getBilling(): AIM\Models\Billing {
			return $this->billing ??= new AIM\Models\Billing();
		}
		
		/**
		 * Set billing information.
		 *
		 * @param null|string $address_line_1
		 * @param null|string $address_line_2
		 * @param null|string $city
		 * @param null|string $company
		 * @param null|string $country
		 * @param null|string $email
		 * @param null|string $fax
		 * @param null|string $first_name
		 * @param null|string $last_name
		 * @param null|string $phone
		 * @param null|string $state
		 * @param null|string $website
		 * @param null|string $zip_code
		 *
		 * @return Client
		 *
		 */
		public function setBilling(?string $address_line_1, ?string $address_line_2, ?string $city, ?string $company, ?string $country, ?string $email, ?string $fax, ?string $first_name, ?string $last_name, ?string $phone, ?string $state, ?string $website, ?string $zip_code): Client {
			$this->billing = new AIM\Models\Billing(
				address_line_1 : filter_var($address_line_1),
				address_line_2 : filter_var($address_line_2),
				city           : filter_var($city),
				company        : filter_var($company),
				country        : filter_var($country),
				email          : filter_var($email, FILTER_VALIDATE_EMAIL),
				fax            : filter_var($fax),
				first_name     : filter_var($first_name),
				last_name      : filter_var($last_name),
				phone          : filter_var($phone),
				state          : filter_var($state),
				website        : filter_var($website, FILTER_VALIDATE_URL),
				zip_code       : filter_var($zip_code)
			);
			
			return $this;
		}
		
		/**
		 * Transaction captures flag existing authorizations for settlement. Only authorizations can be captured. Captures
		 * can be submitted for an amount equal to or less than the original authorization.
		 *
		 * @param float|string $amount Float representing the amount to be captured.
		 *
		 * @return void
		 *
		 * @throws Exception
		 */
		private function doCapture(float|string $amount): void {
			$this->setAmount($amount)->process(array(
				// Transaction Information
				'type'          => $this->getType(),
				'transactionid' => $this->getTransactionId(),
				'amount'        => $this->getAmount()
			));
		}
		
		/**
		 * @return string|null
		 */
		private function getTransactionId(): ?string {
			return $this->transaction_id;
		}
		
		/**
		 * Transaction credits apply an amount to the cardholder's card that was not originally processed through the
		 * Gateway. In most situations credits are disabled as transaction refunds should be used instead.
		 *
		 * @param float|string $amount Float representing the amount to be credited.
		 *
		 * @return void
		 *
		 * @throws Exception
		 *
		 * @noinspection PhpUnusedParameterInspection
		 */
		private function doCredit(float|string $amount): void {
			throw new Exception('Unsupported method.');
		}
		
		/**
		 * Transaction sales are submitted and immediately flagged for settlement.
		 *
		 * @param float|string $amount Float representing the amount to be processed.
		 *
		 * @return void
		 *
		 * @throws Exception
		 */
		private function doPurchase(float|string $amount): void {
			$this->setAmount($amount)->process(array(
				// Transaction
				'amount'        => $this->getAmount(),
				'transactionId' => $this->getInvoice(),
				'clientIp'      => $this->getOrder()->getIpAddress(),
				'description'   => $this->getOrder()->getDescription(),
				'memo'          => $this->getOrder()->getComments(),
				'card'          => new Common\CreditCard(array(
					// Contact
					'firstName'   => $this->getBilling()->getFirstName(),
					'lastName'    => $this->getBilling()->getLastName(),
					'company'     => $this->getBilling()->getCompany(),
					'address1'    => $this->getBilling()->getAddressLine1(),
					'address2'    => $this->getBilling()->getAddressLine2(),
					'city'        => $this->getBilling()->getCity(),
					'postcode'    => $this->getBilling()->getZipCode(),
					'state'       => $this->getBilling()->getState(),
					'country'     => $this->getBilling()->getCountry(),
					'phone'       => $this->getBilling()->getPhone(),
					'fax'         => $this->getBilling()->getFax(),
					'email'       => $this->getBilling()->getEmail(),
					
					// Credit Card
					'number'      => $this->getCreditCard()->getAccount(),
					'expiryMonth' => $this->getCreditCard()->getExpiration('m'),
					'expiryYear'  => $this->getCreditCard()->getExpiration('Y'),
					'cvv'         => $this->getCreditCard()->getCvv()
				))
			));
		}
		
		/**
		 * Generates an invoice number based on the parameters sent.
		 *
		 * @param string $prefix
		 * @param mixed  ...$details
		 *
		 * @return string
		 */
		public function getInvoice(string $prefix = 'AN', ...$details): string {
			return $this->invoice ??= sprintf("%s-%s", $prefix, substr(strtoupper(md5(implode($details) . time())), 0, 7));
		}
		
		/**
		 * Transaction refunds will reverse a previously settled or pending settlement transaction. If the transaction has not
		 * been settled, a transaction void can also reverse it.
		 *
		 * @param float|string $amount Float representing the amount to be refunded.
		 *
		 * @return void
		 *
		 * @throws Exception
		 */
		private function doRefund(float|string $amount): void {
			$this->setAmount($amount)->process(array(
				// Transaction Information
				'type'          => $this->getType(),
				'transactionid' => $this->getTransactionId(),
				'amount'        => $this->getAmount()
			));
		}
		
		/**
		 * Transaction updates can be used to update previous transactions with specific order information, such as a
		 * tracking number and shipping carrier.
		 *
		 * @return AuthNet\Message\AIMResponse
		 *
		 * @throws Exception
		 */
		private function doUpdate(): AuthNet\Message\AIMResponse {
			throw new Exception('Unsupported method.');
		}
		
		/**
		 * This action is used for doing an "Account Verification" on the cardholder's credit card without actually doing an
		 * authorization.
		 *
		 * @param float|string $amount Float representing the amount to be validated.
		 *
		 * @return AuthNet\Message\AIMResponse
		 *
		 * @throws Exception
		 *
		 * @noinspection PhpUnusedParameterInspection
		 */
		private function doValidate(float|string $amount): AuthNet\Message\AIMResponse {
			throw new Exception('Unsupported method.');
		}
		
		/**
		 * Transaction voids will cancel an existing sale or captured authorization. In addition, non-captured authorizations
		 * can be voided to prevent any future capture. Voids can only occur if the transaction has not been settled.
		 *
		 * @return AuthNet\Message\AIMResponse
		 *
		 * @throws Exception
		 */
		private function doVoid(): AuthNet\Message\AIMResponse {
			throw new Exception('Unsupported method.');
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
		 * @return Client
		 */
		public function setVerbose(bool $verbose): Client {
			$this->verbose = $verbose;
			return $this;
		}
		
		/**
		 * String representing the transaction ID from the original transaction.
		 *
		 * @param string|null $transaction_id
		 */
		public function setTransactionId(?string $transaction_id): void {
			$this->transaction_id = $transaction_id;
		}
		
		/**
		 * @return Config
		 */
		public function getConfig(): Config {
			return $this->config;
		}
	}