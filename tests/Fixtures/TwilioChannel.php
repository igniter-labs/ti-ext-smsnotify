<?php

namespace IgniterLabs\SmsNotify\Tests\Fixtures;

use IgniterLabs\SmsNotify\Classes\BaseChannel;

class TwilioChannel extends BaseChannel
{
    public function send($to, $content)
    {
        return "Sent via Twilio to {$to}: {$content}";
    }

    public function defineFormConfig()
    {
        return __DIR__.'/../_fixtures/fields';
    }

    public function channelDetails()
    {
        return [
            'name' => 'Twilio',
            'description' => 'Send notifications via Twilio',
        ];
    }
}
