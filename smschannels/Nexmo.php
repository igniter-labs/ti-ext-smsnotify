<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Illuminate\Notifications\Channels\NexmoSmsChannel;

class Nexmo extends BaseChannel
{
    protected $channelClassName = NexmoSmsChannel::class;

    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.nexmo.text_title',
            'description' => 'igniterlabs.smsnotify::default.nexmo.text_desc',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => '$/igniterlabs/smsnotify/smschannels/nexmo/info',
                ],
                'api_key' => [
                    'label' => 'API Key',
                    'type' => 'text',
                ],
                'api_secret' => [
                    'label' => 'API Secret',
                    'type' => 'text',
                ],
                'send_from' => [
                    'label' => 'Send From Number',
                    'type' => 'text',
                ],
            ],
        ];
    }
}
