<?php

namespace IgniterLabs\SmsNotify\SmsNotifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class OrderConfirmed extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.order_confirmed';
}