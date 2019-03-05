<?php
namespace farhan928\Ipay88\events;

use Yii;
use yii\base\Event;

class BackendPostEvent extends Event
{
    public $payload;
}