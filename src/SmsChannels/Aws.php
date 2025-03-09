<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Aws\Sns\SnsClient;
use IgniterLabs\SmsNotify\Classes\BaseChannel;

class Aws extends BaseChannel
{
    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.aws.text_title',
            'description' => 'igniterlabs.smsnotify::default.aws.text_desc',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => 'aws/info',
                ],
                'key' => [
                    'label' => 'Key',
                    'type' => 'text',
                ],
                'secret' => [
                    'label' => 'Secret',
                    'type' => 'text',
                ],
                'country_code' => [
                    'label' => 'Default country code',
                    'type' => 'text',
                ],
            ],
        ];
    }

    public function getConfigRules()
    {
        return [
            'key' => ['required', 'string', 'max:128'],
            'secret' => ['required', 'string', 'max:128'],
            'country_code' => ['required', 'string', 'max:128'],
        ];
    }

    public function send($to, $content)
    {
        // if not starting with + sign, use default country code
        if (!str_starts_with($to, '+')) {
            $to = $this->model->country_code.$to;
        }

        app()->resolving(SnsClient::class, function() {
            config([
                'igniterlabs-smsnotify.aws.key' => $this->model->key,
                'igniterlabs-smsnotify.aws.secret' => $this->model->secret,
            ]);
        });

        resolve(SnsClient::class)->publish([
            'Message' => $content,
            'PhoneNumber' => $to,
        ]);
    }
}
