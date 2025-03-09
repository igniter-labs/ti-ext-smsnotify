<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\SmsChannels;

use Clickatell\Rest as ClickatellClient;
use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Override;

class Clickatell extends BaseChannel
{
    protected const int SUCCESSFUL_SEND = 0;

    #[Override]
    public function channelDetails(): array
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.clickatell.text_title',
            'description' => 'igniterlabs.smsnotify::default.clickatell.text_desc',
        ];
    }

    #[Override]
    public function defineFormConfig(): array
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => 'clickatell/info',
                ],
                'api_key' => [
                    'label' => 'API Key',
                    'type' => 'text',
                ],
                'api_id' => [
                    'label' => 'API ID',
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
            'api_id' => ['required', 'string', 'max:128'],
        ];
    }

    #[Override]
    public function send($to, $content): void
    {
        app()->resolving(ClickatellClient::class, function(): void {
            config([
                'igniterlabs-smsnotify.clickatell.api_key' => $this->model->api_key,
            ]);
        });

        $responses = resolve(ClickatellClient::class)->sendMessage([
            'to' => [$to],
            'content' => $content,
        ]);

        collect($responses)->each(function($response): void {
            $errorCode = (int)array_get($response, 'errorCode');

            if ($errorCode != self::SUCCESSFUL_SEND) {
                throw new SystemException(sprintf("Clickatell responded with an error '%s: %s'",
                    (string)array_get($response, 'error'),
                    $errorCode,
                ));
            }
        });
    }
}
