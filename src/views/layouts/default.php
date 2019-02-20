<?php
/* @var $this \yii\web\View */
/* @var $content string */

$controller = $this->context;
$route = $controller->route;

?>
<?php $this->beginContent($controller->module->mainLayout) ?>
<div class="container mt-5">    
    <?= $content ?>    
</div>
<?php $this->endContent(); ?>
