<?php

namespace IgniterLabs\SmsNotify\Classes;

use Igniter\Flame\Database\Model;
use IgniterLabs\SmsNotify\Models\Channel;
use Illuminate\Notifications\Notification;

class SmsNotification extends Notification
{
    public $templateCode;

    protected $host;

    public function __construct($host = null)
    {
        $this->host = $host;
    }

    public function template($code)
    {
        $this->templateCode = $code;

        return $this;
    }

    public function via($notifiable)
    {
        return [
            Channel::getDefault()->getChannelObject()->getChannelClassName(),
        ];
    }

    protected function getData()
    {
        if (is_array($this->host))
            return $this->host;

        if ($this->host instanceof Model AND $this->host->methodExists('mailGetData'))
            return $this->host->mailGetData();

        return [];
    }

    protected function addContentToMessage($message)
    {
        return Manager::instance()->addContentToMessage(
            $message, $this->templateCode, $this->getData()
        );
    }

    public function __call($name, $args)
    {
        if (starts_with($name, 'to')) {
            return $this->addContentToMessage(
                Channel::getDefault()->getChannelObject()->toMessage(array_shift($args))
            );
        }
    }
}
