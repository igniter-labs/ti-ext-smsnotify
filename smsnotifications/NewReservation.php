<?php

namespace IgniterLabs\SmsNotify\SmsNotifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class NewReservation extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.new_reservation';
}