<?php

namespace IgniterLabs\SmsNotify\SmsNotifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class OrderAssigned extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.order_assigned';
}