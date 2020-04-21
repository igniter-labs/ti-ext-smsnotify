<?php

namespace IgniterLabs\SmsNotify\Notifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class OrderStatusChanged extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.order_status_changed';
}