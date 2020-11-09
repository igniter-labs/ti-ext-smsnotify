<?php

namespace IgniterLabs\SmsNotify;

use Event;
use Exception;
use IgniterLabs\SmsNotify\Classes\BaseNotification;
use IgniterLabs\SmsNotify\Classes\Manager;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Notifications\Events\NotificationFailed;
use System\Classes\BaseExtension;

/**
 * SmsNotify Extension Information File
 */
class Extension extends BaseExtension
{
    /**
     * Register method, called when the extension is first registered.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__.'/config/channels.php';
        $this->mergeConfigFrom($configPath, 'services');

        $this->app->register(\Igniter\Flame\Notifications\NotificationServiceProvider::class);
        $this->app->register(\Illuminate\Notifications\NexmoChannelServiceProvider::class);
        $this->app->register(\NotificationChannels\Twilio\TwilioProvider::class);
        $this->app->register(\NotificationChannels\Clickatell\ClickatellServiceProvider::class);
        $this->app->register(\NotificationChannels\Plivo\PlivoServiceProvider::class);

        AliasLoader::getInstance()->alias('Notification', \Illuminate\Support\Facades\Notification::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        $this->bindNotificationEvents();
    }

    /**
     * Registers any admin permissions used by this extension.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'IgniterLabs.SmsNotify.Manage' => [
                'description' => 'Manage SMS notification channels and templates',
                'group' => 'module',
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'design' => [
                'child' => [
                    'notification_templates' => [
                        'priority' => 999,
                        'class' => 'notification_templates',
                        'href' => admin_url('igniterlabs/smsnotify/templates'),
                        'title' => lang('igniterlabs.smsnotify::default.template.text_title'),
                        'permission' => 'IgniterLabs.SmsNotify.Manage',
                    ],
                ],
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'igniterlabs.smsnotify::default.setting_title',
                'description' => 'igniterlabs.smsnotify::default.setting_desc',
                'icon' => 'fa fa-sms',
                'permission' => 'IgniterLabs.SmsNotify.Manage',
                'url' => admin_url('igniterlabs/smsnotify/channels'),
            ],
        ];
    }

    public function registerAutomationRules()
    {
        return [
            'events' => [],
            'actions' => [
                \IgniterLabs\SmsNotify\AutomationRules\Actions\SendSmsNotification::class,
            ],
            'conditions' => [],
            'presets' => [
                'smsnotify_new_order_status' => [
                    'name' => 'Send an SMS message when an order status is updated',
                    'event' => \Igniter\Cart\AutomationRules\Events\NewOrderStatus::class,
                    'actions' => [
                        \IgniterLabs\SmsNotify\AutomationRules\Actions\SendSmsNotification::class => [
                            'template' => 'igniterlabs.smsnotify::_sms.order_status_changed',
                            'send_to' => 'customer',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function registerSmsChannels()
    {
        return [
            'twilio' => \IgniterLabs\SmsNotify\SmsChannels\Twilio::class,
            'nexmo' => \IgniterLabs\SmsNotify\SmsChannels\Nexmo::class,
            'clickatell' => \IgniterLabs\SmsNotify\SmsChannels\Clickatell::class,
            'plivo' => \IgniterLabs\SmsNotify\SmsChannels\Plivo::class,
        ];
    }

    public function registerSmsTemplates()
    {
        return [
            'igniterlabs.smsnotify::_sms.new_order' => 'igniterlabs.smsnotify::default.template.text_order_placed',
            'igniterlabs.smsnotify::_sms.new_reservation' => 'igniterlabs.smsnotify::default.template.text_new_reservation',
            'igniterlabs.smsnotify::_sms.order_assigned' => 'igniterlabs.smsnotify::default.template.text_order_assigned',
            'igniterlabs.smsnotify::_sms.order_confirmed' => 'igniterlabs.smsnotify::default.template.text_order_confirmed',
            'igniterlabs.smsnotify::_sms.order_status_changed' => 'igniterlabs.smsnotify::default.template.text_order_status_changed',
            'igniterlabs.smsnotify::_sms.reservation_assigned' => 'igniterlabs.smsnotify::default.template.text_reservation_assigned',
            'igniterlabs.smsnotify::_sms.reservation_confirmed' => 'igniterlabs.smsnotify::default.template.text_reservation_confirmed',
            'igniterlabs.smsnotify::_sms.reservation_status_changed' => 'igniterlabs.smsnotify::default.template.text_reservation_status_changed',
        ];
    }

    protected function bindNotificationEvents()
    {
        Event::listen('notification.beforeRegister', function () {
            Manager::instance()->applyNotificationConfigValues();
        });

        Event::listen(NotificationFailed::class, function ($event) {
            if (!$event->notification instanceof BaseNotification)
                return;

            $exception = array_get($event->data, 'exception');
            if (!$exception instanceof Exception)
                return;

            throw $exception;
        });
    }
}
