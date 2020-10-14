<?php

namespace IgniterLabs\SmsNotify\Models;

use IgniterLabs\SmsNotify\Classes\BaseNotification;
use Illuminate\Notifications\Events\NotificationSent;

class MessageLog extends \Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniterlabs_smsnotify_logs';

    public $timestamps = TRUE;

    /**
     * @var array fillable fields
     */
    protected $guarded = [];

    public $casts = [
        'channel' => 'string',
        'to' => 'string',
        'message' => 'string',
        'status' => 'string',
        'short_status' => 'integer',
    ];

    public static function createLogFromEvent($event)
    {
        if (!$event->notification instanceof BaseNotification)
            return;

        $isSuccess = $event instanceof NotificationSent;

        $record = new static;
        $record->channel = $event->channel;
        $record->template = $event->notification->templateCode;
        $record->from = array_get($event->notifiable->routes, $event->channel);
        $record->to = array_get($event->notifiable->routes, $event->channel);
        $record->message = !$isSuccess ? array_get($event->data, 'message') : 'Message successfully sent!';
        $record->status = $isSuccess;

        return $record->save();
    }
}
