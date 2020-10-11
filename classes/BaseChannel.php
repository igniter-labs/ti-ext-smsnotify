<?php

namespace IgniterLabs\SmsNotify\Classes;

use Igniter\Flame\Support\Facades\File;
use System\Actions\ModelAction;

abstract class BaseChannel extends ModelAction
{
    protected $channelClassName;

    protected $configFields = [];

    protected $configRules = [];

    public function __construct($model = null)
    {
        parent::__construct($model);

        $calledClass = strtolower(get_called_class());
        $this->configPath = extension_path(File::normalizePath($calledClass));
        $formConfig = $this->loadConfig($this->defineFormConfig(), ['fields']);
        $this->configFields = array_get($formConfig, 'fields', []);
        $this->configRules = array_get($formConfig, 'rules', []);

        if (!$model)
            return;

        $this->initConfigData();
    }

    /**
     * Initialize method called when the notification channel is first loaded.
     */
    public function initConfigData()
    {
    }

    /**
     * Extra field configuration for the payment type.
     */
    public function defineFormConfig()
    {
        return 'fields';
    }

    /**
     * Returns the form configuration used by this model.
     */
    public function getConfigFields()
    {
        return $this->configFields;
    }

    public function getConfigRules()
    {
        return $this->configRules;
    }

    /**
     * Returns information about this channel, including name and description.
     */
    public function channelDetails()
    {
        return [
            'name' => 'Notification channel',
            'description' => 'Notification channel description',
        ];
    }

    public function getName()
    {
        return array_get($this->channelDetails(), 'name', 'Unknown name');
    }

    public function getDescription()
    {
        return array_get($this->channelDetails(), 'description');
    }

    public function getChannelClassName()
    {
        return $this->channelClassName;
    }
}
