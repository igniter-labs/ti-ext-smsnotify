<?php

namespace IgniterLabs\SmsNotify\Http\Controllers;

use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Facades\AdminMenu;
use Igniter\Admin\Widgets\Form;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Local\Models\Location;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Template;

class Templates extends AdminController
{
    public $implement = [
        \Igniter\Admin\Http\Actions\FormController::class,
        \Igniter\Admin\Http\Actions\ListController::class,
    ];

    public $listConfig = [
        'list' => [
            'model' => \IgniterLabs\SmsNotify\Models\Template::class,
            'title' => 'igniterlabs.smsnotify::default.template.text_title',
            'emptyMessage' => 'igniterlabs.smsnotify::default.template.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'configFile' => 'template',
        ],
    ];

    public $formConfig = [
        'name' => 'igniterlabs.smsnotify::default.template.text_title',
        'model' => \IgniterLabs\SmsNotify\Models\Template::class,
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

    protected $requiredPermissions = 'IgniterLabs.SmsNotify.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('notification_templates', 'design');
    }

    public function index()
    {
        if ($this->getUser()->hasPermission('IgniterLabs.SmsNotify.ManageTemplates')) {
            Template::syncAll();
        }

        $this->asExtension('ListController')->index();
    }

    public function formExtendFields(Form $form)
    {
        if ($form->context != 'create') {
            $field = $form->getField('code');
            $field->disabled = true;
        }
    }

    public function formBeforeSave($model)
    {
        $model->is_custom = true;
    }

    public function onTestTemplate($context, $recordId)
    {
        if (!strlen($recordId)) {
            throw new ApplicationException('Template id not found');
        }

        if (!$model = $this->formFindModelObject($recordId)) {
            throw new ApplicationException('Template not found');
        }

        $telephoneNo = Location::getDefault()->location_telephone;

        resolve(Manager::class)->notify($model->code, $telephoneNo, []);

        flash()->success(sprintf(
            lang('igniterlabs.smsnotify::default.template.alert_test_message_sent'), $telephoneNo
        ));

        return [
            '#notification' => $this->makePartial('flash'),
        ];
    }
}
