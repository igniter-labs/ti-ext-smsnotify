<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\Classes;

use Igniter\System\Classes\ExtensionManager;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\Models\Template;
use IgniterLabs\SmsNotify\Tests\Fixtures\TwilioChannel;

beforeEach(function(): void {
    $this->extensionManager = mock(ExtensionManager::class);
    app()->instance(ExtensionManager::class, $this->extensionManager);
    $this->manager = new Manager();
});

it('lists registered channels', function(): void {
    $this->extensionManager->shouldReceive('getRegistrationMethodValues')
        ->with('registerSmsChannels')
        ->andReturn([
            'test.extension' => [
                'twilio' => TwilioChannel::class,
                'invalid-class' => 'InvalidClass',
            ],
        ]);

    expect($this->manager->listChannels())->toBeArray()->toHaveKey('twilio')
        ->and($this->manager->listChannelObjects())->toBeArray()->toHaveKey('twilio');
});

it('gets a specific channel', function(): void {
    $this->extensionManager->shouldReceive('getRegistrationMethodValues')
        ->with('registerSmsChannels')
        ->andReturn([
            'test.extension' => [
                'twilio' => TwilioChannel::class,
            ],
        ]);

    expect($this->manager->getChannel('twilio'))->toBe(TwilioChannel::class);
});

it('lists registered templates', function(): void {
    $this->extensionManager->shouldReceive('getRegistrationMethodValues')
        ->with('registerSmsTemplates')
        ->andReturn([
            'test.extension' => [
                'order_confirmation' => 'Order Confirmation Template',
                'password_reset' => 'Password Reset Template',
            ],
        ]);

    $templates = $this->manager->listTemplates();

    expect($templates)
        ->toBeArray()
        ->toHaveKey('order_confirmation', 'Order Confirmation Template')
        ->toHaveKey('password_reset', 'Password Reset Template')
        ->and($this->manager->getTemplate('order_confirmation'))->toBe('Order Confirmation Template');
});

it('resolves template code from code or class', function(): void {
    $this->extensionManager->shouldReceive('getRegistrationMethodValues')
        ->with('registerSmsTemplates')
        ->andReturn([
            'test.extension' => [
                'order_confirmation' => 'Order Confirmation Template',
            ],
        ]);

    // Test resolving by code
    $template = $this->manager->resolveTemplateCode('order_confirmation');
    expect($template)->toBe('Order Confirmation Template');

    // Test resolving by template name
    $template = $this->manager->resolveTemplateCode('Order Confirmation Template');
    expect($template)->toBe('Order Confirmation Template');
});

it('builds content from a template', function(): void {
    $data = [
        'order_type' => 'Delivery',
        'location_name' => 'Location Name',
        'order_date' => '2023-10-01',
        'order_time' => '12:00:00',
    ];

    $content = $this->manager->buildContent('igniterlabs.smsnotify::_sms.new_order', $data);

    expect($content)->toBe('New Delivery order at Location Name for 2023-10-01 at 12:00:00')
        ->and($content)->toBe($this->manager->buildContent('igniterlabs.smsnotify::_sms.new_order', $data)); // Cache hit
});

it('renders a template with provided data', function(): void {
    $template = new Template();
    $template->content = 'Hello {{$name}}, welcome to {{$site_name}}!';

    $data = [
        'name' => 'John',
        'site_name' => 'Our Website',
    ];

    $content = $this->manager->renderTemplate($template, $data);

    expect($content)->toBe('Hello John, welcome to Our Website!');
});

it('sends a notification to a recipient', function(): void {
    Channel::flushEventListeners();
    $channel = new Channel([
        'name' => 'Twilio',
        'description' => 'Description',
        'code' => 'twilio',
        'class_name' => TwilioChannel::class,
        'config_data' => [],
        'is_enabled' => true,
        'is_default' => true,
    ]);
    $channel->save();

    expect($this->manager->notify('igniterlabs.smsnotify::_sms.new_order', '1234567890', ['name' => 'John']))->toBeNull();
});
