<?php

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\SmsChannels\Vonage;

it('returns correct channel details', function() {
    $vonageChannel = new Vonage();

    $details = $vonageChannel->channelDetails();

    expect($details)->toBeArray()
        ->and($details['name'])->toBe('igniterlabs.smsnotify::default.vonage.text_title')
        ->and($details['description'])->toBe('igniterlabs.smsnotify::default.vonage.text_desc');
});

it('returns correct form config', function() {
    $vonageChannel = new Vonage();

    $config = $vonageChannel->defineFormConfig();

    expect($config)->toBeArray()
        ->and($config['fields'])->toHaveKey('setup')
        ->and($config['fields']['setup']['type'])->toBe('partial')
        ->and($config['fields']['setup']['path'])->toBe('nexmo/info')
        ->and($config['fields'])->toHaveKey('api_key')
        ->and($config['fields']['api_key']['label'])->toBe('API Key')
        ->and($config['fields']['api_key']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('api_secret')
        ->and($config['fields']['api_secret']['label'])->toBe('API Secret')
        ->and($config['fields']['api_secret']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('send_from')
        ->and($config['fields']['send_from']['label'])->toBe('Send From Number')
        ->and($config['fields']['send_from']['type'])->toBe('text');
});

it('returns correct config rules', function() {
    $vonageChannel = new Vonage();

    $rules = $vonageChannel->getConfigRules();

    expect($rules)->toBeArray()
        ->and($rules['api_key'])->toContain('required', 'string', 'max:128')
        ->and($rules['api_secret'])->toContain('required', 'string', 'max:128')
        ->and($rules['send_from'])->toContain('required', 'string', 'max:128');
});

it('sends message successfully', function() {
    $vonageClient = Mockery::mock(\Vonage\Client::class);
    $vonageClient->shouldReceive('message->send')->withArgs(function($payload) {
        return $payload['type'] === 'text' &&
            $payload['from'] === '12345' &&
            $payload['to'] === '67890' &&
            $payload['text'] === 'Test message' &&
            $payload['client-ref'] === '';
    })->andReturn(['message-id' => 'abc123']);
    app()->singleton(\Vonage\Client::class, fn() => $vonageClient);

    $channel = new Channel();
    $channel->forceFill([
        'api_key' => 'test_api_key',
        'api_secret' => 'test_api_secret',
        'send_from' => '12345',
        'status_callback' => function() {},
    ]);
    $vonageChannel = new Vonage($channel);

    $response = $vonageChannel->send('67890', 'Test message');

    expect($response['message-id'])->toBe('abc123');
});

it('throws exception when api credentials are missing', function() {
    $channel = new Channel();
    $channel->forceFill([
        'api_key' => '',
        'api_secret' => '',
        'send_from' => '12345',
    ]);
    $vonageChannel = new Vonage($channel);

    expect(fn() => $vonageChannel->send('67890', 'Test message'))
        ->toThrow(SystemException::class, 'Please provide your Vonage API credentials. api_key + api_secret');
});
