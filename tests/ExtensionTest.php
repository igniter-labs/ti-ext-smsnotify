<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests;

use Aws\Sns\SnsClient;
use Clickatell\Rest as ClickatellClient;
use Igniter\Cart\AutomationRules\Events\NewOrderStatus;
use IgniterLabs\SmsNotify\AutomationRules\Actions\SendSmsNotification;
use IgniterLabs\SmsNotify\Extension;
use IgniterLabs\SmsNotify\SmsChannels\AwsSns;
use IgniterLabs\SmsNotify\SmsChannels\Clickatell;
use IgniterLabs\SmsNotify\SmsChannels\Plivo;
use IgniterLabs\SmsNotify\SmsChannels\Twilio;
use IgniterLabs\SmsNotify\SmsChannels\Vonage;
use Plivo\RestClient as PlivoClient;
use Twilio\Rest\Client as TwilioClient;
use Vonage\Client as VonageClient;

it('resolves aws sns client correctly', function(): void {
    app()->resolving(SnsClient::class, function(): void {
        config([
            'igniterlabs-smsnotify.aws.key' => 'test_key',
            'igniterlabs-smsnotify.aws.secret' => 'test_secret',
        ]);
    });

    expect(resolve(SnsClient::class))->toBeInstanceOf(SnsClient::class);
});

it('resolves clickatell client correctly', function(): void {
    app()->resolving(ClickatellClient::class, function(): void {
        config([
            'igniterlabs-smsnotify.clickatell.api_key' => 'test_api_key',
            'igniterlabs-smsnotify.clickatell.api_id' => 'test_api_id',
        ]);
    });

    expect(resolve(ClickatellClient::class))->toBeInstanceOf(ClickatellClient::class);
});

it('resolves plivo client correctly', function(): void {
    app()->resolving(PlivoClient::class, function(): void {
        config([
            'igniterlabs-smsnotify.plivo.auth_id' => 'test_auth_id',
            'igniterlabs-smsnotify.plivo.auth_token' => 'test_auth_token',
        ]);
    });

    expect(resolve(PlivoClient::class))->toBeInstanceOf(PlivoClient::class);
});

it('resolves vonage client correctly', function(): void {
    app()->resolving(VonageClient::class, function(): void {
        config([
            'igniterlabs-smsnotify.vonage.api_key' => 'test_api_key',
            'igniterlabs-smsnotify.vonage.api_secret' => 'test_api_secret',
        ]);
    });

    expect(resolve(VonageClient::class))->toBeInstanceOf(VonageClient::class);
});

it('resolves twilio client correctly', function(): void {
    config([
        'igniterlabs-smsnotify.twilio.account_sid' => 'test_account_sid',
        'igniterlabs-smsnotify.twilio.auth_token' => 'test_auth_token',
    ]);

    expect(resolve(TwilioClient::class))->toBeInstanceOf(TwilioClient::class);
});

it('registers permissions correctly', function(): void {
    $extension = new Extension(app());
    $permissions = $extension->registerPermissions();

    expect($permissions)->toBeArray()
        ->and($permissions)->toHaveKey('IgniterLabs.SmsNotify.Manage')
        ->and($permissions['IgniterLabs.SmsNotify.Manage']['description'])->toBe('Manage SMS notification channels and templates')
        ->and($permissions['IgniterLabs.SmsNotify.Manage']['group'])->toBe('igniter::admin.permissions.name');
});

it('registers navigation correctly', function(): void {
    $extension = new Extension(app());
    $navigation = $extension->registerNavigation();

    expect($navigation)->toBeArray()
        ->and($navigation['design']['child'])->toHaveKey('notification_templates')
        ->and($navigation['design']['child']['notification_templates']['title'])->toBe(lang('igniterlabs.smsnotify::default.template.text_title'))
        ->and($navigation['design']['child']['notification_templates']['permission'])->toBe('IgniterLabs.SmsNotify.Manage');
});

it('registers settings correctly', function(): void {
    $extension = new Extension(app());
    $settings = $extension->registerSettings();

    expect($settings)->toBeArray()
        ->and($settings['settings']['label'])->toBe('igniterlabs.smsnotify::default.setting_title')
        ->and($settings['settings']['description'])->toBe('igniterlabs.smsnotify::default.setting_desc')
        ->and($settings['settings']['permission'])->toBe('IgniterLabs.SmsNotify.Manage');
});

it('registers automation rules correctly', function(): void {
    $extension = new Extension(app());
    $rules = $extension->registerAutomationRules();

    expect($rules)->toBeArray()
        ->and($rules['actions'])->toContain(SendSmsNotification::class)
        ->and($rules['presets']['smsnotify_new_order_status']['name'])->toBe('Send an SMS message when an order status is updated')
        ->and($rules['presets']['smsnotify_new_order_status']['event'])->toBe(NewOrderStatus::class);
});

it('registers sms channels correctly', function(): void {
    $extension = new Extension(app());
    $channels = $extension->registerSmsChannels();

    expect($channels)
        ->toBeArray()
        ->toHaveKey('twilio', Twilio::class)
        ->toHaveKey('vonage', Vonage::class)
        ->toHaveKey('clickatell', Clickatell::class)
        ->toHaveKey('plivo', Plivo::class)
        ->toHaveKey('awssns', AwsSns::class);
});

it('registers sms templates correctly', function(): void {
    $extension = new Extension(app());
    $templates = $extension->registerSmsTemplates();

    expect($templates)
        ->toBeArray()
        ->toHaveKey('igniterlabs.smsnotify::_sms.new_order', 'igniterlabs.smsnotify::default.template.text_order_placed')
        ->toHaveKey('igniterlabs.smsnotify::_sms.new_reservation', 'igniterlabs.smsnotify::default.template.text_new_reservation')
        ->toHaveKey('igniterlabs.smsnotify::_sms.order_assigned', 'igniterlabs.smsnotify::default.template.text_order_assigned')
        ->toHaveKey('igniterlabs.smsnotify::_sms.order_confirmed', 'igniterlabs.smsnotify::default.template.text_order_confirmed')
        ->toHaveKey('igniterlabs.smsnotify::_sms.order_status_changed', 'igniterlabs.smsnotify::default.template.text_order_status_changed')
        ->toHaveKey('igniterlabs.smsnotify::_sms.reservation_assigned', 'igniterlabs.smsnotify::default.template.text_reservation_assigned')
        ->toHaveKey('igniterlabs.smsnotify::_sms.reservation_confirmed', 'igniterlabs.smsnotify::default.template.text_reservation_confirmed')
        ->toHaveKey('igniterlabs.smsnotify::_sms.reservation_status_changed', 'igniterlabs.smsnotify::default.template.text_reservation_status_changed');
});
