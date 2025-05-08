<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\SmsChannels;

use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\SmsChannels\Clickatell;
use Illuminate\Support\Facades\Http;

it('returns correct channel details', function(): void {
    $clickatellChannel = new Clickatell;

    $details = $clickatellChannel->channelDetails();

    expect($details)->toBeArray()
        ->and($details['name'])->toBe('igniterlabs.smsnotify::default.clickatell.text_title')
        ->and($details['description'])->toBe('igniterlabs.smsnotify::default.clickatell.text_desc');
});

it('returns correct form config', function(): void {
    $clickatellChannel = new Clickatell;

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
    $clickatellChannel = new Clickatell;

    $rules = $clickatellChannel->getConfigRules();

    expect($rules)->toBeArray()
        ->and($rules['api_key'])->toContain('required', 'string', 'max:128')
        ->and($rules['api_id'])->toContain('required', 'string', 'max:128');
});

it('sends message successfully', function(): void {
    Http::fake([
        'https://platform.clickatell.com/*' => Http::response([], 202),
    ]);

    $channel = new Channel;
    $channel->forceFill([
        'api_key' => 'test_api_key',
        'api_id' => 'test_api_id',
    ]);

    expect(fn() => (new Clickatell($channel))->send('+1234567890', 'Test message'))->not->toThrow(SystemException::class);
});

it('throws exception on failed message send', function(): void {
    Http::fake([
        'https://platform.clickatell.com/*' => Http::response([
            'messages' => [
                [
                    'error' => [
                        'code' => 1,
                        'description' => 'Some error',
                    ],
                ],
            ],
        ], 400),
    ]);

    $channel = new Channel;
    $channel->forceFill([
        'api_key' => 'test_api_key',
        'api_id' => 'test_api_id',
    ]);

    expect(fn() => (new Clickatell($channel))->send('+1234567890', 'Test message'))
        ->toThrow(SystemException::class, 'Clickatell responded with an error: 1: Some error');
});
