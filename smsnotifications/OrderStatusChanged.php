<?php

namespace IgniterLabs\SmsNotify\SmsNotifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class OrderStatusChanged extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.order_status_changed';
}