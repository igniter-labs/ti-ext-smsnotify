<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Classes;

use Closure;
use Igniter\Flame\Database\Model;
use Igniter\System\Actions\ModelAction;

abstract class BaseChannel extends ModelAction
{
    protected $configFields = [];

    protected $configRules = [];

    public function __construct(?Model $model = null)
    {
        parent::__construct($model);

        $parts = explode('\\', strtolower(static::class));
        $namespace = implode('.', array_slice($parts, 0, 2));

        $this->configPath[] = 'igniterlabs.smsnotify::/models';
        $this->configPath[] = $namespace.'::models';

        $formConfig = $this->loadConfig($this->defineFormConfig(), ['fields']);
        $this->configFields = array_get($formConfig, 'fields', []);
        $this->configRules = array_get($formConfig, 'rules', []);

        if (!$model instanceof Model) {
            return;
        }

        $this->initConfigData();
    }

    abstract public function send($to, $content);

    /**
     * Initialize method called when the notification channel is first loaded.
     */
    public function initConfigData() {}

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

    protected function sendUsingConfig(array $config, Closure $callback)
    {
        $oldConfig = config('igniterlabs-smsnotify');
        config()->set('igniterlabs-smsnotify', array_merge($oldConfig, array_undot($config)));

        $response = $callback();

        config()->set('igniterlabs-smsnotify', $oldConfig);

        return $response;
    }
}
