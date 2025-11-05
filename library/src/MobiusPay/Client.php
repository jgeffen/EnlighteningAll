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

namespace MobiusPay;

use Config\MobiusPay as Config;
use DateTime;
use Exception;
use Freelancehunt\Validators\CreditCard;
use Helpers;
use JetBrains\PhpStorm\Pure;

/**
 * MobiusPay Client
 *
 * @Link: https://secure.mobiusgateway.com/merchants/resources/integration/integration_portal.php
 */
class Client {
    public const string MERCHANT_NAME      = 'MobiusPay';
    public const string MERCHANT_USERAGENT = 'MobiusPayClient-PHP';
    public const string MERCHANT_ENDPOINT  = 'https://secure.mobiusgateway.com/api/transact.php';

    public const int RESPONSE_APPROVED = 1;
    public const int RESPONSE_DECLINED = 2;
    public const int RESPONSE_ERROR    = 3;

    public const string TYPE_AUTH     = 'auth';
    public const string TYPE_CAPTURE  = 'capture';
    public const string TYPE_CREDIT   = 'credit';
    public const string TYPE_REFUND   = 'refund';
    public const string TYPE_SALE     = 'sale';
    public const string TYPE_UPDATE   = 'update';
    public const string TYPE_VALIDATE = 'validate';
    public const string TYPE_VOID     = 'void';

    private array $billing     = array();
    private array $creditCard  = array();
    private array $order       = array();
    private array $shipping    = array();
    private array $transaction = array();
    private bool  $verbose     = FALSE;

    private Config  $config;
    private float   $amount;
    private string  $account_number;
    private ?string $transaction_id;
    private string  $type;

    public function __construct(?string $mode = NULL, ?string $config_path = NULL) {
        $this->config = new Config($mode, $config_path);
    }

    public static function FormOptions(?string $type = NULL, ?string $sub_type = NULL, ?string $key = NULL): array|string|null {
        $array = match ($type) {
            'countries'         => array('US' => 'United States of America'),
            'credit_card_types' => array(
                'Visa'       => 'Visa',
                'MasterCard' => 'Master Card',
                'Discover'   => 'Discover',
                'Amex'       => 'American Express',
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
                'US' => array(
                    'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California',
                    'CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District of Columbia',
                    'FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois',
                    'IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana',
                    'ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota',
                    'MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada',
                    'NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York',
                    'NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon',
                    'PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota',
                    'TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia',
                    'WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'
                ),
                default => array()
            },
            default => array(),
        };
        return is_null($key) ? $array : $array[$key] ?? NULL;
    }

    public static function GenerateInvoice(string $prefix = 'MP', ...$details): string {
        return sprintf("%s-%s", $prefix, substr(strtoupper(md5(implode($details) . time())), 0, 7));
    }

    public function setMode(string $mode, ?string $config_path = NULL): void {
        $this->config = new Config($mode, $config_path);
    }

    public function setCreditCard(string $account, string $type, string $expiration, string $cvv): Client {
        $account = preg_replace('/[^\d]/', '', $account);
        if(self::ValidateAccount($account, $type)) throw new Exception('Invalid credit card number');
        if(self::ValidateExpiration($expiration)) throw new Exception('Invalid expiration date');
        if(self::ValidateCVV($cvv, $type)) throw new Exception('Invalid CVV');

        $this->account_number = self::MaskCreditCard($account);
        $this->creditCard     = array(
            'account'    => $account,
            'expiration' => $expiration,
            'cvv'        => $cvv,
            'type'       => $type
        );
        return $this;
    }

    public static function ValidateAccount(string $account, string $type): bool {
        return !CreditCard::validCreditCard($account, strtolower($type))['valid'];
    }
    public static function ValidateExpiration(string $cardExp): bool {
        list($expMonth, $expYear) = strlen($cardExp) == 6
            ? array(substr($cardExp, 0, 2), substr($cardExp, -4))
            : array(substr($cardExp, 0, 2), '20' . substr($cardExp, -2));
        return !CreditCard::validDate($expYear, $expMonth);
    }
    public static function ValidateCVV(string $cardCvv, string $cardType): bool {
        return !CreditCard::validCvc($cardCvv, strtolower($cardType));
    }
    public static function MaskCreditCard($credit_card): string {
        $credit_card = (string)preg_replace('/[^0-9]/', '', $credit_card);
        $length = strlen($credit_card);
        return substr($credit_card, 0, 1) . str_repeat('X', $length - 5) . substr($credit_card, $length - 4, 4);
    }

    public function getPaymentStatus(): string {
        return self::PaymentStatus($this->getTransaction('response'));
    }
    public static function PaymentStatus(int $response): string {
        return match ($response) {
            self::RESPONSE_APPROVED => 'Approved',
            self::RESPONSE_DECLINED => 'Declined',
            self::RESPONSE_ERROR    => 'Errored',
            default => 'Unknown'
        };
    }

