<?php

namespace IgniterLabs\SmsNotify\SmsNotifications\Channels;

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
                'setup' => [
                    'type' => 'partial',
                    'path' => '$/igniterlabs/smsnotify/smsnotifications/channels/clickatell/info',
                ],
                'api_key' => [
                    'label' => 'API Key',
                    'type' => 'text',
                ],
            ],
        ];
    }
}