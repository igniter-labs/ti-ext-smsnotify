---
title: "SMS Notify"
section: "extensions"
sortOrder: 999
---

## Installation

You can install the extension via composer using the following command:

```bash
composer require igniterlabs/ti-ext-smsnotify -W
```

Run the database migrations to create the required tables:

```bash
php artisan igniter:up
```

## Getting started

From your TastyIgniter Admin, you can manage SMS notification channels and templates by navigating to the **Manage > Settings > Configure SMS Channels** admin page. You can also customize SMS messages in the admin panel by going to **Design > SMS Templates**.

You can use the `SendSmsNotification` Automation rule action to send out notifications when certain events occur by navigating to **Tools > Automations**.

### Using the Send an SMS notification Automation Rule Action

You can use the `SendSmsNotification` Automation rule action to send SMS notifications when certain events occur in your TastyIgniter platform. This action can be configured in the **Tools > Automations** admin page. You can specify the SMS template to use and the recipient.

The `SendSmsNotification` action will automatically resolve the appropriate SMS channel based on the location from the event payload, if available. If no location is provided, it will use the default SMS channel configured in the settings. 

Follow these steps to set up the action:

- Navigate to **Tools > Automations** in your TastyIgniter Admin.
- Click on the **New** button to create a new automation rule.
- Select the event you want to trigger the SMS notification. For example, you can choose the `Order Placed Event` event from the dropdown.
- Click on the **Save** button to proceed to the next step.
- Provide a name for the automation rule in the **Name** field. This will help you identify the rule later.
- In the **Code** field, provide a unique identifier for the automation rule.
- In the **Status** field, select whether you want the automation rule to be active or inactive.
- Under the **Conditions** tab (optional), you can specify any conditions that must be met for the action to be executed. For example, you can send notifications only for delivery orders by selecting the `Order attribute` condition. Once selected, click on it under the **Conditions** tab to configure it.
- Under the **Actions** tab, select the `Send an SMS notification` action from the dropdown. Once selected, click on it under the **Actions** tab to configure it.
- In the **SMS Template** field, select the SMS template you want to use for the notification. This should correspond to the event you selected earlier as the data will be passed to the template from the event payload.
- In the **Send To** field, you can select the recipient of the SMS notification. You can choose from the following options:
  - **Customer phone number**: Sends the SMS to the customer's phone number depending on the event.
  - **Location phone number**: Sends the SMS to the location's phone number depending on the event.
  - **Order phone number**: Sends the SMS to the order's phone number depending on the event.
  - **Reservation phone number**: Sends the SMS to the reservation's phone number depending on the event.
  - **Specific phone number**: Allows you to enter a custom phone number to send the SMS to.
- Click on the **Save** button to save the automation rule.
- After saving, the automation rule will be active and will send SMS notifications based on the specified event and conditions.

## Usage

This section covers how to integrate the SMS Notify extension into your own extension if you need to create custom SMS channels or extend existing ones. The SMS Notify extension provides a simple API for managing SMS notifications.

### Defining SMS Notification Channels

You can define SMS notification channels by creating a class that extends `IgniterLabs\SmsNotify\Classes\BaseChannel`. This class should implement the `channelDetails`, `defineFormConfig`, `getConfigRules` and `send` methods.

Here's an example of how to define a custom SMS notification channel:

```php
use IgniterLabs\SmsNotify\Classes\BaseChannel;

class MyCustomSmsChannel extends BaseChannel
{
    /**
     * Returns information about this channel, including name and description.
     */
    public function channelDetails(): array
    {
        return [
            'name'        => 'My Custom SMS Channel',
            'description' => 'A custom SMS channel for sending notifications.',
        ];
    }

    public function defineFormConfig(): array
    {
        return [
            'status' => [
                'label' => 'Status',
                'type' => 'switch',
                'default' => FALSE,
            ],
            'api_key' => [
                'label' => 'API Key',
                'type' => 'text',
            ],
            // Add more configuration fields as needed
        ];
    }

    public function getConfigRules(): array
    {
        return [
            'status' => ['boolean'],
            'api_key' => ['required', 'string'],
            // Add more validation rules as needed
        ];
    }

    public function send($to, $content): void
    {
        // Implement the logic to send the SMS using the sms gateway API
    }
}
```

### Registering SMS Notification Channels

To register your custom SMS notification channel, you can create a `registerSmsChannels` method in your [Extension class](https://tastyigniter.com/docs/extend/extensions#extension-class) that returns an array of channels where the keys are the channel identifiers and the values are the fully qualified class names of the channel classes.

Here's an example of how to register SMS notification channel:

```php
public function registerSmsChannels()
{
    return [
        'mycustomchannel' => \Author\Extension\SmsChannels\MyCustomSmsChannel::class,
        // Add more channels as needed
    ];
}
```

### Registering SMS Templates

You can register SMS templates in your extension by creating a method `registerSmsTemplates` in your [Extension class](https://tastyigniter.com/docs/extend/extensions#extension-class) that returns an array of templates where the keys are the template identifier and the values are the descriptions of the templates.

Here's an example of how to register SMS templates:

```php
public function registerSmsTemplates()
{
    return [
        'author.extension::sms.new_order' => 'Order confirmation sms notification to customer',
        // Add more templates as needed
    ];
}
```

In the example above, the identifier `author.extension::sms.new_order` should correspond to a view file located in your extension's `resources/views/sms` directory. The description is used to provide context for the template in the admin panel.

### Customizing SMS Templates

You can customize SMS templates by creating a view file in your extension's `resources/views/sms` directory. The view file should have the same name as the template identifier you registered in the `registerSmsTemplates` method.

For example, if you registered a template with the identifier `author.extension::sms.new_order`, you should create a view file named `new_order.blade.php` in the `resources/views/sms` directory of your extension.

The view file can contain any text or HTML content, and you can use the data passed to the `notify` method to dynamically generate the SMS content. Here's an example of a simple SMS template:

```blade
Your order #{{ $data['order_id'] }} has been received, {{ $data['customer_name'] }}! Thank you for choosing us.
```

### Sending SMS Notifications

You can send SMS notifications using the `notify` method of the `IgniterLabs\SmsNotify\Classes\Manager` class. This method accepts the template name, recipient's phone number, an array of data that will be passed to the SMS template, and an optional location `Igniter\Local\Models\Location` model.

Here's an example of how to send an SMS notification:

```php
use IgniterLabs\SmsNotify\Classes\Manager;

$to = '+1234567890'; // Recipient's phone number
$notificationData = [
    'order_id' => 12345,
    'customer_name' => 'John Doe',
];
$location = null; // Optional location model

resolve(Manager::class)->notify('author.extension::sms.new_order', $to, $notificationData, $location);
```

This example sends an SMS notification using the `author.extension::sms.new_order` template defined in your extension. The `data` array can contain any variables that are used in the SMS template. The `location` parameter is optional and is used when determining the SMS channel to use for sending the notification, if you have a location-specific SMS channel.
