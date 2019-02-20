<?php 
namespace farhan928\Ipay88\components;

use Yii;
use yii\base\Component;

class Ipay88 extends Component
{
    public $merchantCode;
    public $merchantKey;
    public $paymentId;
    public $refNo;
    public $amount;
    public $currency = 'MYR';
    public $prodDesc;
    public $userName;
    public $userEmail;
    public $userContact;
    public $remark;
    public $lang = 'UTF-8';
    public $signatureType = 'SHA256';
    private $signature;
    public $responseUrl;
    public $backendUrl;
    private $endPonintUrl = 'https://payment.ipay88.com.my/epayment';
    private $entryUrl;
    private $enquiryUrl;

    public function __construct($merchantCode, $merchantKey, $config = [])
    {
        // ... initialization before configuration is applied
        $this->merchantCode = $merchantCode;
        $this->merchantKey = $merchantKey;
        parent::__construct($config);
    }

    public function init()
    {
        parent::init(); // Call parent implementation;   
        
        $this->setEntryUrl($this->endPonintUrl.'/entry.asp');
        $this->setEnquiryUrl($this->endPonintUrl.'/enquiry.asp');
    }

    public function generateSignature($source){		
		  
		//return base64_encode(self::hex2bin(sha1($source)));
		return hash ('sha256', $source);
	}
	
	public function getSignature(){
		
        $amount = str_replace(',','', $this->amount*100);
        $source = $this->merchantKey.$this->merchantCode.$this->refNo.$amount.$this->currency;
				
		return self::generateSignature($source);
	}
	
	public function getResponseSignature($status){
        
        $amount = str_replace(',','', $this->amount*100);
		$source = $this->merchantKey.$this->merchantCode.$this->paymentId.$this->refNo.$amount.$this->currency.$status;
					
		return self::generateSignature($source);
    }   
    
    public function setEntryUrl($entry_url){
        return $this->entryUrl = $entry_url;
    }

    public function getEntryUrl(){
        return $this->entryUrl;
    }

    public function setEnquiryUrl($enquiry_url){
        return $this->enquiryUrl = $enquiry_url;
    }

    public function getEnquiryUrl(){
        return $this->enquiryUrl;
    }
    
}