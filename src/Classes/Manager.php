<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Classes;

use Igniter\Flame\Support\PagicHelper;
use Igniter\Flame\Support\StringParser;
use Igniter\System\Classes\ExtensionManager;
use Igniter\System\Helpers\ViewHelper;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\Models\Template;

class Manager
{
    /**
     * @var null|array An array of channel types.
     */
    protected $channelCache;

    /**
     * @var null|array An array of template types.
     */
    protected $templateCache;

    /**
     * @var null|array An array of template types.
     */
    protected $messageTemplateCache;

    /**
     * @var array Cache of notification channel registration callbacks.
     */
    protected static $callbacks = [];

    public function buildContent($templateCode, $data = []): string
    {
        if (isset($this->messageTemplateCache[$templateCode])) {
            $template = $this->messageTemplateCache[$templateCode];
        } else {
            $this->messageTemplateCache[$templateCode] = $template = Template::findOrMakeTemplate($templateCode);
        }

        $globalVars = ViewHelper::getGlobalVars();
        if ($globalVars !== []) {
            $data = (array)$data + $globalVars;
        }

        return $this->renderTemplate($template, $data);
    }

    public function renderTemplate($template, $data = []): string
    {
        $content = PagicHelper::parse($template->content, $data);

        $content = (new StringParser)->parse($content, $data);

        return html_entity_decode((string)preg_replace("/[\r\n]{2,}/", "\n\n", $content), ENT_QUOTES, 'UTF-8');
    }

    public function notify($templateCode, $to, $data, $location = null): void
    {
        $content = $this->buildContent($templateCode, $data);

        Channel::getDefault(optional($location)->location_id)
            ->getChannelObject()
            ->send($to, $content);
    }

    //
    //
    //

    public function listChannels()
    {
        if (!is_null($this->channelCache)) {
            return $this->channelCache;
        }

        foreach ($this->getRegisteredChannels() as $channelCode => $className) {
            if (!class_exists($className)) {
                continue;
            }

            $this->channelCache[$channelCode] = $className;
        }

        return $this->channelCache;
    }

    /**
     * @return BaseChannel[]
     */
    public function listChannelObjects()
    {
        $results = [];
        foreach ($this->listChannels() as $channelCode => $className) {
            $results[$channelCode] = resolve($className);
        }

        return $results;
    }

    /**
     * @param $name
     * @return BaseChannel
     */
    public function getChannel($code)
    {
        return array_get($this->listChannels(), $code);
    }

    public function listTemplates()
    {
        if (!is_null($this->templateCache)) {
            return $this->templateCache;
        }

        $templates = (array)$this->getRegisteredTemplates();
        foreach ($templates as $code => $name) {
            $this->templateCache[$code] = $name;
        }

        return $this->templateCache;
    }

    /**
     * @return string|null
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
        if (isset($templates[$codeOrClass])) {
            return $templates[$codeOrClass];
        }

        $templates = array_flip($templates);

        return isset($templates[$codeOrClass]) ? $codeOrClass : null;
    }

    protected function loadRegistered(string $methodName)
    {
        $results = [];
        $manager = resolve(ExtensionManager::class);
        $bundles = $manager->getRegistrationMethodValues($methodName);
        foreach ($bundles as $definitions) {
            foreach ($definitions as $index => $value) {
                if (is_string($index)) {
                    $results[$index] = $value;
                }
            }
        }

        return $results;
    }
}
