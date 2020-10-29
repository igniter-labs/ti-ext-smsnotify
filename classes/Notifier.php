<?php

namespace IgniterLabs\SmsNotify\Classes;

use ApplicationException;
use Igniter\Flame\Exception\SystemException;
use IgniterLabs\SmsNotify\Models\Channel;
use IgniterLabs\SmsNotify\Models\Template;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class Notifier
{
    public function notify($notifiables, $notification, array $data = [])
    {
        $notification = $this->makeNotification($notification, $data);

        $notifiables = $this->routeNotifiable($notifiables);

        app(Dispatcher::class)->send($notifiables, $notification);
    }

    public function notifyNow($notifiables, $notification, array $data = [])
    {
        $notification = $this->makeNotification($notification, $data);

        $notifiables = $this->routeNotifiable($notifiables);

        app(Dispatcher::class)->sendNow($notifiables, $notification);
    }

    protected function routeNotifiable($to)
    {
        $notifiable = new AnonymousNotifiable;

        if (!$defaultChannel = Channel::getDefault())
            throw new SystemException('Default SMS channel not found.');

        if ($defaultChannel->code == 'clickatell')
            $to = (array)$to;

        $notifiable->route($defaultChannel->code, $to);

        return $notifiable;
    }

    protected function makeNotification($notification, $data)
    {
        if (!is_string($notification))
            return $notification;

        $template = Template::findOrMakeTemplate($notification);
        if (!($template instanceof Notification))
            throw new ApplicationException("Notification [{$notification}] not found");

        $template->setParameters($data);

        return $template;
    }
}
