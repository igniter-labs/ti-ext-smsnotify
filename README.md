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

Use the `SendSmsNotification` Event rule action to send out notification when certain events happen by navigating to **Tools > Event Rules**.

### Usage

**Example of Registering Notification channel and/or template**

```
public function registerSmsNotifications()
{
    return [
        'channels' => [
            \IgniterLabs\SmsNotify\Notifications\Channels\Twilio::class,
        ],
        'template' => [
            \Igniter\Cart\Notifications\OrderPlaced::class,
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

    public function getType()
    {
        return 'sms';
    }

    public function initConfigData()
    {
        Config::set('services.twilio.account_sid', $this->getSetting('account_sid'));
        Config::set('services.twilio.auth_token', $this->getSetting('auth_token'));
        Config::set('services.twilio.from', $this->getSetting('from'));
    }

    public function extendSettingsForm(Form $form)
    {
        $form->addFields([
            'setup' => [
                'type' => 'partial',
                'path' => '$/igniterlabs/smsnotify/notifications/channels/twilio/info',
                'tab' => 'Twilio',
            ],
            'channels[twilio][status]' => [
                'label' => 'Status',
                'type' => 'switch',
                'default' => FALSE,
                'span' => 'left',
                'tab' => 'Twilio',
            ],
            'channels[twilio][account_sid]' => [
                'label' => 'Account SID',
                'type' => 'text',
                'tab' => 'Twilio',
            ],
            'channels[twilio][auth_token]' => [
                'label' => 'Auth Token',
                'type' => 'text',
                'tab' => 'Twilio',
            ],
            'channels[twilio][from]' => [
                'label' => 'Send From Number',
                'type' => 'text',
                'tab' => 'Twilio',
            ],
        ], 'primary');
    }
}
```

**Example of a Notification Template Class**

```
class OrderPlaced extends \IgniterLabs\SmsNotify\Classes\BaseNotification
{
    /**
     * Returns information about this template, including name and description.
     */
    public function templateDetails()
    {
        return [
            'name' => 'Order confirmation notification',
            'description' => 'Order confirmation notification message for all channels',
        ];
    }

    public function defineFormConfig()
    {
        return [
            'data[sms][content]' => [
                'tab' => 'SMS',
                'label' => 'Content',
                'type' => 'textarea',
                'default' => 'Order {order_id} has been received and will be with you shortly.',
            ],
            'data[alert][subject]' => [
                'tab' => 'Alert (eg. slack)',
                'label' => 'Subject',
                'type' => 'text',
                'default' => 'You received a new order!',
            ],
            'data[alert][title]' => [
                'tab' => 'Alert (eg. slack)',
                'label' => 'Title',
                'type' => 'text',
                'default' => 'Order ID: {order_id}',
            ],
            'data[alert][content]' => [
                'tab' => 'Alert (eg. slack)',
                'label' => 'Content',
                'type' => 'textarea',
                'default' => 'You just received a new order {order_id} at {location.location_name}.',
            ],
        ];
    }

    public function defineValidationRules()
    {
        return [
            ['data.sms.content', 'SMS Content', 'required|string|max:255'],
            ['data.alert.subject', 'Alert Subject', 'required|string|max:255'],
            ['data.alert.title', 'Alert Title', 'required|string|max:255'],
            ['data.alert.content', 'Alert Content', 'required|string|max:255'],
        ];
    }

    public function getActionUrl($notifiable)
    {
        return admin_url('order/edit/'.$this->parameters->get('order_id'));
    }
}
```
