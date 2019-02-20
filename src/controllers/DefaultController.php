<?php

namespace farhan928\Ipay88\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use farhan928\Ipay88\components\Ipay88;
use farhan928\Ipay88\models\Ipay88Config;
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
        if (in_array($action->id, ['index', 'response', 'backend'])) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
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

    public function actionResponse()
    {
        $request = Yii::$app->request; 
        $module = Yii::$app->controller->module; 
        $merchantCode = $merchantKey = null;
        
        if ( $request->isPost ) {  
            //dd($request->post());  
                        
            if ( !$request->post('RefNo') ) {
                Yii::error($request->post(), __METHOD__.':Missing parameters');
                throw new \yii\web\UnprocessableEntityHttpException('Missing parameters');
            }
            
            if ( $module->authMode == 'config' ) {
                $merchantCode = $module->merchantCode;
                $merchantKey = $module->merchantKey;
            } else if( $module->authMode == 'db' ) {
                if ( !$request->post('MerchantCode') ) {
                    Yii::error($request->post(), __METHOD__.':Missing merchant code');
                    throw new \yii\web\UnprocessableEntityHttpException('Missing merchant code');
                }

                $config = Ipay88Config::findOne(['merchant_code'=>$request->post('MerchantCode')]);
                
                if ( $config ) {                  
                    $merchantCode = $config->merchant_code;
                    $merchantKey = $config->merchant_key;
                } else {
                    Yii::error($request->post(), __METHOD__.':No config found');
                    throw new \yii\web\NotFoundHttpException('No config found');
                }                
            }

            // if status is success, check hash token
            if ( $request->post('Status') == 1 ) {
                $ipay = new Ipay88($merchantCode, $merchantKey);
                $ipay->paymentId = $request->post('PaymentId');
                $ipay->refNo = $request->post('RefNo');
                $ipay->amount = $request->post('Amount');
                $ipay->currency = $request->post('Currency');
                $match_signature = $ipay->getResponseSignature($request->post('Status'));

                if( $match_signature != $request->post('Signature') ) {
                    Yii::error($request->post(), __METHOD__.':Signature not matched');
                    throw new \yii\web\ForbiddenHttpException('Signature not matched');
                }
            }            

            $transaction = Ipay88Transaction::findOne(['ref_no'=>$request->post('RefNo')]);
            
            $model = new Ipay88Response();
            if ($transaction) $model->transaction_id = $transaction->id; 
            $model->ref_no = $request->post('RefNo'); 
            $model->trans_id = $request->post('TransId'); 
            $model->content =  $request->post();
            $model->save(false);
            

            if ($transaction) {
                if( !$transaction->payment_id ) $transaction->payment_id = $request->post('PaymentId');
                if( !$transaction->status != 1 ) {
                    $transaction->status = $request->post('Status');
                    $transaction->err_desc = $request->post('ErrDesc');                    
                }

                $transaction->save(false);  

                if(parse_url ($transaction->redirect_url, PHP_URL_QUERY)){
					$concat = '&';
				}else{
					$concat = '?';
				}
				$redirect_url = $transaction->redirect_url.$concat.'ref_no='.$transaction->ref_no.'&status='.$request->post('Status');
                
                return $this->render('response', ['data'=>$request->post(), 'transaction'=>$transaction, 'redirect_url'=>$redirect_url]);
            } else {
                Yii::error($request->post(), __METHOD__.':Transaction not found');
                throw new \yii\web\BadRequestHttpException('Transaction not found');
            }

        } else {
            Yii::error($request->post(), __METHOD__.':HTTP request not allowed');
            throw new \yii\web\BadRequestHttpException('HTTP request not allowed');
        }
    }

    public function actionBackend()
    {
        $request = Yii::$app->request; 
        $module = Yii::$app->controller->module; 
        $merchantCode = $merchantKey = null;

        if ( $request->isPost ) {  
            //dd($request->post());  

            echo 'RECEIVEOK';
                        
            if ( !$request->post('RefNo') ) {
                Yii::error($request->post(), __METHOD__.':Missing parameters');
                return;
            }
            
            if ( $module->authMode == 'config' ) {
                $merchantCode = $module->merchantCode;
                $merchantKey = $module->merchantKey;
            } else if( $module->authMode == 'db' ) {
                if ( !$request->post('MerchantCode') ) {
                    Yii::error($request->post(), __METHOD__.':Missing merchant code');
                    return;
                }

                $config = Ipay88Config::findOne(['merchant_code'=>$request->post('MerchantCode')]);
                
                if ( $config ) {                  
                    $merchantCode = $config->merchant_code;
                    $merchantKey = $config->merchant_key;
                } else {
                    Yii::error($request->post(), __METHOD__.':No config found');
                    return;
                }                
            }

            // if status is success, check hash token
            if ( $request->post('Status') == 1 ) {
                $ipay = new Ipay88($merchantCode, $merchantKey);
                $ipay->paymentId = $request->post('PaymentId');
                $ipay->refNo = $request->post('RefNo');
                $ipay->amount = $request->post('Amount');
                $ipay->currency = $request->post('Currency');
                $match_signature = $ipay->getResponseSignature($request->post('Status'));

                if( $match_signature != $request->post('Signature') ) {
                    Yii::error($request->post(), __METHOD__.':Signature not matched');
                    return;
                }
            }            

            $transaction = Ipay88Transaction::findOne(['ref_no'=>$request->post('RefNo')]);
            
            $model = new Ipay88Response();
            if ($transaction) $model->transaction_id = $transaction->id; 
            $model->ref_no = $request->post('RefNo'); 
            $model->trans_id = $request->post('TransId'); 
            $model->content =  $request->post();
            $model->save(false);
            

            if ($transaction) {
                if( !$transaction->payment_id ) $transaction->payment_id = $request->post('PaymentId');
                if( !$transaction->status != 1 ) {
                    $transaction->status = $request->post('Status');
                    $transaction->err_desc = $request->post('ErrDesc');                    
                }

                $transaction->save(false);  
                
            } else {
                Yii::error($request->post(), __METHOD__.':Transaction not found');
                return;
            }

        } else {
            Yii::error($request->post(), __METHOD__.':HTTP request not allowed');
            return;
        }

        
    }
}
