<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\SmsChannels;

use Override;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use Twilio\Rest\Client as TwilioClient;

class Twilio extends BaseChannel
{
    #[Override]
    public function channelDetails(): array
    {
        return [
            'name' => 'igniterlabs.smsnotify::default.twilio.text_title',
            'description' => 'igniterlabs.smsnotify::default.twilio.text_desc',
        ];
    }

    #[Override]
    public function defineFormConfig(): array
    {
        return [
            'fields' => [
                'setup' => [
                    'type' => 'partial',
                    'path' => 'twilio/info',
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

    #[Override]
    public function getConfigRules(): array
    {
        return [
            'account_sid' => ['required', 'string', 'max:128'],
            'auth_token' => ['required', 'string', 'max:128'],
            'from' => ['required', 'string', 'max:128'],
        ];
    }

    #[Override]
    public function send($to, $content): MessageInstance
    {
        $params = [
            'body' => trim((string) $content),
        ];

        if (strlen($this->model->service_sid) !== 0) {
            $params['messagingServiceSid'] = $this->model->service_sid;
        }

        if (strlen($this->model->from) !== 0) {
            $params['from'] = $this->model->from;
        }

        if (empty($params['from']) && empty($params['messagingServiceSid'])) {
            throw new SystemException('SMS message was not sent. Missing `from` number.');
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

        config([
            'igniterlabs-smsnotify.twilio.account_sid' => $this->model->auth_id, // @phpstan-ignore-line property.notFound
            'igniterlabs-smsnotify.twilio.auth_token' => $this->model->auth_token, // @phpstan-ignore-line property.notFound
        ]);

        return resolve(TwilioClient::class)->messages->create($to, $params);
    }

    protected function fillOptionalParams(&$params, $optionalParams): self
    {
        return $this;
    }
}
