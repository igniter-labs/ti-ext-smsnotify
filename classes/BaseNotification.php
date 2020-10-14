<?php

namespace IgniterLabs\SmsNotify\Classes;

use IgniterLabs\SmsNotify\Models\Channel;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Clickatell\ClickatellMessage;
use NotificationChannels\Plivo\PlivoMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

abstract class BaseNotification extends Notification
{
    protected $host;

    public $templateCode;

    protected $supportedChannels = ['twilio', 'nexmo', 'clickatell', 'plivo'];

    public function __construct($host = null)
    {
        $this->host = $host;
    }

    public function template($code)
    {
        $this->templateCode = $code;

        return $this;
    }

    //
    //
    //

    public function via($notifiable)
    {
        return [
            Channel::getDefault()->getChannelObject()->getChannelClassName(),
        ];
    }

    public function toTwilio($notifiable)
    {
        return $this->addContentToMessage(new TwilioSmsMessage);
    }

    public function toNexmo($notifiable)
    {
        return $this->addContentToMessage(new NexmoMessage);
    }

    public function toClickatell($notifiable)
    {
        return $this->addContentToMessage(new ClickatellMessage);
    }

    public function toPlivo($notifiable)
    {
        return $this->addContentToMessage(new PlivoMessage);
    }

    //
    //
    //

    protected function getData()
    {
        return [];
    }

    protected function addContentToMessage($message)
    {
        return Manager::instance()->addContentToMessage(
            $message, $this->templateCode, $this->getData()
        );
    }
}
