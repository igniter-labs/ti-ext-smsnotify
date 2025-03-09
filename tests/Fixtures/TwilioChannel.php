<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\Fixtures;

use Override;
use IgniterLabs\SmsNotify\Classes\BaseChannel;

class TwilioChannel extends BaseChannel
{
    #[Override]
    public function send($to, $content): string
    {
        return sprintf('Sent via Twilio to %s: %s', $to, $content);
    }

    #[Override]
    public function defineFormConfig(): string
    {
        return __DIR__.'/../_fixtures/fields';
    }

    #[Override]
    public function channelDetails(): array
    {
        return [
            'name' => 'Twilio',
            'description' => 'Send notifications via Twilio',
        ];
    }
}
