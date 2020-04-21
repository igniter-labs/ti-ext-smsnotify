<?php

namespace IgniterLabs\SmsNotify\Notifications\Channels;

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
                'key' => [
                    'label' => 'API Key',
                    'type' => 'text',
                ],
                'secret' => [
                    'label' => 'API Secret',
                    'type' => 'text',
                ],
                'from' => [
                    'label' => 'Send From Number',
                    'type' => 'text',
                ],
                'setup' => [
                    'type' => 'partial',
                    'path' => '$/igniterlabs/smsnotify/notifications/channels/nexmo/info',
                    'tab' => 'Setup',
                ],
            ],
        ];
    }
}