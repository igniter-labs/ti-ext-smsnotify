<?php

namespace IgniterLabs\SmsNotify\Tests\SmsChannels;

use Aws\Sns\SnsClient;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\SmsChannels\Aws;

it('returns correct channel details', function() {
    $awsChannel = new Aws();

    $details = $awsChannel->channelDetails();

    expect($details)->toBeArray()
        ->and($details['name'])->toBe('igniterlabs.smsnotify::default.aws.text_title')
        ->and($details['description'])->toBe('igniterlabs.smsnotify::default.aws.text_desc');
});

it('returns correct form config', function() {
    $awsChannel = new Aws();

    $config = $awsChannel->defineFormConfig();

    expect($config)->toBeArray()
        ->and($config['fields'])->toHaveKey('setup')
        ->and($config['fields']['setup']['type'])->toBe('partial')
        ->and($config['fields']['setup']['path'])->toBe('aws/info')
        ->and($config['fields'])->toHaveKey('key')
        ->and($config['fields']['key']['label'])->toBe('Key')
        ->and($config['fields']['key']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('secret')
        ->and($config['fields']['secret']['label'])->toBe('Secret')
        ->and($config['fields']['secret']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('country_code')
        ->and($config['fields']['country_code']['label'])->toBe('Default country code')
        ->and($config['fields']['country_code']['type'])->toBe('text');
});

it('returns correct config rules', function() {
    $awsChannel = new Aws();

    $rules = $awsChannel->getConfigRules();

    expect($rules)->toBeArray()
        ->and($rules['key'])->toContain('required', 'string', 'max:128')
        ->and($rules['secret'])->toContain('required', 'string', 'max:128')
        ->and($rules['country_code'])->toContain('required', 'string', 'max:128');
});

it('sends message with default country code', function() {
    $snsClient = mock(SnsClient::class);
    $snsClient->shouldReceive('publish')->once()->with([
        'Message' => 'Test message',
        'PhoneNumber' => '+1234567890',
    ])->andReturn(true);
    app()->singleton(SnsClient::class, fn() => $snsClient);

    $channel = new Channel();
    $channel->forceFill([
        'key' => 'test_key',
        'secret' => 'test_secret',
        'country_code' => '+1',
    ]);
    $awsChannel = new Aws($channel);

    $awsChannel->send('234567890', 'Test message');
});

it('sends message with provided country code', function() {
    $snsClient = mock(SnsClient::class);
    $snsClient->shouldReceive('publish')->once()->with([
        'Message' => 'Test message',
        'PhoneNumber' => '+1234567890',
    ])->andReturn(true);
    app()->singleton(SnsClient::class, fn() => $snsClient);

    $channel = new Channel();
    $channel->forceFill([
        'key' => 'test_key',
        'secret' => 'test_secret',
        'country_code' => '+1',
    ]);
    $awsChannel = new Aws($channel);

    $awsChannel->send('+1234567890', 'Test message');
});
