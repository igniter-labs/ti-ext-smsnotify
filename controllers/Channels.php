<?php

namespace IgniterLabs\SmsNotify\Controllers;

use Admin\Classes\AdminController;
use Admin\Facades\AdminMenu;
use Exception;
use Igniter\Flame\Exception\ApplicationException;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Channel;

class Channels extends AdminController
{
    public $implement = [
        \Admin\Actions\FormController::class,
        \Admin\Actions\ListController::class,
        \Admin\Actions\LocationAwareController::class,
    ];

    public $listConfig = [
        'list' => [
            'model' => \IgniterLabs\SmsNotify\Models\Channel::class,
            'title' => 'igniterlabs.smsnotify::default.channel.text_title',
            'emptyMessage' => 'igniterlabs.smsnotify::default.channel.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'configFile' => 'channel',
        ],
    ];

    public $formConfig = [
        'name' => 'igniterlabs.smsnotify::default.channel.text_title',
        'model' => \IgniterLabs\SmsNotify\Models\Channel::class,
        'create' => [
            'title' => 'igniterlabs.smsnotify::default.channel.text_new_title',
            'redirect' => 'igniterlabs/smsnotify/channels/edit/{id}',
            'redirectClose' => 'igniterlabs/smsnotify/channels',
        ],
        'edit' => [
            'title' => 'igniterlabs.smsnotify::default.channel.text_edit_title',
            'redirect' => 'igniterlabs/smsnotify/channels/edit/{id}',
            'redirectClose' => 'igniterlabs/smsnotify/channels',
        ],
        'preview' => [
            'title' => 'igniterlabs.smsnotify::default.channel.text_preview_title',
            'redirect' => 'igniterlabs/smsnotify/channels',
        ],
        'configFile' => 'channel',
    ];

    protected $requiredPermissions = 'IgniterLabs.SmsNotify.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('settings', 'system');
    }

    public function index()
    {
        if ($this->getUser()->hasPermission('IgniterLabs.SmsNotify.ManageChannels'))
            Channel::syncAll();

        $this->asExtension('ListController')->index();
    }

    public function formExtendModel($model)
    {
        if (!$model->exists)
            $model->applyChannelClass();

        return $model;
    }

    public function formExtendFields($form)
    {
        $model = $form->model;
        if ($model->exists) {
            $form->addTabFields($model->getConfigFields());
        }

        if ($form->context != 'create') {
            $field = $form->getField('code');
            $field->disabled = true;
        }
    }

    public function formBeforeCreate($model)
    {
        if (!strlen($code = post('Channel.channel')))
            throw new ApplicationException('Invalid channel code selected');

        $model->class_name = Manager::instance()->getChannel($code);
        $model->applyChannelClass();
    }

    public function formValidate($model, $form)
    {
        $rules = [
            ['channel', 'lang:admin::lang.payments.label_payments', 'sometimes|required|alpha_dash'],
            ['name', 'lang:admin::lang.label_name', 'sometimes|required|min:2|max:128'],
            ['code', 'lang:admin::lang.payments.label_code', 'sometimes|required|alpha_dash|unique:igniterlabs_smsnotify_channels,code'],
            ['description', 'lang:admin::lang.label_description', 'sometimes|required|max:255'],
            ['is_default', 'lang:admin::lang.payments.label_default', 'required|integer'],
            ['is_enabled', 'lang:admin::lang.label_status', 'required|integer'],
        ];

        if ($mergeRules = $form->model->getConfigRules())
            array_push($rules, ...$mergeRules);

        return $this->validatePasses($form->getSaveData(), $rules);
    }
}
