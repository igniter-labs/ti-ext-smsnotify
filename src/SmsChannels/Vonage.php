<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Vonage\Client as VonageClient;

class Vonage extends BaseChannel
{
    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.vonage.text_title',
            'description' => 'igniterlabs.smsnotify::default.vonage.text_desc',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => 'nexmo/info',
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

    public function getConfigRules()
    {
        return [
            'api_key' => ['required', 'string', 'max:128'],
            'api_secret' => ['required', 'string', 'max:128'],
            'send_from' => ['required', 'string', 'max:128'],
        ];
    }

    public function send($to, $content)
    {
        $payload = [
            'type' => 'text',
            'from' => $this->model->send_from, // @phpstan-ignore-line property.notFound
            'to' => $to,
            'text' => trim($content),
            'client-ref' => '',
        ];

        if ($this->model->status_callback) { // @phpstan-ignore-line property.notFound
            $payload['callback'] = $this->model->status_callback;
        }

        // @phpstan-ignore property.notFound
        if (!strlen($this->model->api_key) || !strlen($this->model->api_secret)) {
            throw new SystemException('Please provide your Vonage API credentials. api_key + api_secret');
        }

        app()->resolving(VonageClient::class, function() {
            config([
                'igniterlabs-smsnotify.vonage.api_key' => $this->model->api_key, // @phpstan-ignore-line property.notFound
                'igniterlabs-smsnotify.vonage.api_secret' => $this->model->api_secret, // @phpstan-ignore-line property.notFound
            ]);
        });

        return resolve(VonageClient::class)->message()->send($payload);
    }
}
