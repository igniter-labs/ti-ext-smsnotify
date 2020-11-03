<?php

namespace IgniterLabs\SmsNotify\Classes;

use Igniter\Flame\Support\PagicHelper;
use Igniter\Flame\Support\StringParser;
use Igniter\Flame\Traits\Singleton;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\Models\Template;
use Illuminate\Support\Facades\Config;
use System\Classes\ExtensionManager;
use System\Helpers\ViewHelper;

class Manager
{
    use Singleton;

    /**
     * @var array An array of channel types.
     */
    protected $channelCache;

    /**
     * @var array An array of template types.
     */
    protected $templateCache;

    protected $messageTemplateCache;

    /**
     * @var array Cache of notification channel registration callbacks.
     */
    protected static $callbacks = [];

    public function addContentToMessage($message, $templateCode, $data = [])
    {
        if (isset($this->messageTemplateCache[$templateCode])) {
            $template = $this->templateCache[$templateCode];
        }
        else {
            $this->templateCache[$templateCode] = $template = Template::findOrMakeTemplate($templateCode);
        }

        $globalVars = ViewHelper::getGlobalVars();
        if (!empty($globalVars)) {
            $data = (array)$data + $globalVars;
        }

        $content = $this->renderTemplate($template, $data);
        $message->content($content);

        return $message;
    }

    public function renderTemplate($template, $data = [])
    {
        $content = PagicHelper::parse($template->content, $data);

        $content = (new StringParser)->parse($content, $data);

        return html_entity_decode(preg_replace("/[\r\n]{2,}/", "\n\n", $content), ENT_QUOTES, 'UTF-8');
    }

    public function applyNotificationConfigValues()
    {
        foreach (array_keys($this->listChannels()) as $channelCode) {
            $config = Channel::getConfig($channelCode, []);
            foreach ($config as $key => $value) {
                $configKey = sprintf('services.%s.%s', $channelCode, $key);
                Config::set($configKey, $value ?? Config::get($configKey));

                if ($channelCode == 'nexmo')
                    Config::set('nexmo.'.$key, $value ?? Config::get('nexmo.'.$key));
            }
        }
    }

    //
    //
    //

    public function listChannels()
    {
        if (!is_null($this->channelCache))
            return $this->channelCache;

        foreach ($this->getRegisteredChannels() as $channelCode => $className) {
            if (!class_exists($className))
                continue;

            $this->channelCache[$channelCode] = $className;
        }

        return $this->channelCache;
    }

    /**
     * @return \IgniterLabs\SmsNotify\Classes\BaseChannel[]
     */
    public function listChannelObjects()
    {
        $results = [];
        foreach ($this->listChannels() as $channelCode => $className) {
            $results[$channelCode] = new $className;
        }

        return $results;
    }

    /**
     * @param $name
     * @return \IgniterLabs\SmsNotify\Classes\BaseChannel
     */
    public function getChannel($code)
    {
        return array_get($this->listChannels(), $code);
    }

    public function listTemplates()
    {
        if (!is_null($this->templateCache))
            return $this->templateCache;

        $templates = (array)$this->getRegisteredTemplates();
        foreach ($templates as $code => $name) {
            $this->templateCache[$code] = $name;
        }

        return $this->templateCache;
    }

    /**
     * @param $code
     * @return \IgniterLabs\SmsNotify\Classes\BaseNotification|null
     */
    public function getTemplate($code)
    {
        return array_get($this->listTemplates(), $code);
    }

    public function getRegisteredChannels()
    {
        return $this->loadRegistered('registerSmsChannels');
    }

    public function getRegisteredTemplates()
    {
        return $this->loadRegistered('registerSmsTemplates');
    }

    public function resolveTemplateCode(string $codeOrClass)
    {
        $templates = (array)$this->getRegisteredTemplates();
        if (isset($templates[$codeOrClass]))
            return $templates[$codeOrClass];

        $templates = array_flip($templates);
        if (isset($templates[$codeOrClass]))
            return $codeOrClass;
    }

    protected function loadRegistered(string $methodName)
    {
        $results = [];
        $manager = ExtensionManager::instance();
        $bundles = $manager->getRegistrationMethodValues($methodName);
        foreach ($bundles as $owner => $definitions) {
            foreach ($definitions as $index => $value) {
                if (is_string($index)) {
                    $results[$index] = $value;
                }
            }
        }

        return $results;
    }
}
