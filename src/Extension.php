<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify;

use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;
use Igniter\Cart\AutomationRules\Events\NewOrderStatus;
use Igniter\System\Classes\BaseExtension;
use IgniterLabs\SmsNotify\AutomationRules\Actions\SendSmsNotification;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\SmsChannels\AwsSns;
use IgniterLabs\SmsNotify\SmsChannels\Clickatell;
use IgniterLabs\SmsNotify\SmsChannels\Plivo;
use IgniterLabs\SmsNotify\SmsChannels\Twilio;
use IgniterLabs\SmsNotify\SmsChannels\Vonage;
use Override;
use Plivo\RestClient as PlivoClient;
use Twilio\Rest\Client as TwilioClient;
use Vonage\Client as VonageClient;
use Vonage\Client\Credentials\Basic;

/**
 * SmsNotify Extension Information File
 */
class Extension extends BaseExtension
{
    public $singletons = [
        Manager::class,
    ];

    #[Override]
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/channels.php', 'igniterlabs-smsnotify');

        $this->registerSnsClient();
        $this->registerPlivoClient();
        $this->registerVonageClient();
        $this->registerTwilioClient();
    }

    #[Override]
    public function registerPermissions(): array
    {
        return [
            'IgniterLabs.SmsNotify.Manage' => [
                'description' => 'Manage SMS notification channels and templates',
                'group' => 'igniter::admin.permissions.name',
            ],
        ];
    }

    #[Override]
    public function registerNavigation(): array
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

    #[Override]
    public function registerSettings(): array
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

    public function registerAutomationRules(): array
    {
        return [
            'events' => [],
            'actions' => [
                SendSmsNotification::class,
            ],
            'conditions' => [],
            'presets' => [
                'smsnotify_new_order_status' => [
                    'name' => 'Send an SMS message when an order status is updated',
                    'event' => NewOrderStatus::class,
                    'actions' => [
                        SendSmsNotification::class => [
                            'template' => 'igniterlabs.smsnotify::_sms.order_status_changed',
                            'send_to' => 'customer',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function registerSmsChannels(): array
    {
        return [
            'twilio' => Twilio::class,
            'vonage' => Vonage::class,
            'clickatell' => Clickatell::class,
            'plivo' => Plivo::class,
            'awssns' => AwsSns::class,
        ];
    }

    public function registerSmsTemplates(): array
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

    protected function registerSnsClient(): void
    {
        $this->app->singleton(SnsClient::class, fn($app): SnsClient => new SnsClient([
            'credentials' => new Credentials(
                $app['config']['igniterlabs-smsnotify.aws.key'],
                $app['config']['igniterlabs-smsnotify.aws.secret'],
            ),
            'use_aws_shared_config_files' => false,
            'region' => 'us-east-1',
            'version' => '2010-03-31',
        ]));
    }

    protected function registerPlivoClient(): void
    {
        $this->app->singleton(PlivoClient::class, fn($app): PlivoClient => new PlivoClient(
            $app['config']['igniterlabs-smsnotify.plivo.auth_id'],
            $app['config']['igniterlabs-smsnotify.plivo.auth_token'],
        ));
    }

    protected function registerVonageClient(): void
    {
        $this->app->singleton(VonageClient::class, fn($app): VonageClient => new VonageClient(new Basic(
            $app['config']['igniterlabs-smsnotify.vonage.api_key'],
            $app['config']['igniterlabs-smsnotify.vonage.api_secret'],
        )));
    }

    protected function registerTwilioClient(): void
    {
        $this->app->singleton(TwilioClient::class, fn($app): TwilioClient => new TwilioClient(
            $app['config']['igniterlabs-smsnotify.twilio.account_sid'],
            $app['config']['igniterlabs-smsnotify.twilio.auth_token'],
        ));
    }
}
