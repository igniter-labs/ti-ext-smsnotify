<?php

namespace IgniterLabs\SmsNotify\Models;

use IgniterLabs\SmsNotify\Classes\BaseNotification;

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

    public static function createLogFromEvent($event, $isSuccess = TRUE)
    {
        if (!$event->notification instanceof BaseNotification)
            return;

        return self::createLog(
            $event->channel,
            array_get($event->notifiable->routes, $event->channel),
            array_get($event->data, 'message'),
            $isSuccess,
            $isSuccess
        );
    }

    public static function createLog($channel, $to, $message, $status)
    {
        $record = new static;
        $record->channel = $channel;
        $record->to = $to;
        $record->message = $message;
        $record->status = $status;

        return $record->save();
    }
}