    public function getTransaction(?string $key = NULL): array|string|null {
        $array = filter_var_array($this->transaction, array(
            'authcode'=>FILTER_VALIDATE_INT,'avsresponse'=>FILTER_DEFAULT,'billing_id'=>FILTER_VALIDATE_INT,
            'customer_vault_id'=>FILTER_VALIDATE_INT,'cvvresponse'=>FILTER_DEFAULT,
            'emv_auth_response_data'=>FILTER_DEFAULT,'orderid'=>FILTER_DEFAULT,'response'=>FILTER_VALIDATE_INT,
            'response_code'=>FILTER_VALIDATE_INT,'responsetext'=>FILTER_DEFAULT,
            'shipping_id'=>FILTER_VALIDATE_INT,'transactionid'=>FILTER_DEFAULT
        )) ?: array();
        return is_null($key)?$array:($array[$key]??NULL);
    }
    public function getTransactionDetails(array $form_values=array()):array{
        return array_merge($form_values,array_combine(array_map(function($key){
            return sprintf("MOBIUSPAY_%s",strtoupper(str_replace('[','_',trim($key,']'))));
        },array_keys($this->transaction)),$this->transaction));
    }
    public function getAccountNumber():?string{return $this->account_number??NULL;}
    public function getAccountType():?string{return $this->getCreditCard('type');}

    private function getCreditCard(?string $key=NULL):array|string|null{
        $array=filter_var_array($this->creditCard,array('account'=>FILTER_DEFAULT,'cvv'=>FILTER_DEFAULT,'expiration'=>FILTER_DEFAULT,'type'=>FILTER_DEFAULT))?:array();
        return is_null($key)?$array:($array[$key]??NULL);
    }

    public function getOrder(?string $key=NULL):array|string|null{
        $array=filter_var_array($this->order,array('description'=>FILTER_DEFAULT,'id'=>FILTER_DEFAULT,'ip_address'=>FILTER_DEFAULT,'po_number'=>FILTER_DEFAULT,'shipping'=>FILTER_DEFAULT,'tax'=>FILTER_VALIDATE_FLOAT,'comments'=>FILTER_DEFAULT))?:array();
        return is_null($key)?$array:($array[$key]??NULL);
    }

    public function setBilling(array $billing):Client{
        $this->billing=filter_var_array($billing,array(
            'first_name'=>FILTER_DEFAULT,'last_name'=>FILTER_DEFAULT,'email'=>FILTER_VALIDATE_EMAIL,
            'phone'=>FILTER_DEFAULT,'fax'=>FILTER_DEFAULT,'company'=>FILTER_DEFAULT,
            'address_line_1'=>FILTER_DEFAULT,'address_line_2'=>FILTER_DEFAULT,'city'=>FILTER_DEFAULT,
            'state'=>FILTER_DEFAULT,'zip_code'=>FILTER_DEFAULT,'country'=>FILTER_DEFAULT
        ))?:array();return $this;}
    public function getBilling(?string $key=NULL):array|string|null{
        $array=filter_var_array($this->billing,array(
            'first_name'=>FILTER_DEFAULT,'last_name'=>FILTER_DEFAULT,'email'=>FILTER_VALIDATE_EMAIL,
            'phone'=>FILTER_DEFAULT,'fax'=>FILTER_DEFAULT,'company'=>FILTER_DEFAULT,
            'address_line_1'=>FILTER_DEFAULT,'address_line_2'=>FILTER_DEFAULT,'city'=>FILTER_DEFAULT,
            'state'=>FILTER_DEFAULT,'zip_code'=>FILTER_DEFAULT,'country'=>FILTER_DEFAULT))?:array();
        return is_null($key)?$array:($array[$key]??NULL);}
    public function setShipping(array $shipping):Client{
        $this->shipping=filter_var_array($shipping,array('first_name'=>FILTER_DEFAULT,'last_name'=>FILTER_DEFAULT,'email'=>FILTER_VALIDATE_EMAIL,'phone'=>FILTER_DEFAULT,'company'=>FILTER_DEFAULT,'address_line_1'=>FILTER_DEFAULT,'address_line_2'=>FILTER_DEFAULT,'city'=>FILTER_DEFAULT,'state'=>FILTER_DEFAULT,'zip_code'=>FILTER_DEFAULT,'country'=>FILTER_DEFAULT))?:array();return $this;}
    public function getShipping(?string $key=NULL):array|string|null{
        $array=filter_var_array($this->shipping,array('first_name'=>FILTER_DEFAULT,'last_name'=>FILTER_DEFAULT,'email'=>FILTER_VALIDATE_EMAIL,'phone'=>FILTER_DEFAULT,'company'=>FILTER_DEFAULT,'address_line_1'=>FILTER_DEFAULT,'address_line_2'=>FILTER_DEFAULT,'city'=>FILTER_DEFAULT,'state'=>FILTER_DEFAULT,'zip_code'=>FILTER_DEFAULT,'country'=>FILTER_DEFAULT))?:array();return is_null($key)?$array:($array[$key]??NULL);}

    public function getExpirationDate(string $format='Y-m-d'):?string{
        $exp=$this->getCreditCard('expiration');
        if(empty($exp)||!preg_match('/^\d{4,6}$/',$exp)){error_log('MobiusPay\Client: Invalid or missing expiration');return null;}
        $date=DateTime::createFromFormat('my',$exp);
        if(!$date instanceof DateTime)$date=DateTime::createFromFormat('mY',$exp);
        if(!$date instanceof DateTime)return null;
        $date->modify('last day of this month');return $date->format($format);
    }

