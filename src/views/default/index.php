<?php 
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$controller = $this->context;
$redirectDuration = $controller->module->redirectDuration;

$amount = number_format($model->amount, 2, '.', ',')
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'checkoutForm',
    'action' => $ipay->getEntryUrl(),     
    'options' => ['name' => 'checkoutForm'],
]) 
?>
<?= $form->errorSummary($model, ['class'=>'alert alert-danger']); ?>
<?php if (!$model->hasErrors()): ?>
<?php echo Html::activeHiddenInput($model, 'merchant_code', ['name'=>'MerchantCode']); ?>
<?php echo Html::activeHiddenInput($model, 'ref_no', ['name'=>'RefNo']); ?>
<?php echo $model->payment_id ? Html::activeHiddenInput($model, 'payment_id', ['name'=>'PaymentId']) : ''; ?>
<?php echo Html::activeHiddenInput($model, 'amount', ['name'=>'Amount', 'value'=>$amount ]); ?>
<?php echo Html::activeHiddenInput($model, 'currency', ['name'=>'Currency']); ?>
<?php echo Html::activeHiddenInput($model, 'prod_desc', ['name'=>'ProdDesc']); ?>
<?php echo Html::activeHiddenInput($model, 'user_name', ['name'=>'UserName']); ?>
<?php echo Html::activeHiddenInput($model, 'user_email', ['name'=>'UserEmail']); ?>
<?php echo Html::activeHiddenInput($model, 'user_contact', ['name'=>'UserContact']); ?>
<?php echo Html::activeHiddenInput($model, 'remark', ['name'=>'Remark']); ?>
<?php echo Html::activeHiddenInput($model, 'lang', ['name'=>'Lang']); ?>
<?php echo Html::activeHiddenInput($model, 'signature_type', ['name'=>'SignatureType']); ?>
<?php echo Html::activeHiddenInput($model, 'signature', ['name'=>'Signature']); ?>
<?php echo Html::activeHiddenInput($model, 'response_url', ['name'=>'ResponseURL', 'value'=>$model->response_url]); ?>
<?php echo Html::activeHiddenInput($model, 'backend_url', ['name'=>'BackendURL', 'value'=>$model->backend_url]); ?>

<p class="text-center">Redirecting to payment page. Please wait a few seconds. Do not close the browser.</p>
<p class="text-center">Click button below if it is not redirected in <?= ($redirectDuration+5) ?> seconds.</p>
<p class="text-center"><?= Html::submitButton('Proceed to Payment', ['class' => 'btn btn-success']) ?></p>
<?php endif; ?>
<?php ActiveForm::end() ?>

<?php 
if (!$model->hasErrors()) {
    $script = "
        var redirect_duration = ".($redirectDuration*100).";
        setTimeout(function(){ 			
            var form = document.checkoutForm;
            form.submit(); 						
        }, redirect_duration);	
    ";
    $this->registerJs($script, View::POS_READY);
}

?>