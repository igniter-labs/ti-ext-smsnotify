<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\SmsChannels;

use Override;
use Aws\Sns\SnsClient;
use IgniterLabs\SmsNotify\Classes\BaseChannel;

class Aws extends BaseChannel
{
    #[Override]
    public function channelDetails(): array
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.aws.text_title',
            'description' => 'igniterlabs.smsnotify::default.aws.text_desc',
        ];
    }

    #[Override]
    public function defineFormConfig(): array
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

    #[Override]
    public function getConfigRules(): array
    {
        return [
            'key' => ['required', 'string', 'max:128'],
            'secret' => ['required', 'string', 'max:128'],
            'country_code' => ['required', 'string', 'max:128'],
        ];
    }

    #[Override]
    public function send($to, $content): void
    {
        // if not starting with + sign, use default country code
        if (!str_starts_with((string) $to, '+')) {
            $to = $this->model->country_code.$to;
        }

        app()->resolving(SnsClient::class, function(): void {
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
