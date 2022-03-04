<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use IgniterLabs\SmsNotify\Classes\BaseChannel;
use RuntimeException;
use Vonage\Client as VonageClient;
use Vonage\Client\Credentials\Basic;

class Nexmo extends BaseChannel
{
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

    public function send($to, $content)
    {
        $payload = [
            'type' => 'text',
            'from' => $this->model->send_from,
            'to' => $to,
            'text' => trim($content),
            'client-ref' => '',
        ];

        if ($this->model->status_callback) {
            $payload['callback'] = $this->model->status_callback;
        }

        return $this->client()->message()->send($payload);
    }

    protected function client(): VonageClient
    {
        if (!strlen($this->model->api_key) || !strlen($this->model->api_secret)) {
            throw new RuntimeException('Please provide your Vonage API credentials. api_key + api_secret');
        }

        return new VonageClient(
            new Basic($this->model->api_key, $this->model->api_secret)
        );
    }
}
