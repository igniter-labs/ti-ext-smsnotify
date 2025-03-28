<p align="center">
    <a href="https://github.com/igniter-labs/ti-ext-smsnotify/actions"><img src="https://github.com/igniter-labs/ti-ext-smsnotify/actions/workflows/pipeline.yml/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/igniterlabs/ti-ext-smsnotify"><img src="https://img.shields.io/packagist/dt/igniterlabs/ti-ext-smsnotify" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/igniterlabs/ti-ext-smsnotify"><img src="https://img.shields.io/packagist/v/igniterlabs/ti-ext-smsnotify" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/igniterlabs/ti-ext-smsnotify"><img src="https://img.shields.io/packagist/l/igniterlabs/ti-ext-smsnotify" alt="License"></a>
</p>

## SMS Notification channels and messages for TastyIgniter

This extension allows admins to configure sms notifications to be sent out when certain events happen in TastyIgniter. 

## Features
- Receive SMS notifications whenever a new order has been placed
- SMS alert to your customers about their order or reservation status
- Customizable SMS Messages
- **Supported channels:** twilio, nexmo, clickatell and plivo
- Add your own custom SMS notification channel.

### Admin Panel

Go to **Manage > Settings > Configure SMS Channels** to manage notification channels.
Notification messages can be customized in the admin panel by navigating to **Design > SMS Templates**.

Use the `SendSmsNotification` Automation rule action to send out notification when certain events happen by navigating to **Tools > Automations**.

### Usage

**Example of Registering Notification channel and/or template**

```
public function registerSmsNotifications()
{
    return [
        'channels' => [
            'twilio' => \IgniterLabs\SmsNotify\Notifications\Channels\Twilio::class,
        ],
        'template' => [
            'igniterlabs.smsnotify::_sms.new_order' => 'igniterlabs.smsnotify::default.template.text_order_placed',
        ],
    ];
}
```

**Example of a Notification Channel Class**

A notification channel class is responsible for building the settings form and setting the required configuration values.

```
class Twilio extends \IgniterLabs\SmsNotify\Classes\BaseChannel
{
    /**
     * Returns information about this channel, including name and description.
     */
    public function channelDetails()
    {
        return [
            'name'        => 'Twilio SMS Channel',
            'description' => '',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'status' => [
                'label' => 'Status',
                'type' => 'switch',
                'default' => FALSE,
                'span' => 'left',
                'tab' => 'Twilio',
            ],
            'account_sid' => [
                'label' => 'Account SID',
                'type' => 'text',
                'tab' => 'Twilio',
            ],
            ...
        ];
    }
    
    public function send($to, $content)
    {
        //
    }
}
```
