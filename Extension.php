<?php

namespace IgniterLabs\SmsNotify;

use IgniterLabs\SmsNotify\Classes\SmsNotification;
use System\Classes\BaseExtension;

/**
 * SmsNotify Extension Information File
 */
class Extension extends BaseExtension
{
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
}
