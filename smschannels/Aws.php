<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Aws\Credentials\Credentials;
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
                    'path' => '$/igniterlabs/smsnotify/smschannels/aws/info',
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

    public function send($to, $content)
    {
        // if not starting with + sign, use default country code
        if (substr($to, 0, 1) != '+')
            $to = $this->model->country_code.$to;

        (new SnsClient([
            'credentials' => new Credentials($this->model->key, $this->model->secret),
            'use_aws_shared_config_files' => false,
            'region' => 'us-east-1',
            'version' => '2010-03-31',
        ]))->publish([
            'Message' => $content,
            'PhoneNumber' => $to,
        ]);
    }
}
