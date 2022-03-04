<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Exception;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Plivo\RestAPI as PlivoClient;

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
                    'path' => '$/igniterlabs/smsnotify/smschannels/plivo/info',
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

    public function send($to, $content)
    {
        $response = $this->client()->send_message([
            'src' => $this->model->from_number,
            'dst' => $to,
            'text' => $content,
        ]);

        if ($response['status'] !== 202) {
            throw new Exception("SMS message was not sent. Plivo responded with `{$response['status']}: {$response['response']['error']}`");
        }

        return $response;
    }

    protected function client(): PlivoClient
    {
        return (new PlivoClient(
            $this->model->auth_id,
            $this->model->auth_token,
        ));
    }
}
