<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\SmsChannels;

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Override;
use Plivo\RestClient as PlivoClient;

class Plivo extends BaseChannel
{
    #[Override]
    public function channelDetails(): array
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.plivo.text_title',
            'description' => 'igniterlabs.smsnotify::default.plivo.text_desc',
        ];
    }

    #[Override]
    public function defineFormConfig(): array
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => 'plivo/info',
                ],
                'auth_id' => [
                    'label' => 'Auth ID',
                    'type' => 'text',
                ],
                'auth_token' => [
                    'label' => 'Auth Token',
                    'type' => 'text',
                ],
                'from_number' => [
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
            'auth_id' => ['required', 'string', 'max:128'],
            'auth_token' => ['required', 'string', 'max:128'],
            'from_number' => ['required', 'string', 'max:128'],
        ];
    }

    #[Override]
    public function send($to, $content)
    {
        return $this->sendUsingConfig([
            'plivo.auth_id' => $this->model->auth_id, // @phpstan-ignore-line property.notFound
            'plivo.auth_token' => $this->model->auth_token, // @phpstan-ignore-line property.notFound
        ], function() use ($to, $content) {
            $response = resolve(PlivoClient::class)->messages->create([
                'src' => $this->model->from_number,
                'dst' => $to,
                'text' => $content,
            ]);

            if ($response->statusCode !== 202) {
                throw new SystemException(sprintf('SMS message was not sent. Plivo responded with `%s: %s`', $response->statusCode, $response->error));
            }

            return $response;
        });
    }
}
