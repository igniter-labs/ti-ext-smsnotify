<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\SmsChannels;

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Illuminate\Support\Facades\Http;
use Override;

class Clickatell extends BaseChannel
{
    protected const string API_URL = 'https://platform.clickatell.com';

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
                'from' => [
                    'label' => 'Send from (optional)',
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
            'from' => ['nullable', 'string', 'max:128'],
        ];
    }

    #[Override]
    public function send($to, $content): void
    {
        $response = Http::withHeaders(['Authorization' => $this->model->api_key])
            ->asJson()
            ->acceptJson()
            ->post(self::API_URL.'/v1/message', [
                'messages' => [
                    array_filter([
                        'channel' => 'sms',
                        'to' => $to,
                        'content' => $content,
                        'from' => $this->model->from,
                    ]),
                ],
            ]);

        $errorMessages = collect($response->json('messages'))->map(function($message): string {
            $errorCode = (int)array_get($message, 'error.code');

            return $errorCode !== self::SUCCESSFUL_SEND
                ? $errorCode.': '.array_get($message, 'error.description')
                : '';
        })
            ->filter()
            ->implode(', ');

        throw_unless($response->getStatusCode() === 202, SystemException::class, sprintf(
            'Clickatell responded with an error: %s', $errorMessages
        ));
    }
}
