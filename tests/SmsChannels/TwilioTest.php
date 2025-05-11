<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\SmsChannels;

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\SmsChannels\Twilio;
use Twilio\Rest\Api;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;

it('returns correct channel details', function(): void {
    $twilioChannel = new Twilio;

    $details = $twilioChannel->channelDetails();

    expect($details)->toBeArray()
        ->and($details['name'])->toBe('igniterlabs.smsnotify::default.twilio.text_title')
        ->and($details['description'])->toBe('igniterlabs.smsnotify::default.twilio.text_desc');
});

it('returns correct form config', function(): void {
    $twilioChannel = new Twilio;

    $config = $twilioChannel->defineFormConfig();

    expect($config)->toBeArray()
        ->and($config['fields'])->toHaveKey('setup')
        ->and($config['fields']['setup']['type'])->toBe('partial')
        ->and($config['fields']['setup']['path'])->toBe('twilio/info')
        ->and($config['fields'])->toHaveKey('account_sid')
        ->and($config['fields']['account_sid']['label'])->toBe('Account SID')
        ->and($config['fields']['account_sid']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('auth_token')
        ->and($config['fields']['auth_token']['label'])->toBe('Auth Token')
        ->and($config['fields']['auth_token']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('from')
        ->and($config['fields']['from']['label'])->toBe('Send From Number')
        ->and($config['fields']['from']['type'])->toBe('text');
});

it('returns correct config rules', function(): void {
    $twilioChannel = new Twilio;

    $rules = $twilioChannel->getConfigRules();

    expect($rules)->toBeArray()
        ->and($rules['account_sid'])->toContain('required', 'string', 'max:128')
        ->and($rules['auth_token'])->toContain('required', 'string', 'max:128')
        ->and($rules['from'])->toContain('required', 'string', 'max:128');
});

it('sends message successfully with from number', function(): void {
    /** @var Client $twilioClient */
    $twilioClient = mock(Client::class)->shouldAllowMockingProtectedMethods();
    $twilioClient->shouldReceive('getMessages')->andReturn($messages = mock(MessageList::class));
    $messages->shouldReceive('create')->with('+1234567890', [
        'body' => 'Test message',
        'messagingServiceSid' => 'test_service_sid',
        'from' => '12345',
    ])->andReturn(new MessageInstance(new V2010(new Api($twilioClient)), [
        'sid' => 'SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
    ], 'test_account_sid'));
    app()->singleton(Client::class, fn() => $twilioClient);

    $channel = new Channel;
    $channel->forceFill([
        'account_sid' => 'test_account_sid',
        'auth_token' => 'test_auth_token',
        'from' => '12345',
        'service_sid' => 'test_service_sid',
    ]);
    $twilioChannel = new Twilio($channel);

    $response = $twilioChannel->send('+1234567890', 'Test message');

    expect($response->sid)->toBe('SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
});

it('throws exception when from number and service sid are missing', function(): void {
    $channel = new Channel;
    $channel->forceFill([
        'account_sid' => 'test_account_sid',
        'auth_token' => 'test_auth_token',
        'from' => '',
        'service_sid' => '',
    ]);
    $twilioChannel = new Twilio($channel);

    expect(fn(): MessageInstance => $twilioChannel->send('+1234567890', 'Test message'))
        ->toThrow(SystemException::class, 'SMS message was not sent. Missing `from` number or `messagingServiceSid`.');
});
