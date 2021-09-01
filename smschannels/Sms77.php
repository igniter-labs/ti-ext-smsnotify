<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use IgniterLabs\SmsNotify\Classes\BaseChannel;
use NotificationChannels\SMS77\SMS77Channel;
use NotificationChannels\SMS77\SMS77Message;

class Sms77 extends BaseChannel
{
    protected $channelClassName = SMS77Channel::class;

    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.sms77.text_title',
            'description' => 'igniterlabs.smsnotify::default.sms77.text_desc',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => '$/igniterlabs/smsnotify/smschannels/sms77/info',
                ],
                'api_key' => [
                    'label' => 'API Key',
                    'type' => 'text',
                ],
                'from' => [
                    'label' => 'Send From Number',
                    'type' => 'text',
                ],
            ],
        ];
    }

    public function toMessage($notifiable)
    {
        return new SMS77Message;
    }
}
