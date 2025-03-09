<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\Classes;

use IgniterLabs\SmsNotify\Classes\BaseChannel;
use IgniterLabs\SmsNotify\Models\Channel;

it('initializes correctly', function(): void {
    $channel = new class extends BaseChannel
    {
        public function defineFormConfig(): string
        {
            return __DIR__.'/../_fixtures/fields';
        }

        public function send($to, $content): void {}
    };

    expect($channel->getName())->toBe('Notification channel')
        ->and($channel->getDescription())->toBe('Notification channel description')
        ->and($channel->channelDetails())
        ->toHaveKey('name', 'Notification channel')
        ->toHaveKey('description', 'Notification channel description')
        ->and($channel->getConfigFields())->toBeArray()
        ->and($channel->getConfigRules())->toBeArray();
});

it('send method is abstract and must be implemented', function(): void {
    $model = new Channel;
    $channel = new class($model) extends BaseChannel
    {
        public function defineFormConfig(): string
        {
            parent::defineFormConfig();

            return __DIR__.'/../_fixtures/fields';
        }

        public function send($to, $content): string
        {
            return sprintf('Sending to %s: %s', $to, $content);
        }
    };

    expect($channel->send('1234567890', 'Test message'))->toBe('Sending to 1234567890: Test message');
});
