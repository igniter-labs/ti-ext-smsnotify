<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\AutomationRules\Actions;

use Igniter\Automation\AutomationException;
use Igniter\Automation\Models\RuleAction;
use Igniter\Cart\Models\Order;
use Igniter\User\Models\Customer;
use IgniterLabs\SmsNotify\AutomationRules\Actions\SendSmsNotification;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Template;

it('action details returns expected values', function(): void {
    expect((new SendSmsNotification)->actionDetails())
        ->toBeArray()
        ->toHaveKey('name', 'Send an SMS notification')
        ->toHaveKey('description', 'Send an SMS to a recipient');
});

it('define form fields returns expected structure', function(): void {
    $fields = (new SendSmsNotification)->defineFormFields();

    expect($fields)
        ->toBeArray()
        ->toHaveKey('fields')
        ->and($fields['fields'])
        ->toHaveKey('template')
        ->toHaveKey('send_to')
        ->toHaveKey('custom');
});

it('get template options returns templates from database', function(): void {
    Template::create([
        'code' => 'igniterlabs.smsnotify::_sms.new_order',
        'name' => 'Test Template',
        'content' => 'Test content',
    ]);

    $options = (new SendSmsNotification)->getTemplateOptions();

    expect($options->toArray())
        ->toBeArray()
        ->toHaveKey('igniterlabs.smsnotify::_sms.new_order', 'Test Template');
});

it('get send to options returns predefined options', function(): void {
    $options = (new SendSmsNotification)->getSendToOptions();

    expect($options)
        ->toBeArray()
        ->toHaveCount(5)
        ->toHaveKeys(['location', 'customer', 'order', 'reservation', 'custom']);
});

it('trigger action returns false with invalid params', function(): void {
    expect((new SendSmsNotification)->triggerAction(['invalid' => 'params']))->toBeNull();
});

it('trigger action throws exception with missing template', function(): void {
    $order = Order::factory()->create([
        'customer_id' => Customer::factory(),
    ]);
    $model = new RuleAction;
    $model->template = null;

    expect(fn() => (new SendSmsNotification($model))->triggerAction(['order' => $order]))
        ->toThrow(AutomationException::class, 'SendSmsNotification: Missing a valid mail template');
});

it('trigger action throws exception with missing recipient', closure: function(): void {
    $order = Order::factory()->create([
        'customer_id' => Customer::factory(),
    ]);
    $model = new RuleAction;
    $model->template = 'test_template';
    $model->send_to = 'custom';
    $model->custom = null;

    expect(fn() => (new SendSmsNotification($model))->triggerAction(['order' => $order]))
        ->toThrow(AutomationException::class, 'SendSmsNotification: Missing a valid send to number from the event payload');
});

it('trigger action successfully sends notification', function($sendTo, $expectedNumber): void {
    $order = Order::factory()->create([
        'customer_id' => Customer::factory(),
    ]);
    if (in_array($sendTo, ['order', 'reservation'])) {
        $order->telephone = $expectedNumber;
    }

    if ($sendTo === 'location') {
        $order->location->location_telephone = $expectedNumber;
    }

    if ($sendTo === 'customer') {
        $order->customer->telephone = $expectedNumber;
    }

    $model = new RuleAction;
    $model->template = 'test_template';
    $model->send_to = $sendTo;
    if ($sendTo === 'custom') {
        $model->custom = $expectedNumber;
    }

    $manager = mock(Manager::class);
    $manager->shouldReceive('notify')
        ->once()
        ->with('test_template', $expectedNumber, ['order' => $order], $order->location);

    app()->instance(Manager::class, $manager);

    (new SendSmsNotification($model))->triggerAction(['order' => $order]);
})->with([
    ['location', '1234567890'],
    ['customer', '1122334455'],
    ['order', '0987654321'],
    ['reservation', '0987654321'],
    ['custom', '5556667777'],
]);