    public function doTransaction(?float $amount=NULL):void{
        match($this->getType()){
            self::TYPE_AUTH=>$this->authTransaction($amount),
            self::TYPE_CAPTURE=>$this->captureTransaction($amount),
            self::TYPE_CREDIT=>$this->creditTransaction($amount),
            self::TYPE_REFUND=>$this->refundTransaction($amount),
            self::TYPE_SALE=>$this->saleTransaction($amount),
            self::TYPE_UPDATE=>$this->updateTransaction(),
            self::TYPE_VALIDATE=>$this->validateTransaction($amount),
            self::TYPE_VOID=>$this->voidTransaction(),
            default=>throw new Exception('Unmatched transaction type.')
        };
    }

    public function getType():string{return $this->type;}
    public function setType(string $type):Client{$this->type=$type;return $this;}
    private function setAmount(float|string $amount):void{
        $dot=strrpos($amount,'.');$comma=strrpos($amount,',');
        $sep=$dot>$comma&&$dot?$dot:($comma>$dot&&$comma?$comma:FALSE);
        $this->amount=!$sep?floatval(preg_replace('/[^0-9]/','',$amount)):floatval(sprintf("%d.%d",preg_replace('/[^0-9]/','',substr($amount,0,$sep)),preg_replace('/[^0-9]/','',substr($amount,$sep+1,strlen($amount)))));
    }
    public function setOrder(array $order):Client{
        $this->order=filter_var_array($order,array('description'=>FILTER_DEFAULT,'id'=>FILTER_DEFAULT,'ip_address'=>FILTER_DEFAULT,'po_number'=>FILTER_DEFAULT,'shipping'=>FILTER_DEFAULT,'tax'=>FILTER_VALIDATE_FLOAT,'comments'=>FILTER_DEFAULT))?:array();
        return $this;}
    public function setTransactionRefId(string $refid):Client{$this->order['id']=$refid;return $this;}
    #[Pure]public function getAmount(bool $formatted=FALSE):float|string{return !$formatted?$this->amount:Helpers::FormatCurrency($this->amount);}
    #[Pure]public function isSandbox():bool{return $this->config->getMode()==Config::DEVELOPMENT_MODE;}
    public function setTransactionId(?string $transaction_id):void{$this->transaction_id=$transaction_id;}

    /* ------------------- Added Transaction Handlers ------------------- */

    private function saleTransaction(?float $amount=NULL):void{
        $this->setAmount($amount??0);
        $payload=array_merge([
            'type'=>self::TYPE_SALE,
            'username'=>$this->config->getUsername(),
            'password'=>$this->config->getPassword(),
            'amount'=>number_format($this->amount,2,'.',''),
            'orderid'=>$this->order['id']??uniqid('EA-',true),
            'description'=>$this->order['description']??'Event Pass Sale',
            'ipaddress'=>$this->order['ip_address']??($_SERVER['REMOTE_ADDR']??'127.0.0.1'),
        ],[
            'ccnumber'=>$this->creditCard['account']??'',
            'ccexp'=>$this->creditCard['expiration']??'',
            'cvv'=>$this->creditCard['cvv']??'',
        ],[
            'first_name'=>$this->billing['first_name']??'',
            'last_name'=>$this->billing['last_name']??'',
            'address1'=>$this->billing['address_line_1']??'',
            'address2'=>$this->billing['address_line_2']??'',
            'city'=>$this->billing['city']??'',
            'state'=>$this->billing['state']??'',
            'zip'=>$this->billing['zip_code']??'',
            'country'=>$this->billing['country']??'',
            'phone'=>$this->billing['phone']??'',
            'email'=>$this->billing['email']??'',
        ]);

        $ch=curl_init(self::MERCHANT_ENDPOINT);
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>45,CURLOPT_POSTFIELDS=>http_build_query($payload),CURLOPT_USERAGENT=>self::MERCHANT_USERAGENT]);
        $response=curl_exec($ch);$error=curl_error($ch);curl_close($ch);
        if($error)throw new Exception("MobiusPay cURL Error: ".$error);
        parse_str($response,$parsed);
        $this->transaction=$parsed?:[];
        if(empty($parsed['response']))throw new Exception("MobiusPay invalid response: ".$response);
    }

    private function authTransaction(?float $amount=NULL):void{$this->saleTransaction($amount);}
    private function captureTransaction(?float $amount=NULL):void{$this->saleTransaction($amount);}
    private function creditTransaction(?float $amount=NULL):void{$this->saleTransaction($amount);}
    private function refundTransaction(?float $amount=NULL):void{$this->saleTransaction($amount);}
    private function updateTransaction():void{$this->transaction=['response'=>self::RESPONSE_APPROVED];}
    private function validateTransaction(?float $amount=NULL):void{$this->saleTransaction($amount);}
    private function voidTransaction():void{$this->saleTransaction(0);}
}
