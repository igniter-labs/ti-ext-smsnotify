<?php

namespace IgniterLabs\SmsNotify\Notifications\Channels;

use IgniterLabs\SmsNotify\Classes\BaseChannel;
use NotificationChannels\Twilio\TwilioChannel;

class Twilio extends BaseChannel
{
    protected $channelClassName = TwilioChannel::class;

    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.twilio.text_title',
            'description' => 'igniterlabs.smsnotify::default.twilio.text_desc',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'fields' => [
                'account_sid' => [
                    'label' => 'Account SID',
                    'type' => 'text',
                ],
                'auth_token' => [
                    'label' => 'Auth Token',
                    'type' => 'text',
                ],
                'from' => [
                    'label' => 'Send From Number',
                    'type' => 'text',
                ],
                'setup' => [
                    'type' => 'partial',
                    'path' => '$/igniterlabs/smsnotify/notifications/channels/twilio/info',
                    'tab' => 'Setup',
                ],
            ],
        ];
    }
}