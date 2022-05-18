<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Clickatell\Rest as ClickatellClient;
use Exception;
use IgniterLabs\SmsNotify\Classes\BaseChannel;

class Clickatell extends BaseChannel
{
    const SUCCESSFUL_SEND = 0;

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
                    'path' => '$/igniterlabs/smsnotify/smschannels/clickatell/info',
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

    public function send($to, $content)
    {
        $responses = (new ClickatellClient($this->model->api_key))
            ->sendMessage([
                'to' => [$to],
                'content' => $content,
            ]);

        collect($responses)->each(function ($response) {
            $errorCode = (int)array_get($response, 'errorCode');

            if ($errorCode != self::SUCCESSFUL_SEND) {
                throw new Exception(sprintf("Clickatell responded with an error '{%s}: {%s}'",
                    (string)array_get($response, 'error'),
                    $errorCode
                ));
            }
        });
    }
}
