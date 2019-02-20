<?php 
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php 
$form = ActiveForm::begin([
    'id' => 'responseForm',
    'action' => $redirect_url,     
    'options' => ['name' => 'responseForm'],
]) 
?>

<?php 
if($data){
    foreach ($data as $key => $value) {
        echo Html::hiddenInput($key, $value);
    }
}
?>
<?= Html::submitButton('Proceed to Payment', ['style' => 'visibility: hidden;']) ?>

<?php ActiveForm::end() ?>

<?php 
$script = "        
setTimeout(function(){ 			
    var form = document.responseForm;
    form.submit(); 						
}, 1);	
";
$this->registerJs($script, View::POS_READY);
?>