<?php

namespace IgniterLabs\SmsNotify\Tests\Http\Controllers;

use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\SmsChannels\Twilio;
use IgniterLabs\SmsNotify\Tests\Fixtures\TwilioChannel;

it('loads channels page', function() {
    actingAsSuperUser()
        ->get(route('igniterlabs.smsnotify.channels'))
        ->assertOk();
});

it('loads create channel page', function() {
    actingAsSuperUser()
        ->get(route('igniterlabs.smsnotify.channels', ['slug' => 'create']))
        ->assertOk();
});

it('loads edit channel page', function() {
    Channel::flushEventListeners();
    $channel = Channel::create([
        'name' => 'Twilio',
        'description' => 'Description',
        'code' => 'twilio',
        'class_name' => TwilioChannel::class,
        'config_data' => [],
        'is_enabled' => true,
        'is_default' => true,
    ]);

    actingAsSuperUser()
        ->get(route('igniterlabs.smsnotify.channels', ['slug' => 'edit/'.$channel->getKey()]))
        ->assertOk();
});

it('loads channel preview page', function(): void {
    Channel::flushEventListeners();
    $channel = Channel::create([
        'name' => 'Twilio',
        'description' => 'Description',
        'code' => 'twilio',
        'class_name' => TwilioChannel::class,
        'config_data' => [],
        'is_enabled' => true,
        'is_default' => true,
    ]);

    actingAsSuperUser()
        ->get(route('igniterlabs.smsnotify.channels', ['slug' => 'preview/'.$channel->getKey()]))
        ->assertOk();
});

it('creates channel', function() {
    actingAsSuperUser()
        ->post(route('igniterlabs.smsnotify.channels', ['slug' => 'create']), [
            'Channel' => [
                'channel' => 'twilio',
                'name' => 'Twilio',
                'code' => 'twilio',
                'description' => 'Description',
                'is_enabled' => true,
                'is_default' => true,
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Channel::where('class_name', Twilio::class)->exists())->toBeTrue();

});

it('updates channel', function() {
    Channel::flushEventListeners();
    $channel = Channel::create([
        'name' => 'Twilio',
        'description' => 'Description',
        'code' => 'twilio',
        'class_name' => TwilioChannel::class,
        'config_data' => [],
        'is_enabled' => true,
        'is_default' => false,
    ]);

    actingAsSuperUser()
        ->post(route('igniterlabs.smsnotify.channels', ['slug' => 'edit/'.$channel->getKey()]), [
            'Channel' => [
                'name' => 'Updated Twilio',
                'description' => 'Updated Description',
                'code' => 'updated_twilio',
                'is_enabled' => '1',
                'is_default' => '1',
                'test_field' => 'test_value',
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Channel::where('description', 'Updated Description')->exists())->toBeTrue();
});

it('deletes channel', function() {
    Channel::flushEventListeners();
    $channel = Channel::create([
        'name' => 'Twilio',
        'description' => 'Description',
        'code' => 'twilio',
        'class_name' => TwilioChannel::class,
        'config_data' => [],
        'is_enabled' => '1',
        'is_default' => '1',
    ]);

    actingAsSuperUser()
        ->post(route('igniterlabs.smsnotify.channels', ['slug' => 'edit/'.$channel->getKey()]), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(Channel::where('id', $channel->getKey())->exists())->toBeFalse();
});
