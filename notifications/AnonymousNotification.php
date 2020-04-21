<?php

namespace IgniterLabs\SmsNotify\Notifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class AnonymousNotification extends BaseNotification
{
    protected function getData()
    {
        return is_array($this->host) ? $this->host : [];
    }
}