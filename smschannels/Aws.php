<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Exception;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;

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
        $cred = new Credentials($this->model->key, $this->model->secret);
        $SnSclient = new SnsClient([
            'credentials' => $cred,
            'use_aws_shared_config_files' => false,
            'region' => 'us-east-1',
            'version' => '2010-03-31'
        ]);

        if (substr($to, 0, 1) != '+') // if not starting with + sign, use default country code
            $to = $this->model->country_code . $to;

        $result = $SnSclient->publish([
            'Message' => $content,
            'PhoneNumber' => $to,
        ]);
        $a = $result;
    }

    protected function fillOptionalParams(&$params, $optionalParams): self
    {
        return $this;
    }
}
