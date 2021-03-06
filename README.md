Yii2 iPay88
===========
Yii2 extension for iPay88 payment gateway

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require farhan928/yii2-ipay88 "^1.0.0"
```

or add

```
"farhan928/yii2-ipay88": "^1.0.0"
```

to the require section of your `composer.json` file.

## Release Changes

> NOTE: Refer the [CHANGE LOG](https://github.com/farhan928/yii2-ipay88/blob/master/CHANGE.md) for details on changes to various releases.

## Usage

Add this to the alias section of your main config.  
```php
'aliases' => [
    ...
    '@farhan928/Ipay88' => '@vendor/farhan928/yii2-ipay88/src',
    ...
],
```

and add this code below to the module section of your main config.  
```php
'modules' => [
    ...
    'ipay88' => [
            'class' => 'farhan928\Ipay88\Module',
            'layout' => 'default',
            'authMode' => 'db', //db or config, default to config         
            'entityTable' => 'user', // only set if authMode is db        
            'entityNameColumn' => 'name', // only set if authMode is db        
            'merchantKey' => 'YourMerchantKey', // only set if authMode is config
            'merchantCode' => 'YourMerchantCode', // only set if authMode is config
            'testMode' => false, // if set to true, all transactions will use amount 1.00
            'schema' => 'http', // default to http. 
            'on backendPost' => function ($event) {              
                // optional - event triggered when there is a backend post                
                // use $event->payload to get the post data from ipay88
                // write your code here
            },
        ],           
    ],
    ...
],
```

Add this code to the urlManager rules section.  
```php
'urlManager' => [
    ...
    'rules' => [
        ...
        'ipay88/<controller:\w+>/<id:\d+>' => 'ipay88/<controller>/index',
        'ipay88/<controller:\w+>/<action:\w+>/<id:\d+>' => 'ipay88/<controller>/<action>',
        'ipay88/<controller:\w+>/<action:\w+>' => 'ipay88/<controller>/<action>',
        ...
    ]
]
```

Run migration files
```
php yii migrate --migrationPath=@farhan928/Ipay88/migrations
```

If authMode is set to db, you can go to http://your.app.url/index.php?r=ipay88/config to add your entity configuration such as the merchant code and merchant key

## Examples

### Create a payment Request
```php
$create_request = $ipay88->setEntityId(1) // optional 
    ->setRefNo('ABC123') // optional. if leave null or not set, will auto generate.
    ->setAmount(1)
    ->setProdDesc('Product Description')
    ->setUserName('Your User Name')
    ->setUserEmail('youruser@email.com')
    ->setUserContact('012123123123')
    ->setRedirectUrl('http://yourredirecturl.com')
    ->setRemark('Remark') // optional
    ->createRequest();
```
Will return iPay88 object with payment URL. Redirect to the payment URL to proceed with payment.

## License

**yii2-ipay88** is released under the BSD-4-Clause License
