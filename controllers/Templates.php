<?php

namespace IgniterLabs\SmsNotify\Controllers;

use Admin\Classes\AdminController;
use Admin\Models\Locations_model;
use Admin\Widgets\Form;
use AdminMenu;
use Igniter\Flame\Exception\ApplicationException;
use IgniterLabs\SmsNotify\Classes\Notifier;
use IgniterLabs\SmsNotify\Models\Template;
use IgniterLabs\SmsNotify\SmSNotifications\AnonymousNotification;

class Templates extends AdminController
{
    public $implement = [
        'Admin\Actions\FormController',
        'Admin\Actions\ListController',
    ];

    public $listConfig = [
        'list' => [
            'model' => 'IgniterLabs\SmsNotify\Models\Template',
            'title' => 'igniterlabs.smsnotify::default.template.text_title',
            'emptyMessage' => 'igniterlabs.smsnotify::default.template.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'configFile' => 'template',
        ],
    ];

    public $formConfig = [
        'name' => 'igniterlabs.smsnotify::default.template.text_title',
        'model' => 'IgniterLabs\SmsNotify\Models\Template',
        'edit' => [
            'title' => 'igniterlabs.smsnotify::default.template.text_edit_title',
            'redirect' => 'igniterlabs/smsnotify/templates/edit/{id}',
            'redirectClose' => 'igniterlabs/smsnotify/templates',
        ],
        'preview' => [
            'title' => 'igniterlabs.smsnotify::default.template.text_preview_title',
            'redirect' => 'igniterlabs/smsnotify/templates',
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
        if ($this->getUser()->hasPermission('IgniterLabs.SmsNotify.ManageTemplates'))
            Template::syncAll();

        $this->asExtension('ListController')->index();
    }

    public function formExtendFields(Form $form)
    {
        if ($form->context != 'create') {
            $field = $form->getField('code');
            $field->disabled = TRUE;
        }
    }

    public function formBeforeSave($model)
    {
        $model->is_custom = TRUE;
    }

    public function onTestTemplate($context, $recordId)
    {
        if (!strlen($recordId))
            throw new ApplicationException('Template id not found');

        if (!$model = $this->formFindModelObject($recordId))
            throw new ApplicationException('Template not found');

        $telephoneNo = Locations_model::getDefault()->location_telephone;
        $notification = (new AnonymousNotification())->template($model->code);

        (new Notifier)->notifyNow($telephoneNo, $notification);

        flash()->success(sprintf(
            lang('igniterlabs.smsnotify::default.template.alert_test_message_sent'), $telephoneNo
        ));

        return [
            '#notification' => $this->makePartial('flash'),
        ];
    }
}
