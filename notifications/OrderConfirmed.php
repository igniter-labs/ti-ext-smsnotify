<?php

namespace IgniterLabs\SmsNotify\Notifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class OrderConfirmed extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.order_confirmed';
}