<?php 
namespace farhan928\Ipay88\components;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use farhan928\Ipay88\models\Ipay88Config;
use farhan928\Ipay88\models\Ipay88Transaction;

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
    public $remark = null;
    public $lang = 'UTF-8';
    public $signatureType = 'SHA256';
    private $signature;
    public $responseUrl;
    public $backendUrl;    
    public $redirectUrl;    
    private $entryUrl;
    private $enquiryUrl;
    public $entity_id = null;
    public $status;
    private $endPonintUrl = 'https://payment.ipay88.com.my/epayment';
    private $module;
    private $moduleName = 'ipay88';

    public function __construct($config = [])
    {
        // ... initialization before configuration is applied
        //$this->merchantCode = $merchantCode;
        //$this->merchantKey = $merchantKey;
       
        parent::__construct($config);
    }

    public function init()
    {
        parent::init(); // Call parent implementation;   
        
        $this->setEntryUrl($this->endPonintUrl.'/entry.asp');
        $this->setEnquiryUrl($this->endPonintUrl.'/enquiry.asp');
        $this->setResponseUrl(Url::to(['/'.$this->moduleName.'/default/response'], true));
        $this->setBackendUrl(Url::to(['/'.$this->moduleName.'/default/backend'], true));

        //$module = Yii::$app->controller->module;
        $this->module = \Yii::$app->getModule($this->moduleName);
       
        if ( $this->module->authMode == 'config' ) {
            $this->setMerchantCode($this->module->merchantCode);
            $this->setMerchantKey($this->module->merchantKey);           
        } 
    }

    public function createRequest() {
        if( $this->getEntityId() && $this->module->authMode == 'db' ) {           
            $config = Ipay88Config::findOne(['entity_id'=>$this->getEntityId()]);
            if ( $config ) {                  
                $this->setMerchantCode($config->merchant_code);
                $this->setMerchantKey($config->merchant_key);
            } 
        }

        if( !($this->getMerchantCode() && $this->getMerchantKey()) ) {
            throw new \yii\web\NotFoundHttpException('No config found');
        }

        $model = new Ipay88Transaction(['scenario' => Ipay88Transaction::SCENARIO_REQUEST]);

        if(!$this->getRefNo()) $this->setRefNo($model->generateRefNo());
        if(!$this->getSignature()) $this->setSignature($this->getRequestSignature());
        if( $this->module->testMode == true ) {
            $this->setAmount(1.00);
        }  
               
        $model->entity_id = $this->getEntityId();
        $model->amount = $this->getAmount();
        $model->prod_desc = $this->getProdDesc();
        $model->user_name = $this->getUserName();
        $model->user_email = $this->getUserEmail();
        $model->user_contact = $this->getUserContact();
        $model->redirect_url = $this->getRedirectUrl();
        $model->remark = $this->getRemark();
        $model->merchant_code = $this->getMerchantCode();           
        $model->ref_no = $this->getRefNo();           
        $model->currency = $this->getCurrency();            
        $model->lang = $this->getLang(); 
        $model->signature_type = $this->getSignatureType();                            
        $model->response_url = $this->getResponseUrl();                            
        $model->backend_url = $this->getBackendUrl(); 
        $model->signature = $this->getSignature(); 
            
        if($model->validate()){
            $model->save(false);

            return ['ipay88'=>$this, 'url'=>Url::to(['/'.$this->moduleName.'/default', 'id'=>$model->id])];
        } else {
            //dd($model->getErrors());
            throw new \yii\web\UnprocessableEntityHttpException('Validation Failed');
        }
    }

    public function generateSignature($source){		
		  
		//return base64_encode(self::hex2bin(sha1($source)));
		return hash ('sha256', $source);
	}
	
	public function getRequestSignature(){
		
        $amount = str_replace(',','', $this->getAmount()*100);
        $source = $this->getMerchantKey().$this->getMerchantCode().$this->getRefNo().$amount.$this->getCurrency();
		
		return self::generateSignature($source);
	}
	
	public function getResponseSignature(){
        
        $amount = str_replace(',','', $this->getAmount()*100);
		$source = $this->getMerchantKey().$this->getMerchantCode().$this->getPaymentId().$this->getRefNo().$amount.$this->getCurrency().$this->getStatus();
					
		return self::generateSignature($source);
    }    
    
    public function setMerchantIdentity($entity_id)
    {
        $this->setEntityId($entity_id);
        if( $this->getEntityId() && $this->module->authMode == 'db' ) {           
            $config = Ipay88Config::findOne(['entity_id'=>$this->getEntityId()]);
            if ( $config ) {                  
                $this->setMerchantCode($config->merchant_code);
                $this->setMerchantKey($config->merchant_key);
            } 
        }

        return $this;
    }

    /**
     * Get the value of merchantCode
     */ 
    public function getMerchantCode()
    {
        return $this->merchantCode;
    }

    /**
     * Set the value of merchantCode
     *
     * @return  self
     */ 
    public function setMerchantCode($merchantCode)
    {
        $this->merchantCode = $merchantCode;

        return $this;
    }

    /**
     * Get the value of merchantKey
     */ 
    public function getMerchantKey()
    {
        return $this->merchantKey;
    }

    /**
     * Set the value of merchantKey
     *
     * @return  self
     */ 
    public function setMerchantKey($merchantKey)
    {
        $this->merchantKey = $merchantKey;

        return $this;
    }

    /**
     * Get the value of paymentId
     */ 
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Set the value of paymentId
     *
     * @return  self
     */ 
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Get the value of refNo
     */ 
    public function getRefNo()
    {
        return $this->refNo;
    }

    /**
     * Set the value of refNo
     *
     * @return  self
     */ 
    public function setRefNo($refNo)
    {
        $this->refNo = $refNo;

        return $this;
    }

    /**
     * Get the value of amount
     */ 
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */ 
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of currency
     */ 
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the value of currency
     *
     * @return  self
     */ 
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the value of prodDesc
     */ 
    public function getProdDesc()
    {
        return $this->prodDesc;
    }

    /**
     * Set the value of prodDesc
     *
     * @return  self
     */ 
    public function setProdDesc($prodDesc)
    {
        $this->prodDesc = $prodDesc;

        return $this;
    }

    /**
     * Get the value of userName
     */ 
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set the value of userName
     *
     * @return  self
     */ 
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get the value of userEmail
     */ 
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set the value of userEmail
     *
     * @return  self
     */ 
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * Get the value of userContact
     */ 
    public function getUserContact()
    {
        return $this->userContact;
    }

    /**
     * Set the value of userContact
     *
     * @return  self
     */ 
    public function setUserContact($userContact)
    {
        $this->userContact = $userContact;

        return $this;
    }

    /**
     * Get the value of remark
     */ 
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set the value of remark
     *
     * @return  self
     */ 
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get the value of lang
     */ 
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set the value of lang
     *
     * @return  self
     */ 
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get the value of signatureType
     */ 
    public function getSignatureType()
    {
        return $this->signatureType;
    }

    /**
     * Set the value of signatureType
     *
     * @return  self
     */ 
    public function setSignatureType($signatureType)
    {
        $this->signatureType = $signatureType;

        return $this;
    }

    /**
     * Get the value of signature
     */ 
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set the value of signature
     *
     * @return  self
     */ 
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get the value of responseUrl
     */ 
    public function getResponseUrl()
    {
        return $this->responseUrl;
    }

    /**
     * Set the value of responseUrl
     *
     * @return  self
     */ 
    public function setResponseUrl($responseUrl)
    {
        $this->responseUrl = $responseUrl;

        return $this;
    }

    /**
     * Get the value of backendUrl
     */ 
    public function getBackendUrl()
    {
        return $this->backendUrl;
    }

    /**
     * Set the value of backendUrl
     *
     * @return  self
     */ 
    public function setBackendUrl($backendUrl)
    {
        $this->backendUrl = $backendUrl;

        return $this;
    }

    /**
     * Get the value of entryUrl
     */ 
    public function getEntryUrl()
    {
        return $this->entryUrl;
    }

    /**
     * Set the value of entryUrl
     *
     * @return  self
     */ 
    public function setEntryUrl($entryUrl)
    {
        $this->entryUrl = $entryUrl;

        return $this;
    }

    /**
     * Get the value of enquiryUrl
     */ 
    public function getEnquiryUrl()
    {
        return $this->enquiryUrl;
    }

    /**
     * Set the value of enquiryUrl
     *
     * @return  self
     */ 
    public function setEnquiryUrl($enquiryUrl)
    {
        $this->enquiryUrl = $enquiryUrl;

        return $this;
    }

    /**
     * Get the value of entity_id
     */ 
    public function getEntityId()
    {
        return $this->entity_id;
    }

    /**
     * Set the value of entity_id
     *
     * @return  self
     */ 
    public function setEntityId($entity_id)
    {
        $this->entity_id = $entity_id;

        return $this;
    }

    /**
     * Get the value of redirectUrl
     */ 
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set the value of redirectUrl
     *
     * @return  self
     */ 
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}