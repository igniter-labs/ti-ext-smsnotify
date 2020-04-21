<?php

namespace IgniterLabs\SmsNotify\Notifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class ReservationConfirmed extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.reservation_confirmed';
}