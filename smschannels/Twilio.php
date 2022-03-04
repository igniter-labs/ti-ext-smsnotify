<?php

namespace IgniterLabs\SmsNotify\SmsChannels;

use Exception;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Twilio\Rest\Client as TwilioClient;

class Twilio extends BaseChannel
{
    public function channelDetails()
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.twilio.text_title',
            'description' => 'igniterlabs.smsnotify::default.twilio.text_desc',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => '$/igniterlabs/smsnotify/smschannels/twilio/info',
                ],
                'account_sid' => [
                    'label' => 'Account SID',
                    'type' => 'text',
                ],
                'auth_token' => [
                    'label' => 'Auth Token',
                    'type' => 'text',
                ],
                'from' => [
                    'label' => 'Send From Number',
                    'type' => 'text',
                ],
            ],
        ];
    }

    public function send($to, $content)
    {
        $params = [
            'body' => trim($content),
        ];

        if (strlen($this->model->service_sid))
            $params['messagingServiceSid'] = $this->model->service_sid;

        if (strlen($this->model->from))
            $params['from'] = $this->model->from;

        if (empty($params['from']) && empty($params['messagingServiceSid'])) {
            throw new Exception('SMS message was not sent. Missing `from` number.');
        }

        $this->fillOptionalParams($params, [
            'statusCallback',
            'statusCallbackMethod',
            'applicationSid',
            'forceDelivery',
            'maxPrice',
            'provideFeedback',
            'validityPeriod',
        ]);

        return (new TwilioClient(
            $this->model->account_sid,
            $this->model->auth_token
        ))->messages->create($to, $params);
    }

    protected function fillOptionalParams(&$params, $optionalParams): self
    {
        return $this;
    }
}
