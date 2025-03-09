<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\SmsChannels;

use Clickatell\Rest;
use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\SmsChannels\Clickatell;

it('returns correct channel details', function(): void {
    $clickatellChannel = new Clickatell();

    $details = $clickatellChannel->channelDetails();

    expect($details)->toBeArray()
        ->and($details['name'])->toBe('igniterlabs.smsnotify::default.clickatell.text_title')
        ->and($details['description'])->toBe('igniterlabs.smsnotify::default.clickatell.text_desc');
});

it('returns correct form config', function(): void {
    $clickatellChannel = new Clickatell();

    $config = $clickatellChannel->defineFormConfig();

    expect($config)->toBeArray()
        ->and($config['fields'])->toHaveKey('setup')
        ->and($config['fields']['setup']['type'])->toBe('partial')
        ->and($config['fields']['setup']['path'])->toBe('clickatell/info')
        ->and($config['fields'])->toHaveKey('api_key')
        ->and($config['fields']['api_key']['label'])->toBe('API Key')
        ->and($config['fields']['api_key']['type'])->toBe('text')
        ->and($config['fields'])->toHaveKey('api_id')
        ->and($config['fields']['api_id']['label'])->toBe('API ID')
        ->and($config['fields']['api_id']['type'])->toBe('text');
});

it('returns correct config rules', function(): void {
    $clickatellChannel = new Clickatell();

    $rules = $clickatellChannel->getConfigRules();

    expect($rules)->toBeArray()
        ->and($rules['api_key'])->toContain('required', 'string', 'max:128')
        ->and($rules['api_id'])->toContain('required', 'string', 'max:128');
});

it('sends message successfully', function(): void {
    $clickatellClient = mock(Rest::class);
    $clickatellClient->shouldReceive('sendMessage')->once()->with([
        'to' => ['+1234567890'],
        'content' => 'Test message',
    ])->andReturn([['errorCode' => 0]]);
    app()->singleton(Rest::class, fn() => $clickatellClient);

    $channel = new Channel();
    $channel->forceFill([
        'api_key' => 'test_api_key',
        'api_id' => 'test_api_id',
    ]);
    $clickatellChannel = new Clickatell($channel);

    $clickatellChannel->send('+1234567890', 'Test message');
});

it('throws exception on failed message send', function(): void {
    $clickatellClient = mock(Rest::class);
    $clickatellClient->shouldReceive('sendMessage')->once()->with([
        'to' => ['+1234567890'],
        'content' => 'Test message',
    ])->andReturn([['errorCode' => 1, 'error' => 'Some error']]);
    app()->singleton(Rest::class, fn() => $clickatellClient);

    $channel = new Channel();
    $channel->forceFill([
        'api_key' => 'test_api_key',
        'api_id' => 'test_api_id',
    ]);
    $clickatellChannel = new Clickatell($channel);

    expect(fn() => $clickatellChannel->send('+1234567890', 'Test message'))
        ->toThrow(SystemException::class, "Clickatell responded with an error 'Some error: 1'");
});
