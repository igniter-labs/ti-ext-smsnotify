<?php

namespace IgniterLabs\SmsNotify\SmsNotifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class ReservationConfirmed extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.reservation_confirmed';
}