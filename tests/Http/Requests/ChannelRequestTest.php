<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\Http\Requests;

use IgniterLabs\SmsNotify\Http\Requests\ChannelRequest;

it('returns correct attribute labels', function(): void {
    $request = new ChannelRequest;

    expect($request->attributes())
        ->toHaveKey('channel', lang('igniterlabs.smsnotify::default.channel.label_channel'))
        ->toHaveKey('name', lang('igniterlabs.smsnotify::default.channel.label_label'))
        ->toHaveKey('location_id', lang('igniter::admin.label_location'))
        ->toHaveKey('code', lang('igniterlabs.smsnotify::default.channel.label_code'))
        ->toHaveKey('description', lang('igniter::admin.label_description'))
        ->toHaveKey('is_default', lang('igniter.payregister::default.label_default'))
        ->toHaveKey('is_enabled', lang('igniter::admin.label_status'));
});

it('returns correct validation rules', function(): void {
    $request = new ChannelRequest;

    expect($request->rules())
        ->toHaveKey('channel', ['sometimes', 'required', 'alpha_dash'])
        ->toHaveKey('name', ['sometimes', 'required', 'min:2', 'max:128'])
        ->toHaveKey('code', ['sometimes', 'required', 'alpha_dash', 'unique:igniterlabs_smsnotify_channels,code'])
        ->toHaveKey('description', ['sometimes', 'required', 'max:255'])
        ->toHaveKey('is_default', ['required', 'integer'])
        ->toHaveKey('is_enabled', ['required', 'integer']);
});
