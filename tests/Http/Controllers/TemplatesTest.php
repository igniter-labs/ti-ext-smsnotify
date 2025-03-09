<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\Http\Controllers;

use Exception;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Template;

it('loads templates page', function(): void {
    actingAsSuperUser()
        ->get(route('igniterlabs.smsnotify.templates'))
        ->assertOk();
});

it('loads edit template page', function(): void {
    $template = Template::create([
        'code' => 'test_template',
        'name' => 'Test Template',
        'content' => 'Test Template Description',
        'is_custom' => true,
    ]);

    actingAsSuperUser()
        ->get(route('igniterlabs.smsnotify.templates', ['slug' => 'edit/'.$template->getKey()]))
        ->assertOk();
});

it('loads template preview page', function(): void {
    $template = Template::create([
        'code' => 'test_template',
        'name' => 'Test Template',
        'content' => 'Test Template Description',
        'is_custom' => true,
    ]);

    actingAsSuperUser()
        ->get(route('igniterlabs.smsnotify.templates', ['slug' => 'preview/'.$template->getKey()]))
        ->assertOk();
});

it('updates template', function(): void {
    $template = Template::create([
        'code' => 'test_template',
        'name' => 'Test Template',
        'content' => 'Template content',
        'is_custom' => true,
    ]);

    actingAsSuperUser()
        ->post(route('igniterlabs.smsnotify.templates', ['slug' => 'edit/'.$template->getKey()]), [
            'Template' => [
                'code' => 'test_template',
                'name' => 'Test Template Updated',
                'content' => 'Template content updated',
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Template::where('name', 'Test Template Updated')->exists())->toBeTrue();
});

it('sends test template', function(): void {
    Template::flushEventListeners();
    $template = Template::create([
        'code' => 'test_template',
        'name' => 'Test Template',
        'content' => 'Test Template Description',
        'is_custom' => true,
    ]);
    app()->instance(Manager::class, $manager = mock(Manager::class));
    $manager->shouldReceive('notify')->once()->andReturnNull();

    actingAsSuperUser()
        ->post(route('igniterlabs.smsnotify.templates', ['slug' => 'edit/'.$template->getKey()]), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onTestTemplate',
        ]);
});

it('throws exception when sending test template fails', function(): void {
    Template::flushEventListeners();
    $template = Template::create([
        'code' => 'test_template',
        'name' => 'Test Template',
        'content' => 'Test Template Description',
        'is_custom' => true,
    ]);
    app()->instance(Manager::class, $manager = mock(Manager::class));
    $manager->shouldReceive('notify')->once()->andThrow(new Exception('Test exception'));

    actingAsSuperUser()
        ->post(route('igniterlabs.smsnotify.templates', ['slug' => 'edit/'.$template->getKey()]), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onTestTemplate',
        ])
        ->assertSee('Test exception');
});
