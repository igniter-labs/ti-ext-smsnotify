<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

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
                    'path' => '$/igniterlabs/smsnotify/smschannels/clickatell/info',
                ],
                'user' => [
                    'label' => 'API Username',
                    'type' => 'text',
                ],
                'pass' => [
                    'label' => 'API Password',
                    'type' => 'text',
                ],
                'api_id' => [
                    'label' => 'API ID',
                    'type' => 'text',
                ],
            ],
        ];
    }
}
