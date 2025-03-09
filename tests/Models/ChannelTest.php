<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\Models;

use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Local\Models\Concerns\Locationable;
use Igniter\Local\Models\Location;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\Tests\Fixtures\TwilioChannel;

it('returns correct config for enabled channel', function(): void {
    Channel::clearStaticCache();
    $channel = Channel::create([
        'code' => 'test_channel',
        'config_data' => ['key' => 'value'],
        'is_enabled' => true,
    ]);

    $config = Channel::getConfig('test_channel');

    expect($config)->toBe(['key' => 'value'])
        ->and($channel->isEnabled())->toBeTrue();

    $default = ['default_key' => 'default_value'];
    $config = Channel::getConfig('non_existent_channel', $default);

    expect($config)->toBe($default);
});

it('returns correct name and description attribute', function(): void {
    Channel::flushEventListeners();
    $channel = Channel::create([
        'code' => 'test_channel',
        'class_name' => TwilioChannel::class,
    ]);
    $channel->applyChannelClass();

    expect($channel->name)->toBe('Twilio')
        ->and($channel->description)->toBe('Send notifications via Twilio');
});

it('throws exception when setting default on disabled channel', function(): void {
    $channel = Channel::create([
        'code' => 'test_channel',
        'is_enabled' => false,
    ]);

    expect(fn() => $channel->makeDefault())->toThrow(ApplicationException::class);
});

it('sets default channel correctly', function(): void {
    $channel = Channel::create([
        'code' => 'test_channel',
        'is_enabled' => true,
    ]);

    $channel->makeDefault();

    $defaultChannel = Channel::getDefault();
    expect($defaultChannel->id)->toBe($channel->id);
    $channel->delete();

    Channel::clearStaticCache();
    $channel = Channel::create([
        'code' => 'test_channel',
        'is_enabled' => true,
    ]);

    $defaultChannel = Channel::getDefault();

    expect($defaultChannel->id)->toBe($channel->id)
        ->and($defaultChannel)->toEqual(Channel::getDefault());
});

it('lists enabled channels correctly', function(): void {
    Channel::clearStaticCache();
    $manager = mock(Manager::class);
    $manager->shouldReceive('listChannels')->andReturn([
        'invalid' => TwilioChannel::class,
        'test_channel_1' => TwilioChannel::class,
        'test_channel_2' => TwilioChannel::class,
    ]);
    app()->instance(Manager::class, $manager);
    Channel::flushEventListeners();
    Channel::create([
        'code' => 'test_channel_1',
        'is_enabled' => true,
    ])->applyChannelClass();

    Channel::create([
        'code' => 'test_channel_2',
        'is_enabled' => false,
    ]);

    Channel::flushEventListeners();
    $channels = Channel::listChannels();

    expect($channels)->toHaveKey('test_channel_1')
        ->and($channels)->not->toHaveKey('test_channel_2');
});

it('syncs all channels correctly', function(): void {
    $manager = mock(Manager::class);
    $manager->shouldReceive('listChannelObjects')->andReturn([
        'test_channel_1' => new TwilioChannel,
        'new_channel' => new TwilioChannel,
    ]);
    app()->instance(Manager::class, $manager);

    Channel::create([
        'code' => 'test_channel_1',
        'is_enabled' => true,
    ])->applyChannelClass();

    Channel::syncAll();

    $channel = Channel::where('code', 'new_channel')->first();

    expect($channel)->not->toBeNull()
        ->and($channel->name)->toBe('Twilio')
        ->and($channel->description)->toBe('Send notifications via Twilio');
});

it('configures channel model correctly', function(): void {
    $channel = new Channel;

    expect(class_uses_recursive($channel))
        ->toContain(Locationable::class)
        ->toContain(Purgeable::class)
        ->and($channel->getTable())->toBe('igniterlabs_smsnotify_channels')
        ->and($channel->getKeyName())->toBe('id')
        ->and($channel->timestamps)->toBeTrue()
        ->and($channel->getFillable())->toBe(['id', 'name', 'description', 'code', 'class_name', 'config_data', 'is_enabled', 'is_default', 'location_id'])
        ->and($channel->relation['belongsTo']['location'])->toBe(Location::class)
        ->and($channel->getCasts())->toBe([
            'id' => 'int',
            'config_data' => 'array',
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
        ])
        ->and($channel->getPurgeableAttributes())->toBe(['channel']);
});
