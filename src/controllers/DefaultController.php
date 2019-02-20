<?php

namespace farhan928\Ipay88\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use farhan928\Ipay88\components\Ipay88;
use farhan928\Ipay88\models\Ipay88Config;
use farhan928\Ipay88\models\Ipay88Backend;
use farhan928\Ipay88\models\Ipay88Response;
use farhan928\Ipay88\models\Ipay88Transaction;

/**
 * Default controller for the `ipay88` module
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {            
        if (in_array($action->id, ['response', 'backend'])) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($id)
    {
        $transaction = Ipay88Transaction::findOne(['id'=>$id]);
        if (!$transaction) throw new \yii\web\NotFoundHttpException('Invalid transaction');
        if ($transaction->status == 1) throw new \yii\web\UnprocessableEntityHttpException('Transaction was completed');

        $ipay = new Ipay88();
        return $this->render('index', ['model'=>$transaction, 'ipay'=>$ipay]);
    }    

    public function actionResponse()
    {
        $request = Yii::$app->request;                
        
        if ( $request->isPost ) {  
            //dd($request->post());  
                        
            if ( !$request->post('RefNo') ) {
                Yii::error($request->post(), __METHOD__.':Missing parameters');
                throw new \yii\web\UnprocessableEntityHttpException('Missing parameters');
            }

            $ref_no = $request->post('RefNo');
            $status = $request->post('Status');
            $signature = $request->post('Signature');
            $payment_id = $request->post('PaymentId');
            $err_desc = $request->post('ErrDesc');

            $model = new Ipay88Response();
             
            $model->ref_no = $ref_no; 
            $model->trans_id = $request->post('TransId'); 
            $model->content =  $request->post();
            $model->save(false);

            $transaction = Ipay88Transaction::findOne(['ref_no'=>$ref_no]);
            if(!$transaction){
                Yii::error($request->post(), __METHOD__.':Transaction not found');
                throw new \yii\web\BadRequestHttpException('Transaction not found');
            }

            $model->transaction_id = $transaction->id;
            $model->save(false);

            $ipay = new Ipay88();
            if($transaction->entity_id) $ipay->setMerchantIdentity($transaction->entity_id);
            $response_signature = $ipay->setPaymentId($payment_id)
                ->setRefNo($transaction->ref_no)
                ->setAmount($transaction->amount)
                ->setCurrency($transaction->currency)
                ->setStatus($status)
                ->getResponseSignature();

            // if status is success, check hash token
            if ( $status == 1 ) {               
                if( $response_signature != $signature ) {
                    Yii::error($request->post(), __METHOD__.':Signature not matched');
                    throw new \yii\web\ForbiddenHttpException('Signature not matched');
                }
            }                     

            if( !$transaction->payment_id ) $transaction->payment_id = $payment_id;
            if( !$transaction->status != 1 ) {
                $transaction->status = $status;
                $transaction->err_desc = $err_desc;                    
            }

            $transaction->save(false);  

            if(parse_url ($transaction->redirect_url, PHP_URL_QUERY)){
                $concat = '&';
            }else{
                $concat = '?';
            }
            $redirect_url = $transaction->redirect_url.$concat.'ref_no='.$transaction->ref_no.'&status='.$status;
            
            return $this->render('response', ['data'=>$request->post(), 'transaction'=>$transaction, 'redirect_url'=>$redirect_url]);

        } else {
            Yii::error($request->post(), __METHOD__.':HTTP request not allowed');
            throw new \yii\web\BadRequestHttpException('HTTP request not allowed');
        }
    }

    public function actionBackend()
    {
        $request = Yii::$app->request;                
        
        if ( $request->isPost ) {  
                                    
            echo 'RECEIVEOK';

            if ( !$request->post('RefNo') ) {
                Yii::error($request->post(), __METHOD__.':Missing parameters');
                return;
            }

            $ref_no = $request->post('RefNo');
            $status = $request->post('Status');
            $signature = $request->post('Signature');
            $payment_id = $request->post('PaymentId');
            $err_desc = $request->post('ErrDesc');

            $model = new Ipay88Backend();
             
            $model->ref_no = $ref_no; 
            $model->trans_id = $request->post('TransId'); 
            $model->content =  $request->post();
            $model->save(false);

            $transaction = Ipay88Transaction::findOne(['ref_no'=>$ref_no]);
            if(!$transaction){
                Yii::error($request->post(), __METHOD__.':Transaction not found');
                return;
            }

            $model->transaction_id = $transaction->id;
            $model->save(false);

            $ipay = new Ipay88();
            if($transaction->entity_id) $ipay->setMerchantIdentity($transaction->entity_id);
            $response_signature = $ipay->setPaymentId($payment_id)
                ->setRefNo($transaction->ref_no)
                ->setAmount($transaction->amount)
                ->setCurrency($transaction->currency)
                ->setStatus($status)
                ->getResponseSignature();

            // if status is success, check hash token
            if ( $status == 1 ) {               
                if( $response_signature != $signature ) {
                    Yii::error($request->post(), __METHOD__.':Signature not matched');
                    return;
                }
            }                     

            if( !$transaction->payment_id ) $transaction->payment_id = $payment_id;
            if( !$transaction->status != 1 ) {
                $transaction->status = $status;
                $transaction->err_desc = $err_desc;                    
            }

            $transaction->save(false);  
        } else {
            Yii::error($request->post(), __METHOD__.':HTTP request not allowed');
            return;
        }        
    }

    /**
     * deprecated (don't use anymore)
     * @return string
     */
    public function actionPay()
    {
        $db = Yii::$app->db; 
        $request = Yii::$app->request; 
        $module = Yii::$app->controller->module; 
        $ipay = $merchantCode = $merchantKey = null;

        $model = new Ipay88Transaction(['scenario' => Ipay88Transaction::SCENARIO_REQUEST]);
        
        if ( $request->isPost ) {  
            $model->attributes = $request->post();
            
            if( $module->testMode == true ) {
                $model->amount = 1.00;
            }

            if ( $module->authMode == 'config' ) {
                $merchantCode = $module->merchantCode;
                $merchantKey = $module->merchantKey;
            } else if( $module->authMode == 'db' ) {
                if ( !$model->entity_id ) throw new \yii\web\UnprocessableEntityHttpException('Missing entity_id');

                $config = $db->createCommand('SELECT * FROM {{%ipay88_config}} WHERE entity_id='. $model->entity_id)->queryOne();
                if ( $config ) {                  
                    $merchantCode = $config['merchant_code'];
                    $merchantKey = $config['merchant_key'];
                } else {
                    throw new \yii\web\NotFoundHttpException('No config found');
                }
            }

            $ipay = new Ipay88($merchantCode, $merchantKey);
           
            $model->merchant_code = $merchantCode;           
            $model->ref_no = $model->ref_no ?: $model->generateRefNo();            
            $model->currency = $model->currency ?: $ipay->currency;            
            $model->lang = $model->lang ?: $ipay->lang; 
            $model->signature_type = $ipay->signatureType;                            
            $model->response_url = Url::to(['response'], true);                            
            $model->backend_url = Url::to(['backend'], true);                            
          
            $ipay->refNo = $model->ref_no;
            $ipay->amount = $model->amount;
            $ipay->currency = $model->currency;
            $ipay->lang = $model->lang;  
            $model->signature = $ipay->getSignature();  

            if($model->validate()){
                $model->save(false);
            }            
            
        } else {
            throw new \yii\web\BadRequestHttpException('HTTP request not allowed');
        }

        return $this->render('index', compact('model', 'ipay'));
    }
}
