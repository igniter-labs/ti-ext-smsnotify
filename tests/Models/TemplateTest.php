<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Tests\Models;

use Igniter\Flame\Database\Traits\Validation;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Template;

it('returns correct name attribute', function(): void {
    $template = Template::create([
        'code' => 'test_template',
        'name' => 'Test Template',
    ]);

    $name = $template->name;

    expect($name)->toBe('Test Template');
});

it('returns translated name attribute', function(): void {
    $template = Template::create([
        'code' => 'test_template',
        'name' => 'igniter::default.test_template',
    ]);

    $name = $template->name;

    expect($name)->toBe(lang('igniter::default.test_template'));
});

it('fills content from view correctly', function(): void {
    $template = Template::create([
        'code' => 'test_template',
    ]);

    $template->fillFromContent('<html>Test Content</html>');

    expect($template->content)->toBe('<html>Test Content</html>');
});

it('syncs all templates correctly', function(): void {
    $manager = mock(Manager::class);
    $manager->shouldReceive('getRegisteredTemplates')->andReturn([
        'new_template' => 'New Template',
    ]);
    app()->instance(Manager::class, $manager);

    Template::create([
        'code' => 'test_template',
        'is_custom' => 1,
    ]);
    Template::create([
        'code' => 'test_template_2',
    ]);
    Template::syncAll();

    $template = Template::where('code', 'new_template')->first();

    expect($template)->not->toBeNull()
        ->and($template->name)->toBe('New Template')
        ->and($template->is_custom)->toBe(0)
        ->and(Template::query()->where('code', 'test_template')->exists())->toBeTrue()
        ->and(Template::query()->where('code', 'test_template_2')->exists())->toBeFalse();
});

it('configures template model correctly', function(): void {
    $template = new Template;

    expect(class_uses_recursive($template))
        ->toContain(Validation::class)
        ->and($template->getTable())->toBe('igniterlabs_smsnotify_templates')
        ->and($template->getKeyName())->toBe('id')
        ->and($template->timestamps)->toBeTrue()
        ->and($template->getGuarded())->toBe([])
        ->and($template->rules)->toBe([
            ['code', 'igniter.pages::default.menu.label_code', 'required|unique:igniterlabs_smsnotify_templates,code'],
            ['name', 'igniter.pages::default.menu.label_title', 'required|max:128'],
            ['content', 'admin::lang.label_description', 'string'],
        ]);
});
