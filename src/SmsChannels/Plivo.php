<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Plivo\RestClient as PlivoClient;

class Plivo extends BaseChannel
{
    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.plivo.text_title',
            'description' => 'igniterlabs.smsnotify::default.plivo.text_desc',
        ];
    }

    public function defineFormConfig()
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

    public function getConfigRules()
    {
        return [
            'auth_id' => ['required', 'string', 'max:128'],
            'auth_token' => ['required', 'string', 'max:128'],
            'from_number' => ['required', 'string', 'max:128'],
        ];
    }

    public function send($to, $content)
    {
        app()->resolving(PlivoClient::class, function() {
            config([
                'igniterlabs-smsnotify.plivo.auth_id' => $this->model->auth_id,
                'igniterlabs-smsnotify.plivo.auth_token' => $this->model->auth_token,
            ]);
        });

        $response = resolve(PlivoClient::class)->messages->create([
            'src' => $this->model->from_number,
            'dst' => $to,
            'text' => $content,
        ]);

        if ($response['status'] !== 202) {
            throw new SystemException("SMS message was not sent. Plivo responded with `{$response['status']}: {$response['response']['error']}`");
        }

        return $response;
    }
}
