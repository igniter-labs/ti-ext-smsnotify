<?php

namespace IgniterLabs\SmsNotify\Http\Controllers;

use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Facades\AdminMenu;
use Igniter\Flame\Exception\FlashException;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Channel;

class Channels extends AdminController
{
    public array $implement = [
        \Igniter\Admin\Http\Actions\FormController::class,
        \Igniter\Admin\Http\Actions\ListController::class,
        \Igniter\Local\Http\Actions\LocationAwareController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => \IgniterLabs\SmsNotify\Models\Channel::class,
            'title' => 'igniterlabs.smsnotify::default.channel.text_title',
            'emptyMessage' => 'igniterlabs.smsnotify::default.channel.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'configFile' => 'channel',
            'back' => 'settings',
        ],
    ];

    public array $formConfig = [
        'name' => 'igniterlabs.smsnotify::default.channel.text_title',
        'model' => \IgniterLabs\SmsNotify\Models\Channel::class,
        'request' => \IgniterLabs\SmsNotify\Http\Requests\ChannelRequest::class,
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
            'back' => 'igniterlabs/smsnotify/channels',
        ],
        'configFile' => 'channel',
    ];

    protected null|string|array $requiredPermissions = 'IgniterLabs.SmsNotify.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('settings', 'system');
    }

    public function index()
    {
        if ($this->getUser()->hasPermission('IgniterLabs.SmsNotify.ManageChannels')) {
            Channel::syncAll();
        }

        $this->asExtension('ListController')->index();
    }

    public function formExtendModel($model)
    {
        if (!$model->exists) {
            $model->applyChannelClass();
        }

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
        throw_unless(strlen($code = post('Channel.channel')),
            new FlashException('Invalid channel code selected'),
        );

        $model->class_name = resolve(Manager::class)->getChannel($code);
        $model->applyChannelClass();
    }
}
