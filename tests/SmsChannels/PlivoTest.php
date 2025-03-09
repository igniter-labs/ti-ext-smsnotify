<?php

namespace IgniterLabs\SmsNotify\Tests\SmsChannels;

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\SmsChannels\Plivo;
use Plivo\Resources\Message\MessageInterface;

it('returns correct channel details', function() {
    $plivoChannel = new Plivo();

    $details = $plivoChannel->channelDetails();

    expect($details)->toBeArray()
        ->and($details['name'])->toBe('igniterlabs.smsnotify::default.plivo.text_title')
        ->and($details['description'])->toBe('igniterlabs.smsnotify::default.plivo.text_desc');
});

it('returns correct form config', function() {
    $plivoChannel = new Plivo();

    $config = $plivoChannel->defineFormConfig();

    expect($config)->toBeArray()
        ->and($config['fields'])->toHaveKey('setup')
        ->and($config['fields']['setup']['type'])->toBe('partial')
        ->and($config['fields']['setup']['path'])->toBe('plivo/info')
        ->and($config['fields'])->toHaveKey('auth_id')
        ->and($config['fields']['auth_id']['label'])->toBe('Auth ID')
        ->and($config['fields']['auth_id']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('auth_token')
        ->and($config['fields']['auth_token']['label'])->toBe('Auth Token')
        ->and($config['fields']['auth_token']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('from_number')
        ->and($config['fields']['from_number']['label'])->toBe('Send From Number')
        ->and($config['fields']['from_number']['type'])->toBe('text');
});

it('returns correct config rules', function() {
    $plivoChannel = new Plivo();

    $rules = $plivoChannel->getConfigRules();

    expect($rules)->toBeArray()
        ->and($rules['auth_id'])->toContain('required', 'string', 'max:128')
        ->and($rules['auth_token'])->toContain('required', 'string', 'max:128')
        ->and($rules['from_number'])->toContain('required', 'string', 'max:128');
});

it('sends message successfully', function() {
    $plivoClient = mock(\Plivo\RestClient::class)->shouldAllowMockingProtectedMethods();
    $plivoClient->shouldReceive('getMessages')->andReturn($messages = mock(MessageInterface::class));
    $messages->shouldReceive('create')->with([
        'src' => '12345',
        'dst' => '67890',
        'text' => 'Test message',
    ])->andReturn(['status' => 202]);
    app()->singleton(\Plivo\RestClient::class, fn() => $plivoClient);

    $channel = new Channel();
    $channel->forceFill([
        'auth_id' => 'test_auth_id',
        'auth_token' => 'test_auth_token',
        'from_number' => '12345',
    ]);
    $plivoChannel = new Plivo($channel);

    $response = $plivoChannel->send('67890', 'Test message');

    expect($response['status'])->toBe(202);
});

it('throws exception on failed message send', function() {
    $plivoClient = mock(\Plivo\RestClient::class)->shouldAllowMockingProtectedMethods();
    $plivoClient->shouldReceive('getMessages')->andReturn($messages = mock(MessageInterface::class));
    $messages->shouldReceive('create')->with([
        'src' => '12345',
        'dst' => '67890',
        'text' => 'Test message',
    ])->andReturn(['status' => 400, 'response' => ['error' => 'Some error']]);
    app()->singleton(\Plivo\RestClient::class, fn() => $plivoClient);

    $channel = new Channel();
    $channel->forceFill([
        'auth_id' => 'test_auth_id',
        'auth_token' => 'test_auth_token',
        'from_number' => '12345',
    ]);
    $plivoChannel = new Plivo($channel);

    expect(fn() => $plivoChannel->send('67890', 'Test message'))
        ->toThrow(SystemException::class, 'SMS message was not sent. Plivo responded with `400: Some error`');
});
