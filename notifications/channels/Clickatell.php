<?php

namespace IgniterLabs\SmsNotify\Notifications\Channels;

use IgniterLabs\SmsNotify\Classes\BaseChannel;
use NotificationChannels\Clickatell\ClickatellChannel;

class Clickatell extends BaseChannel
{
    protected $channelClassName = ClickatellChannel::class;

    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.clickatell.text_title',
            'description' => 'igniterlabs.smsnotify::default.clickatell.text_desc',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'fields' => [
                'api_key' => [
                    'label' => 'API Key',
                    'type' => 'text',
                ],
                'setup' => [
                    'type' => 'partial',
                    'path' => '$/igniterlabs/smsnotify/notifications/channels/clickatell/info',
                    'tab' => 'Setup',
                ],
            ],
        ];
    }
}