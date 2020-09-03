<?php

namespace IgniterLabs\SmsNotify\SmsNotifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class ReservationAssigned extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.reservation_assigned';
}