<?php

namespace IgniterLabs\SmsNotify\SmsNotifications;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

class AnonymousNotification extends BaseNotification
{
    protected function getData()
    {
        if (is_array($this->host))
            return $this->host;

        return parent::getData();
    }
}