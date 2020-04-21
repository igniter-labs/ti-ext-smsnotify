<?php

namespace IgniterLabs\SmsNotify\Notifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class ReservationAssigned extends BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.reservation_assigned';
}