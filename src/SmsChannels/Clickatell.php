<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Clickatell\Rest as ClickatellClient;
use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Classes\BaseChannel;

class Clickatell extends BaseChannel
{
    protected const int SUCCESSFUL_SEND = 0;

    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.clickatell.text_title',
            'description' => 'igniterlabs.smsnotify::default.clickatell.text_desc',
        ];
    }

    public function defineFormConfig()
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

    public function getConfigRules()
    {
        return [
            'api_key' => ['required', 'string', 'max:128'],
            'api_id' => ['required', 'string', 'max:128'],
        ];
    }

    public function send($to, $content)
    {
        app()->resolving(ClickatellClient::class, function() {
            config([
                'igniterlabs-smsnotify.clickatell.api_key' => $this->model->api_key,
            ]);
        });

        $responses = resolve(ClickatellClient::class)->sendMessage([
            'to' => [$to],
            'content' => $content,
        ]);

        collect($responses)->each(function($response) {
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
