## SMS Notification channels and messages for TastyIgniter

This extension allows admins to configure sms notifications to be sent out when certain events happen in TastyIgniter. This also includes OTP verification

## Features
- Receive SMS notifications whenever a new order is placed, a product is out of stock, and much more
- SMS alert to your customers about their order or reservation status
- OTP Verification for login and registration
- Customizable SMS Templates
- Add your own admin/customer notification.

**TO-DO:**
- Bulk SMS campaigns & target marketing

### Admin Panel

In the admin go to **System > Settings > SMS Notify Settings** to manage related settings.
Notification channels are managed in the admin panel by navigating to **Tools > SMS Channels**.
Notification messages are managed in the admin panel by navigating to **Design > SMS Templates**.

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
}
```

**Example of a Notification Message Class**

```
class OrderConfirmed extends \IgniterLabs\SmsNotify\Classes\BaseNotification
{
    protected $templateCode = 'igniterlabs.smsnotify::_sms.order_confirmed';
}
```
