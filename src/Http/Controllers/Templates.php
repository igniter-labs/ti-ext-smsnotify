<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Http\Controllers;

use Igniter\Admin\Http\Actions\FormController;
use Igniter\Admin\Http\Actions\ListController;
use IgniterLabs\SmsNotify\Http\Requests\TemplateRequest;
use Exception;
use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Facades\AdminMenu;
use Igniter\Admin\Widgets\Form;
use Igniter\Flame\Exception\FlashException;
use Igniter\Local\Facades\Location as LocationFacade;
use Igniter\Local\Models\Location;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Template;

class Templates extends AdminController
{
    public array $implement = [
        FormController::class,
        ListController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => Template::class,
            'title' => 'igniterlabs.smsnotify::default.template.text_title',
            'emptyMessage' => 'igniterlabs.smsnotify::default.template.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'showCheckboxes' => false,
            'configFile' => 'template',
        ],
    ];

    public array $formConfig = [
        'name' => 'igniterlabs.smsnotify::default.template.text_title',
        'model' => Template::class,
        'request' => TemplateRequest::class,
        'edit' => [
            'title' => 'igniterlabs.smsnotify::default.template.text_edit_title',
            'redirect' => 'igniterlabs/smsnotify/templates/edit/{id}',
            'redirectClose' => 'igniterlabs/smsnotify/templates',
        ],
        'preview' => [
            'title' => 'igniterlabs.smsnotify::default.template.text_preview_title',
            'back' => 'igniterlabs/smsnotify/templates',
        ],
        'configFile' => 'template',
    ];

    protected null|string|array $requiredPermissions = 'IgniterLabs.SmsNotify.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('notification_templates', 'design');
    }

    public function index(): void
    {
        if ($this->getUser()->hasPermission('IgniterLabs.SmsNotify.ManageTemplates')) {
            Template::syncAll();
        }

        $this->asExtension('ListController')->index();
    }

    public function formExtendFields(Form $form): void
    {
        if ($form->context != 'create') {
            $field = $form->getField('code');
            $field->disabled = true;
        }
    }

    public function formBeforeSave($model): void
    {
        $model->is_custom = true;
    }

    public function onTestTemplate($context, $recordId): array
    {
        throw_unless(strlen((string) $recordId), new FlashException('Template id not found'));

        throw_unless($model = $this->formFindModelObject($recordId),
            new FlashException('Template not found')
        );

        $telephoneNo = Location::getDefault()->location_telephone;

        try {
            resolve(Manager::class)->notify($model->code, $telephoneNo, [], LocationFacade::current());

            flash()->success(sprintf(
                lang('igniterlabs.smsnotify::default.template.alert_test_message_sent'), $telephoneNo
            ));
        } catch (Exception $e) {
            flash()->error($e->getMessage());
        }

        return [
            '#notification' => $this->makePartial('flash'),
        ];
    }
}
