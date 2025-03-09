<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\SmsChannels;

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Override;
use Vonage\Client as VonageClient;
use Vonage\Message\Message;

class Vonage extends BaseChannel
{
    #[Override]
    public function channelDetails(): array
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.vonage.text_title',
            'description' => 'igniterlabs.smsnotify::default.vonage.text_desc',
        ];
    }

    #[Override]
    public function defineFormConfig(): array
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

    #[Override]
    public function getConfigRules(): array
    {
        return [
            'api_key' => ['required', 'string', 'max:128'],
            'api_secret' => ['required', 'string', 'max:128'],
            'send_from' => ['required', 'string', 'max:128'],
        ];
    }

    #[Override]
    public function send($to, $content): Message
    {
        $payload = [
            'type' => 'text',
            'from' => $this->model->send_from, // @phpstan-ignore-line property.notFound
            'to' => $to,
            'text' => trim((string) $content),
            'client-ref' => '',
        ];

        if ($this->model->status_callback) { // @phpstan-ignore-line property.notFound
            $payload['callback'] = $this->model->status_callback;
        }

        // @phpstan-ignore property.notFound
        if (!strlen($this->model->api_key) || !strlen($this->model->api_secret)) {
            throw new SystemException('Please provide your Vonage API credentials. api_key + api_secret');
        }

        app()->resolving(VonageClient::class, function(): void {
            config([
                'igniterlabs-smsnotify.vonage.api_key' => $this->model->api_key, // @phpstan-ignore-line property.notFound
                'igniterlabs-smsnotify.vonage.api_secret' => $this->model->api_secret, // @phpstan-ignore-line property.notFound
            ]);
        });

        return resolve(VonageClient::class)->message()->send($payload);
    }
}
