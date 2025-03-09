<?php

namespace IgniterLabs\SmsNotify\Tests\Http\Requests;

use IgniterLabs\SmsNotify\Http\Requests\TemplateRequest;

it('returns correct attribute labels', function() {
    $request = new TemplateRequest();

    expect($request->attributes())
        ->toHaveKey('name', lang('igniter::admin.label_name'))
        ->toHaveKey('content', lang('igniterlabs.smsnotify::default.template.label_content'));
});

it('returns correct validation rules', function() {
    $request = new TemplateRequest();

    expect($request->rules())
        ->toHaveKey('name', ['required', 'string', 'max:128'])
        ->toHaveKey('content', ['required', 'string']);
});
