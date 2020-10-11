<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use IgniterLabs\SmsNotify\Classes\BaseChannel;
use NotificationChannels\Plivo\PlivoChannel;

class Plivo extends BaseChannel
{
    protected $channelClassName = PlivoChannel::class;

    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.plivo.text_title',
            'description' => 'igniterlabs.smsnotify::default.plivo.text_desc',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => '$/igniterlabs/smsnotify/smschannels/plivo/info',
                ],
                'auth_id' => [
                    'label' => 'Auth ID',
                    'type' => 'text',
                ],
                'auth_token' => [
                    'label' => 'Auth Token',
                    'type' => 'text',
                ],
                'from_number' => [
                    'label' => 'Send From Number',
                    'type' => 'text',
                ],
            ],
        ];
    }
}
