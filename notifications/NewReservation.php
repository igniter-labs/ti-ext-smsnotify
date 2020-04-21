<?php

namespace IgniterLabs\SmsNotify\Notifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class NewReservation extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.new_reservation';
}