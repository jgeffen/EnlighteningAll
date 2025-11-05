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
	
	namespace Items;
	
	use Database;
	use DateTime;
	use Helpers;
	use Items\Interfaces\Item;
	use MobiusPay;
	use PDO;
	use PDOStatement;
	
	class Transaction implements Item {
		use Traits\Item;
		protected string             $merchant;
		protected string             $form;
		protected string             $type;
		protected string             $payment_status;
		protected ?float             $amount;
		protected string             $account_number;
		protected string             $account_type;
		protected ?string            $table_name;
		protected ?int               $table_id;
		protected ?int               $billing_id;
		protected ?string            $billing_address_line_1;
		protected ?string            $billing_address_line_2;
		protected ?string            $billing_city;
		protected ?string            $billing_company;
		protected ?string            $billing_country;
		protected ?string            $billing_email;
		protected ?string            $billing_fax;
		protected ?string            $billing_first_name;
		protected ?string            $billing_last_name;
		protected ?string            $billing_phone;
		protected ?string            $billing_state;
		protected null|int|string    $billing_zip_code;
		protected ?string            $captcha;
		protected ?string            $comments;
		protected ?int               $customer_vault_id;
		protected ?string            $expiration_date;
		protected ?string            $invoice;
		protected ?int               $member_id;
		protected ?string            $name_on_pass;
		protected ?string            $ref_transaction_id;
		protected ?string            $response_code;
		protected ?string            $response_text;
		protected ?string            $response;
		protected ?float             $sales_tax;
		protected ?int               $shipping_id;
		protected ?string            $shipping_address_line_1;
		protected ?string            $shipping_address_line_2;
		protected ?string            $shipping_city;
		protected ?string            $shipping_company;
		protected ?string            $shipping_country;
		protected ?string            $shipping_email;
		protected ?string            $shipping_first_name;
		protected ?string            $shipping_last_name;
		protected ?string            $shipping_state;
		protected null|int|string    $shipping_zip_code;
		protected ?string            $shipping_phone;
		protected ?string            $shipping_fax;
		protected ?string            $transaction_id;
		protected ?string            $customer_profile_id;
		protected ?string            $payment_profile_id;
		protected ?string            $product_quantity;
		protected ?string            $product_id;
		protected ?string            $name;
		protected ?string            $product_label;
		protected ?string            $avs_response;
		protected ?string            $cvv_response;
		protected ?string            $gateway_debug;
		protected ?string            $discount;
		private ?Members\Reservation $member_reservation;
		private array                $ref_transactions;
		
		// Mobius Pay
		private ?string $MOBIUSPAY_AUTHCODE;
		private ?string $MOBIUSPAY_AVSRESPONSE;
		private ?string $MOBIUSPAY_BILLING_ID;
		private ?string $MOBIUSPAY_CUSTOMER_VAULT_ID;
		private ?string $MOBIUSPAY_CVVRESPONSE;
		private ?string $MOBIUSPAY_EMV_AUTH_RESPONSE_DATA;
		private ?string $MOBIUSPAY_ORDERID;
		private ?string $MOBIUSPAY_RESPONSE_CODE;
		private ?string $MOBIUSPAY_RESPONSE;
		private ?string $MOBIUSPAY_RESPONSETEXT;
		private ?string $MOBIUSPAY_SHIPPING_ID;
		private ?string $MOBIUSPAY_TRANSACTIONID;
		private ?string $MOBIUSPAY_TYPE;
		private ?string $MOBIUSPAY_DATE;
		private ?string $MOBIUSPAY_CUSTOMER_VAULT;
		private ?string $MOBIUSPAY_CC_TYPE;
		private ?string $MOBIUSPAY_CC_NUMBER;
		private ?string $MOBIUSPAY_CC_EXP;
		private ?string $MOBIUSPAY_AMOUNT_AUTHORIZED;
		
		// Authnet AIM
		private ?string $AUTHORIZENET_TRANSACTIONID;
		private ?string $AUTHORIZENET_ACCOUNT_TYPE;
		private ?string $AUTHORIZENET_ACCOUNT_NUMBER;
		private ?string $AUTHORIZENET_RESPONSETEXT;
		private ?string $AUTHORIZENET_RESPONSE_CODE;
		private ?string $AUTHORIZENET_RESPONSE;
		private ?string $AUTHORIZENET_CUSTOMER_VAULT_ID;
		private ?string $AUTHORIZENET_BILLING_ID;
		
		private ?string $AUTHORIZENET_TRANS_ID;
		private ?string $AUTHORIZENET_TRANS_HASH_SHA2;
		private ?string $AUTHORIZENET_TRANS_HASH;
		private ?string $AUTHORIZENET_TEST_REQUEST;
		private ?string $AUTHORIZENET_SUPPLEMENTAL_DATA_QUALIFICATION_INDICATOR;
		private ?string $AUTHORIZENET_REF_TRANS_ID;
		private ?string $AUTHORIZENET_NETWORK_TRANS_ID;
		private ?string $AUTHORIZENET_MESSAGES;
		private ?string $AUTHORIZENET_DATA;
		private ?string $AUTHORIZENET_CVV_RESULT_CODE;
		private ?string $AUTHORIZENET_CAVV_RESULT_CODE;
		private ?string $AUTHORIZENET_AVS_RESULT_CODE;
		private ?string $AUTHORIZENET_AUTH_CODE;
		
		// Authnet CIM
		private ?string $AUTHORIZENETCIM_RESPONSETEXT;
		private ?string $AUTHORIZENETCIM_RESPONSE_CODE;
		private ?string $AUTHORIZENETCIM_RESPONSE;
		private ?string $AUTHORIZENETCIM_CUSTOMER_VAULT_ID;
		private ?string $AUTHORIZENETCIM_PAYMENT_PROFILE_ID;
		private ?string $AUTHORIZENETCIM_CUSTOMER_PROFILE_ID;
		private ?string $AUTHORIZENETCIM_BILLING_ID;
		private ?string $AUTHORIZENETCIM_TRANSACTIONID;
		private ?string $AUTHORIZENETCIM_ACCOUNT_TYPE;
		private ?string $AUTHORIZENETCIM_ACCOUNT_NUMBER;
		
		private ?string $AUTHORIZENETCIM_AMOUNT;
		private ?string $default;
		private ?string $AUTHORIZENET_STATUS;
		private ?string $AUTHORIZENET_PAYMENT_PROFILE_ID;
		private ?string $AUTHORIZENET_EXPIRATION_DATE;
		private ?string $AUTHORIZENET_CUSTOMER_PROFILE_ID;
		private ?int    $staff_id;
		private bool    $is_tip;
		private ?string $referrer_id;
		private ?string $quantity;
		private ?string $product;
		private ?string $member_name;
		private ?string $points_used;
		private ?bool   $paid_out;
		
		/**
		 * @return string
		 */
		public function getMerchant(): string {
			return $this->merchant;
		}
		
		/**
		 * @return string
		 */
		public function getForm(): string {
			return $this->form;
		}
		
		/**
		 * @return string
		 */
		public function getPaymentStatus(): string {
			return $this->payment_status;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return null|float|string
		 */
		public function getAmount(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string|null {
			return !$format || is_null($this->amount) ? $this->amount : Helpers::FormatCurrency($this->amount, $currency, $locale);
		}
		
		/**
		 * @return null|string
		 */
		public function getProductQuantity(): ?string {
			return $this->product_quantity;
		}
		
		/**
		 * @return null|string
		 */
		public function getProductId(): ?string {
			return $this->product_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getName(): ?string {
			return $this->name;
		}
		
		/**
		 * @return null|string
		 */
		public function getAvsResponse(): ?string {
			return $this->avs_response;
		}
		
		/**
		 * @return null|string
		 */
		public function getCvvResponse(): ?string {
			return $this->cvv_response;
		}
		
		/**
		 * @return null|string
		 */
		public function getGatewayDebug(): ?string {
			return $this->gateway_debug;
		}
		
		/**
		 * @return null|string
		 */
		public function getDiscount(): ?string {
			return $this->discount;
		}
		
		/**
		 * @return null|string
		 */
		public function getProductLabel(): ?string {
			return $this->product_label;
		}
		
		/**
		 * @return string
		 */
		public function getAccountNumber(): string {
			return $this->account_number;
		}
		
		/**
		 * @return string
		 */
		public function getAccountType(): string {
			return $this->account_type;
		}
		
		/**
		 * @return null|string
		 */
		public function getTableName(): ?string {
			return $this->table_name;
		}
		
		/**
		 * @return null|int
		 */
		public function getTableId(): ?int {
			return $this->table_id;
		}
		
		/**
		 * @return ?int
		 */
		public function getBillingId(): ?int {
			return $this->billing_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingAddressLine1(): ?string {
			return $this->billing_address_line_1;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingAddressLine2(): ?string {
			return $this->billing_address_line_2;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingCity(): ?string {
			return $this->billing_city;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingCompany(): ?string {
			return $this->billing_company;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingCountry(): ?string {
			return $this->billing_country;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingEmail(): ?string {
			return $this->billing_email;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingFax(): ?string {
			return $this->billing_fax;
		}
		
		/**
		 * @return string
		 */
		public function getBillingName(): string {
			return sprintf("%s %s", $this->getBillingFirstName(), $this->getBillingLastName());
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingFirstName(): ?string {
			return $this->billing_first_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingLastName(): ?string {
			return $this->billing_last_name;
		}
		
		/**
		 * @return string
		 */
		public function getBillingNameLast(): string {
			return sprintf("%s, %s", $this->getBillingLastName(), $this->getBillingFirstName());
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingPhone(): ?string {
			return $this->billing_phone;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingState(): ?string {
			return $this->billing_state;
		}
		
		/**
		 * @return null|int|string
		 */
		public function getBillingZipCode(): null|int|string {
			return $this->billing_zip_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getCAPTCHA(): ?string {
			return $this->captcha;
		}
		
		/**
		 * @return null|string
		 */
		public function getComments(): ?string {
			return $this->comments;
		}
		
		/**
		 * @return null|Members\Wallet
		 */
		public function getCustomerVault(): ?Members\Wallet {
			return Members\Wallet::Init($this->getCustomerVaultId());
		}
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `transactions` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @return null|int
		 */
		public function getCustomerVaultId(): ?int {
			return $this->customer_vault_id;
		}
		
		/**
		 * @return ?DateTime
		 */
		public function getExpirationDate(): ?DateTime {
			return date_create($this->expiration_date);
		}
		
		/**
		 * @return null|string
		 */
		public function getInvoice(): ?string {
			return $this->invoice;
		}
		
		/**
		 * @return null|Member
		 */
		public function getMember(): ?Member {
			return Member::Init($this->getMemberId());
		}
		
		/**
		 * @return null|int
		 */
		public function getMemberId(): ?int {
			return $this->member_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getNameOnPass(): ?string {
			return $this->name_on_pass;
		}
		
		/**
		 * @return null|string
		 */
		public function getRefTransactionId(): ?string {
			return $this->ref_transaction_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getResponseCode(): ?string {
			return $this->response_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getResponseText(): ?string {
			return $this->response_text;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return null|float|string
		 */
		public function getSalesTax(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string|null {
			return !$format || is_null($this->sales_tax) ? $this->sales_tax : Helpers::FormatCurrency($this->sales_tax, $currency, $locale);
		}
		
		/**
		 * @return null|int
		 */
		public function getShippingId(): ?int {
			return $this->shipping_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingAddressLine1(): ?string {
			return $this->shipping_address_line_1;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingAddressLine2(): ?string {
			return $this->shipping_address_line_2;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingCity(): ?string {
			return $this->shipping_city;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingCompany(): ?string {
			return $this->shipping_company;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingCountry(): ?string {
			return $this->shipping_country;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingEmail(): ?string {
			return $this->shipping_email;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingFirstName(): ?string {
			return $this->shipping_first_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingLastName(): ?string {
			return $this->shipping_last_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingPhone(): ?string {
			return $this->shipping_phone;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingFax(): ?string {
			return $this->shipping_fax;
		}
		
		/**
		 * @return null|string
		 */
		public function getShippingState(): ?string {
			return $this->shipping_state;
		}
		
		/**
		 * @return null|int|string
		 */
		public function getShippingZipCode(): null|int|string {
			return $this->shipping_zip_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayAuthCode(): ?string {
			return $this->MOBIUSPAY_AUTHCODE;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayAvsResponse(): ?string {
			return $this->MOBIUSPAY_AVSRESPONSE;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayBillingId(): ?string {
			return $this->MOBIUSPAY_BILLING_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayCustomerVaultId(): ?string {
			return $this->MOBIUSPAY_CUSTOMER_VAULT_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayCvvResponse(): ?string {
			return $this->MOBIUSPAY_CVVRESPONSE;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayEmvAuthResponseData(): ?string {
			return $this->MOBIUSPAY_EMV_AUTH_RESPONSE_DATA;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayOrderId(): ?string {
			return $this->MOBIUSPAY_ORDERID;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayResponseCode(): ?string {
			return $this->MOBIUSPAY_RESPONSE_CODE;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayResponse(): ?string {
			return $this->MOBIUSPAY_RESPONSE;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayResponseText(): ?string {
			return $this->MOBIUSPAY_RESPONSETEXT;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayShippingId(): ?string {
			return $this->MOBIUSPAY_SHIPPING_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayTransactionId(): ?string {
			return $this->MOBIUSPAY_TRANSACTIONID;
		}
		
		/**
		 * @return null|string
		 */
		public function getMOBIUSPAYDATE(): ?string {
			return $this->MOBIUSPAY_DATE;
		}
		
		/**
		 * @return null|string
		 */
		public function getMOBIUSPAYCUSTOMERVAULT(): ?string {
			return $this->MOBIUSPAY_CUSTOMER_VAULT;
		}
		
		/**
		 * @return null|string
		 */
		public function getMOBIUSPAYCCTYPE(): ?string {
			return $this->MOBIUSPAY_CC_TYPE;
		}
		
		/**
		 * @return null|string
		 */
		public function getMOBIUSPAYCCNUMBER(): ?string {
			return $this->MOBIUSPAY_CC_NUMBER;
		}
		
		/**
		 * @return null|string
		 */
		public function getMOBIUSPAYCCEXP(): ?string {
			return $this->MOBIUSPAY_CC_EXP;
		}
		
		/**
		 * @return null|string
		 */
		public function getMOBIUSPAYAMOUNTAUTHORIZED(): ?string {
			return $this->MOBIUSPAY_AMOUNT_AUTHORIZED;
		}
		
		/**
		 * @return null|string
		 */
		public function getMobiuspayType(): ?string {
			return $this->MOBIUSPAY_TYPE;
		}
		
		/**
		 * @return bool
		 */
		public function isCaptured(): bool {
			return !empty(array_filter($this->getRefTransactions(), function(Transaction $transaction) {
				return in_array($this->getType(), array('auth')) && in_array($transaction->getType(), array('capture'))
				       && in_array($transaction->getResponse(), array(MobiusPay\Client::RESPONSE_APPROVED));
			}));
		}
		
		/**
		 * @return Transaction[]
		 */
		public function getRefTransactions(): array {
			return $this->ref_transactions ??= Database::Action("SELECT * FROM `transactions` WHERE `ref_transaction_id` = :ref_transaction_id ORDER BY `timestamp` DESC", array(
				'ref_transaction_id' => $this->getTransactionId()
			))->fetchAll(PDO::FETCH_CLASS, self::class);
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return self[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
		}
		
		/**
		 * @return null|string
		 */
		public function getTransactionId(): ?string {
			return $this->transaction_id;
		}
		
		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}
		
		/**
		 * @return null|string
		 */
		public function getResponse(): ?string {
			return $this->response;
		}
		
		/**
		 * @return bool
		 */
		public function isError(): bool {
			return in_array($this->getResponse(), array(MobiusPay\Client::RESPONSE_DECLINED, MobiusPay\Client::RESPONSE_ERROR));
		}
		
		/**
		 * @return bool
		 */
		public function isPending(): bool {
			return in_array($this->getType(), array('auth'));
		}
		
		/**
		 * @return bool
		 */
		public function isRefunded(): bool {
			return !empty(array_filter($this->getRefTransactions(), function(Transaction $transaction) {
				return in_array($this->getType(), array('capture', 'sale')) && str_contains($transaction->getType(), 'refund')
				       && in_array($transaction->getResponse(), array(MobiusPay\Client::RESPONSE_APPROVED));
			}));
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETTRANSACTIONID(): ?string {
			return $this->AUTHORIZENET_TRANSACTIONID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETACCOUNTTYPE(): ?string {
			return $this->AUTHORIZENET_ACCOUNT_TYPE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETACCOUNTNUMBER(): ?string {
			return $this->AUTHORIZENET_ACCOUNT_NUMBER;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETRESPONSETEXT(): ?string {
			return $this->AUTHORIZENET_RESPONSETEXT;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETRESPONSECODE(): ?string {
			return $this->AUTHORIZENET_RESPONSE_CODE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETRESPONSE(): ?string {
			return $this->AUTHORIZENET_RESPONSE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCUSTOMERVAULTID(): ?string {
			return $this->AUTHORIZENET_CUSTOMER_VAULT_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETBILLINGID(): ?string {
			return $this->AUTHORIZENET_BILLING_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETTRANSID(): ?string {
			return $this->AUTHORIZENET_TRANS_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETTRANSHASHSHA2(): ?string {
			return $this->AUTHORIZENET_TRANS_HASH_SHA2;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETTRANSHASH(): ?string {
			return $this->AUTHORIZENET_TRANS_HASH;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETTESTREQUEST(): ?string {
			return $this->AUTHORIZENET_TEST_REQUEST;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETSUPPLEMENTALDATAQUALIFICATIONINDICATOR(): ?string {
			return $this->AUTHORIZENET_SUPPLEMENTAL_DATA_QUALIFICATION_INDICATOR;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETREFTRANSID(): ?string {
			return $this->AUTHORIZENET_REF_TRANS_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETNETWORKTRANSID(): ?string {
			return $this->AUTHORIZENET_NETWORK_TRANS_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETMESSAGES(): ?string {
			return $this->AUTHORIZENET_MESSAGES;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETDATA(): ?string {
			return $this->AUTHORIZENET_DATA;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCVVRESULTCODE(): ?string {
			return $this->AUTHORIZENET_CVV_RESULT_CODE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCAVVRESULTCODE(): ?string {
			return $this->AUTHORIZENET_CAVV_RESULT_CODE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETAVSRESULTCODE(): ?string {
			return $this->AUTHORIZENET_AVS_RESULT_CODE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETAUTHCODE(): ?string {
			return $this->AUTHORIZENET_AUTH_CODE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMRESPONSETEXT(): ?string {
			return $this->AUTHORIZENETCIM_RESPONSETEXT;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMRESPONSECODE(): ?string {
			return $this->AUTHORIZENETCIM_RESPONSE_CODE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMRESPONSE(): ?string {
			return $this->AUTHORIZENETCIM_RESPONSE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMCUSTOMERVAULTID(): ?string {
			return $this->AUTHORIZENETCIM_CUSTOMER_VAULT_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMBILLINGID(): ?string {
			return $this->AUTHORIZENETCIM_BILLING_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMPAYMENTPROFILEID(): ?string {
			return $this->AUTHORIZENETCIM_PAYMENT_PROFILE_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMCUSTOMERPROFILEID(): ?string {
			return $this->AUTHORIZENETCIM_CUSTOMER_PROFILE_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMTRANSACTIONID(): ?string {
			return $this->AUTHORIZENETCIM_TRANSACTIONID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMACCOUNTTYPE(): ?string {
			return $this->AUTHORIZENETCIM_ACCOUNT_TYPE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCIMACCOUNTNUMBER(): ?string {
			return $this->AUTHORIZENETCIM_ACCOUNT_NUMBER;
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
		public function getAUTHORIZENETCIMAMOUNT(): ?string {
			return $this->AUTHORIZENETCIM_AMOUNT;
		}
		
		/**
		 * @return null|string
		 */
		public function getDefault(): ?string {
			return $this->default;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETSTATUS(): ?string {
			return $this->AUTHORIZENET_STATUS;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETPAYMENTPROFILEID(): ?string {
			return $this->AUTHORIZENET_PAYMENT_PROFILE_ID;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETEXPIRATIONDATE(): ?string {
			return $this->AUTHORIZENET_EXPIRATION_DATE;
		}
		
		/**
		 * @return null|string
		 */
		public function getAUTHORIZENETCUSTOMERPROFILEID(): ?string {
			return $this->AUTHORIZENET_CUSTOMER_PROFILE_ID;
		}
		
		/**
		 * @return null|int
		 */
		public function getStaffId(): ?int {
			return $this->staff_id;
		}
		
		/**
		 * @return bool
		 */
		public function isTip(): bool {
			return $this->is_tip;
		}
		
		/**
		 * @return null|string
		 */
		public function getReferrerId(): ?string {
			return $this->referrer_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getQuantity(): ?string {
			return $this->quantity;
		}
		
		/**
		 * @return null|string
		 */
		public function getProduct(): ?string {
			return $this->product;
		}
		
		/**
		 * @return null|string
		 */
		public function getMemberName(): ?string {
			return $this->member_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getPointsUsed(): ?string {
			return $this->points_used;
		}
		
		public function isPaidOut(): ?bool {
			return $this->paid_out;
		}
		
		/**
		 * @return bool
		 */
		public function isVoided(): bool {
			return !empty(array_filter($this->getRefTransactions(), function($transaction) {
				return in_array($this->getType(), array('auth')) && str_contains($transaction->getType(), 'void')
				       && in_array($transaction->getResponse(), array(MobiusPay\Client::RESPONSE_APPROVED));
			}));
		}
		
		/**
		 * @return null|Members\Reservation
		 */
		public function getMemberReservation(): ?Members\Reservation {
			return $this->member_reservation ??= Members\Reservation::Fetch(Database::Action("SELECT * FROM `member_reservations` WHERE `transaction_id` = :transaction_id ORDER BY `item_count` LIMIT 1", array(
				'transaction_id' => $this->getId()
			)));
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|static
		 */
		public static function Fetch(PDOStatement $statement): ?self {
			return $statement->fetchObject(self::class) ?: NULL;
		}
	}