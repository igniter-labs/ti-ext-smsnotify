<?php

namespace IgniterLabs\SmsNotify\SmsNotifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class ReservationStatusChanged extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.reservation_status_changed';
}