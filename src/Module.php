<?php

namespace farhan928\Ipay88;

use Yii;

/**
 * iPay88 payment gateway module
 * Connect with iPay88 as payment gateway
 *
 * @author Ahmad Farhan
 */
class Module extends \yii\base\Module
{    
    public $authMode = 'config';
    public $entityTable = 'user';
    public $entityNameColumn = 'username';
    public $merchantCode;
    public $merchantKey;
    public $assetUrl;
    public $redirectDuration = 1; // in seconds
    public $bootstrapVersion = '4.3.1';
    public $testMode = false;
    public $schema = 'http';
    
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'farhan928\Ipay88\controllers';

    /**
     * @var string Main layout using for module. Default to layout of parent module.
     * Its used when `layout` set to 'left-menu', 'right-menu' or 'top-menu'.
     */
    public $mainLayout = '@farhan928/Ipay88/views/layouts/main.php';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        list(,$this->assetUrl) = Yii::$app->assetManager->publish('@farhan928/Ipay88/assets');   
      
        //dd($this->authMode);     
    }
    
}
