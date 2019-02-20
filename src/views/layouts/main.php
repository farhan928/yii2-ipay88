<?php 
use yii\helpers\Html;

$controller = $this->context;
$assetUrl = $controller->module->assetUrl;
$bootstrapVersion = $controller->module->bootstrapVersion;

//$this->registerCssFile($assetUrl.'/css/normalize.css');
//$this->registerCssFile($assetUrl.'/css/skeleton.css');
$this->registerCssFile($assetUrl.'/css/style.css');
?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?= Html::csrfMetaTags() ?>   
    
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/<?= $bootstrapVersion ?>/css/bootstrap.min.css" crossorigin="anonymous">
    <?php $this->head() ?>
  </head>
  <body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>
  </body>
</html>
<?php $this->endPage() ?>