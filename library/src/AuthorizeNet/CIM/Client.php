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
	
	namespace AuthorizeNet\CIM;
	
	use Config\AuthorizeNet as Config;
	use DateTime;
	use Exception;
	use Freelancehunt\Validators;
	use Helpers;
	use Omnipay\AuthorizeNet as AuthNet;
	use Omnipay\AuthorizeNet\CIMGateway;
	use Omnipay\Common;
	use Omnipay\Common\CreditCard;
	
	/**
	 * AuthorizeNet CIM Client
	 *
	 * @Link: https://developer.authorize.net/api/reference/index.html#payment-transactions
	 */
	class Client {
		public const string MERCHANT_NAME      = 'AuthorizeNet';
		public const string MERCHANT_USERAGENT = 'AuthorizeNetCIMClient-PHP';
		
		public const string TYPE_AUTHORIZE   = 'authorize';
		public const string TYPE_CAPTURE     = 'capture';
		public const string TYPE_CREATE_CARD = 'createCard';
		public const string TYPE_CREDIT      = 'credit';
		public const string TYPE_PURCHASE    = 'purchase';
		public const string TYPE_REFUND      = 'refund';
		public const string TYPE_UPDATE      = 'update';
		public const string TYPE_VALIDATE    = 'validate';
		public const string TYPE_VOID        = 'void';
		
		private Models\Billing                   $billing;
		private Models\CreditCard                $credit_card;
		private Models\Order                     $order;
		private Models\Shipping                  $shipping;
		//private Models\Transaction               $transaction;
        private ?Models\Transaction              $transaction = null;

        private AuthNet\Message\CIMResponse      $response;
		private Common\Message\ResponseInterface $cim_response;
		
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
		 * @param string $mode "development" | "production"
		 *
		 * @throws Exception
		 */
		public function setMode(string $mode): void {
			$this->config = new Config($mode);
		}
		
		/**
		 * Set credit card to be used for transactions
		 *
		 * @param string $account    String representing the credit card account
		 * @param string $expiration String representing the credit card expiration
		 * @param string $cvv        String representing the credit card cvv
		 * @param string $type
		 *
		 * @return Client
		 *
		 * @throws \Exception
		 */
		public function setCreditCard(string $account, string $expiration, string $cvv, string $type): Client {
			$credit_card = self::ValidateCard(preg_replace('/\D/', '', $account));
			$expiration  = preg_replace('/\D/', '', $expiration);
			
			if(!$credit_card['valid']) throw new Exception('Invalid credit card number');
			
			$this->account_number = self::MaskCreditCard($credit_card['number']);
			$this->credit_card    = new Models\CreditCard(
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
		 * @return Models\Transaction
		 */
		//public function getTransaction(): Models\Transaction {
		//	return $this->transaction;
		//}

        public function getTransaction(): ?Models\Transaction {
            if ($this->transaction === null) {
                // Provide a dummy transaction to prevent fatals
                return new Models\Transaction([
                    'status' => 'uninitialized',
                    'message' => 'Transaction not initialized yet.'
                ]);
            }
            return $this->transaction;
        }
        public function hasTransaction(): bool {
            return $this->transaction !== null;
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
		 * @return Models\CreditCard
		 */
		private function getCreditCard(): Models\CreditCard {
			return $this->credit_card ??= new Models\CreditCard();
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
				self::TYPE_AUTHORIZE   => $this->doAuthorization($amount),
				self::TYPE_CAPTURE     => $this->doCapture($amount),
				self::TYPE_CREATE_CARD => $this->createCard(),
				self::TYPE_CREDIT      => $this->doCredit($amount),
				self::TYPE_PURCHASE    => $this->doPurchase($amount),
				self::TYPE_REFUND      => $this->doRefund($amount),
				self::TYPE_UPDATE      => $this->doUpdate(),
				self::TYPE_VALIDATE    => $this->doValidate(),
				self::TYPE_VOID        => $this->doVoid(),
				default                => throw new Exception('Unmatched transaction type. Please refer to documentation.'),
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
				'tax'                 => $this->getOrder()->getTax(),
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
		/*private function process(array $payload): void {
			$gateway = new CIMGateway();
			$gateway->setDeveloperMode($this->config->isSandbox());
			$gateway->setApiLoginId($this->config->getApiLoginId());
			$gateway->setTransactionKey($this->config->getTransactionKey());
			
			$this->cim_response = $gateway->{$this->type}($payload)->send();
			$this->transaction  = new Models\Transaction(json_decode(json_encode($this->cim_response->getData()), TRUE));
		} */

        private function process(array $payload): void {
            try {
                $gateway = new CIMGateway();
                $gateway->setDeveloperMode($this->config->isSandbox());
                $gateway->setApiLoginId($this->config->getApiLoginId());
                $gateway->setTransactionKey($this->config->getTransactionKey());

                $this->cim_response = $gateway->{$this->type}($payload)->send();
                $this->transaction  = new Models\Transaction(json_decode(json_encode($this->cim_response->getData()), TRUE));
            } catch (\Exception $e) {
                // Fall back to non-CIM (AIM) transaction if CIM not enabled
                if (str_contains($e->getMessage(), 'Customer Information Manager is not enabled')) {
                    $gateway = \Omnipay\Omnipay::create('AuthorizeNet_AIM');
                    $gateway->setApiLoginId($this->config->getApiLoginId());
                    $gateway->setTransactionKey($this->config->getTransactionKey());
                    $gateway->setTestMode($this->config->isSandbox());

                    // For AIM, we just run a simple purchase/authorize
                    $this->cim_response = $gateway->authorize([
                        'amount' => $payload['amount'] ?? 0.00,
                        'card'   => [
                            'number'      => $payload['ccnumber'] ?? '',
                            'expiryMonth' => substr($payload['ccexp'] ?? '', 0, 2),
                            'expiryYear'  => substr($payload['ccexp'] ?? '', -2),
                            'cvv'         => $payload['cvv'] ?? ''
                        ]
                    ])->send();

                    $this->transaction = new Models\Transaction(json_decode(json_encode($this->cim_response->getData()), true));
                } else {
                    throw $e;
                }
            }
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
		 * @return Models\Order
		 */
		public function getOrder(): Models\Order {
			return $this->order ??= new Models\Order();
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
		 * @param float       $tax
		 * @param float       $discount
		 * @param null|string $comments
		 * @param null|string $invoice
		 * @param null|string $customer_vault_id
		 * @param null|string $billing_id
		 *
		 * @return Client
		 */
		public function setOrder(float $amount, ?string $description, ?string $id, ?string $ip_address, ?string $po_number, float $shipping, float $tax, float $discount, ?string $comments, ?string $invoice, ?string $customer_vault_id, ?string $billing_id): Client {
			$this->invoice = $invoice;
			$this->order   = new Models\Order(
				amount            : filter_var($amount, FILTER_VALIDATE_FLOAT),
				description       : filter_var($description),
				id                : filter_var($id),
				ip_address        : filter_var($ip_address, FILTER_VALIDATE_IP),
				po_number         : filter_var($po_number),
				shipping          : filter_var($shipping, FILTER_VALIDATE_FLOAT),
				tax               : filter_var($tax, FILTER_VALIDATE_FLOAT),
				discount          : filter_var($discount, FILTER_VALIDATE_FLOAT),
				comments          : filter_var($comments),
				invoice           : filter_var($invoice),
				customer_vault_id : filter_var($customer_vault_id),
				billing_id        : filter_var($billing_id)
			);
			
			return $this;
		}
		
		/**
		 * @return Models\Shipping
		 */
		public function getShipping(): Models\Shipping {
			return $this->shipping ??= new Models\Shipping();
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
			$this->shipping = new Models\Shipping(
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
		 *
		 * @return Models\Billing
		 */
		public function getBilling(): Models\Billing {
			return $this->billing ??= new Models\Billing();
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
			$this->billing = new Models\Billing(
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
		 * @throws Exception
		 */
		private function createCard(): void {
			$gateway = new CIMGateway();
			$gateway->setApiLoginId($this->config->getApiLoginId());
			$gateway->setTransactionKey($this->config->getTransactionKey());
			$gateway->setTestMode($this->config->isSandbox());
			$gateway->setDeveloperMode($this->config->isSandbox());
			
			$cardData = array(
				'number'          => $this->getCreditCard()->getAccount(),
				'expiryMonth'     => $this->getCreditCard()->getExpiration('m'),
				'expiryYear'      => $this->getCreditCard()->getExpiration('Y'),
				'cvv'             => $this->getCreditCard()->getCvv(),
				'firstName'       => $this->getBilling()->getFirstName(),
				'lastName'        => $this->getBilling()->getLastName(),
				'billingAddress1' => $this->getBilling()->getAddressLine1(),
				'billingAddress2' => $this->getBilling()->getAddressLine2(),
				'city'            => $this->getBilling()->getCity(),
				'state'           => $this->getBilling()->getState(),
				'postcode'        => $this->getBilling()->getZipCode(),
				'country'         => $this->getBilling()->getCountry(),
				'email'           => $this->getBilling()->getEmail(),
			);
			
			$cim_response = $gateway->createCard(array(
				'customerId' => $this->getBilling()->getPhone(), // or phone, etc.
				'card'       => $cardData,
			))->send();
			
			$this->cim_response = $cim_response;
			
			if($cim_response->isSuccessful()) {
				$data    = $cim_response->getData();
				$profile = $data['paymentProfile'] ?? array();
				$card    = $profile['payment']['creditCard'] ?? array();
				// Handle expiration fallback cleanly
				$expiration_date = NULL;
				if(!empty($data['expiryYear']) && !empty($data['expiryMonth'])) {
					$expiration_date = sprintf('%04d-%02d-01', $data['expiryYear'], $data['expiryMonth']);
				} elseif(!empty($card['expirationDate']) && preg_match('/^(\d{4})-(\d{2})$/', $card['expirationDate'], $m)) {
					$expiration_date = sprintf('%04d-%02d-01', $m[1], $m[2]);
				}
				
				$this->transaction = new Models\Transaction(array(
					'customer_vault_id'   => $profile['customerProfileId'] ?? NULL,
					'customer_profile_id' => $profile['customerProfileId'] ?? NULL,
					'payment_profile_id'  => $profile['customerPaymentProfileId'] ?? NULL,
					'billing_id'          => $profile['customerPaymentProfileId'] ?? NULL,
					'account_number'      => $card['cardNumber'] ?? NULL,
					'account_type'        => strtolower($card['cardType'] ?? ''),
					'expiration_date'     => $expiration_date,
					'status'              => 'success',
					'raw'                 => $data,
					'response'            => $data['messages']['resultCode'] ?? NULL,
				));
			} else {
				$this->transaction = new Models\Transaction(array(
					'status'   => 'error',
					'message'  => $cim_response->getMessage(),
					'code'     => $cim_response->getCode(),
					'response' => $cim_response->getData()
				));
			}
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
				
				// Vault Reference (CIM)
				'cardReference' => $this->getCardReference()
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
		 * @return string
		 */
		public function getCardReference(): string {
			$customerId = (string)$this->getOrder()->getCustomerVaultId();
			$paymentId  = (string)$this->getOrder()->getBillingId();
			
			if(empty($customerId) || empty($paymentId)) {
				throw new \RuntimeException("Missing customer or payment profile ID for CIM cardReference.");
			}
			
			return json_encode([
				'customerProfileId'        => $customerId,
				'customerPaymentProfileId' => $paymentId,
			]);
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
		 * Update the stored payment profile in the CIM vault.
		 *
		 * @return void
		 * @throws Exception
		 */
		public function doUpdate(): void {
			$this->setType(self::TYPE_UPDATE);
			
			$gateway = new CIMGateway();
			$gateway->setApiLoginId($this->config->getApiLoginId());
			$gateway->setTransactionKey($this->config->getTransactionKey());
			$gateway->setTestMode($this->config->isSandbox());
			$gateway->setDeveloperMode($this->config->isSandbox());
			
			// Send the updateCard request
			$cim_response = $gateway->updateCard(array(
				'customerProfileId'        => $this->getOrder()->getCustomerVaultId(),
				'customerPaymentProfileId' => $this->getOrder()->getBillingId(),
				'card'                     => array(
					'number'      => $this->getCreditCard()->getAccount(),
					'expiryMonth' => $this->getCreditCard()->getExpiration('m'),
					'expiryYear'  => $this->getCreditCard()->getExpiration('Y'),
					'cvv'         => $this->getCreditCard()->getCvv(),
					'type'        => $this->getCreditCard()->getType(),
				),
				'validationMode'           => $this->config->isSandbox() ? 'testMode' : 'liveMode'
			))->send();
			
			$this->cim_response = $cim_response;
			
			if($cim_response->isSuccessful()) {
				// Optional: parse data
				$data    = $cim_response->getData();
				$profile = $data['paymentProfile'] ?? array();
				
				$this->transaction = new Models\Transaction(array(
					'customer_vault_id'   => $this->getOrder()->getCustomerVaultId(),
					'customer_profile_id' => $this->getOrder()->getCustomerVaultId(),
					'payment_profile_id'  => $this->getOrder()->getBillingId(),
					'billing_id'          => $this->getOrder()->getBillingId(),
					'account_number'      => $this->getCreditCard()->getAccount(),
					'account_type'        => $this->getCreditCard()->getType(),
					'expiration_date'     => $this->getCreditCard()->getExpiration('Y-m') . '-01',
					'status'              => 'success',
					'raw'                 => $data,
					'response'            => $data['messages']['resultCode'] ?? NULL,
				));
			}
		}
		
		/**
		 * Validate a customer's payment profile without charging.
		 *
		 * @return void
		 * @throws Exception
		 */
		public function doValidate(): void {
			$this->setType(self::TYPE_VALIDATE);
			
			$gateway = new CIMGateway();
			$gateway->setApiLoginId($this->config->getApiLoginId());
			$gateway->setTransactionKey($this->config->getTransactionKey());
			$gateway->setTestMode($this->config->isSandbox());
			$gateway->setDeveloperMode($this->config->isSandbox());
			
			// Send the updateCard request
			$cim_response = $gateway->updateCard(array(
				'customerProfileId'        => $this->getOrder()->getCustomerVaultId(),
				'customerPaymentProfileId' => $this->getOrder()->getBillingId(),
				'card'                     => array(
					'number'      => $this->getCreditCard()->getAccount(),
					'expiryMonth' => $this->getCreditCard()->getExpiration('m'),
					'expiryYear'  => $this->getCreditCard()->getExpiration('Y'),
					'cvv'         => $this->getCreditCard()->getCvv(),
					'type'        => $this->getCreditCard()->getType(),
				),
				'validationMode'           => $this->config->isSandbox() ? 'testMode' : 'liveMode'
			))->send();
			
			$this->cim_response = $cim_response;
			
			if($cim_response->isSuccessful()) {
				// Optional: parse data
				$data    = $cim_response->getData();
				
				$this->transaction = new Models\Transaction(array(
					'customer_vault_id'   => $this->getOrder()->getCustomerVaultId(),
					'customer_profile_id' => $this->getOrder()->getCustomerVaultId(),
					'payment_profile_id'  => $this->getOrder()->getBillingId(),
					'billing_id'          => $this->getOrder()->getBillingId(),
					'account_number'      => $this->getCreditCard()->getAccount(),
					'account_type'        => $this->getCreditCard()->getType(),
					'expiration_date'     => $this->getCreditCard()->getExpiration('Y-m') . '-01',
					'status'              => 'success',
					'raw'                 => $data,
					'response'            => $data['messages']['resultCode'] ?? NULL,
				));
			}
		}
		
		/**
		 * Transaction voids will cancel an existing sale or captured authorization. In addition, non-captured authorizations
		 * can be voided to prevent any future capture. Voids can only occur if the transaction has not been settled.
		 *
		 * @return AuthNet\Message\CIMResponse
		 *
		 * @throws Exception
		 */
		private function doVoid(): AuthNet\Message\CIMResponse {
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
		
		/**
		 * @return AuthNet\Message\CIMResponse
		 */
		public function getResponse(): AuthNet\Message\CIMResponse {
			return $this->response;
		}
		
		/**
		 * @return \Omnipay\Common\Message\ResponseInterface
		 */
		public function getCimResponse(): Common\Message\ResponseInterface {
			return $this->cim_response;
		}
	}