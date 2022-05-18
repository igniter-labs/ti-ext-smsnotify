<?php

return [
    'text_tab_sms' => 'SMS',
    'text_tab_alert' => 'Alert (eg. slack)',
    'text_send_to_location_tel' => 'Location phone number (if available)',
    'text_send_to_customer_tel' => 'Customer phone number (if available)',
    'text_send_to_order_tel' => 'Order phone number (if available)',
    'text_send_to_reservation_tel' => 'Reservation phone number (if available)',
    'text_send_to_custom_tel' => 'Specific phone number',

    'setting_title' => 'Configure SMS Channels',
    'setting_desc' => 'Configure twilio, plivo, nexmo or clickatell settings.',

    'label_template' => 'SMS Template',
    'label_send_to' => 'Send To',
    'label_send_to_custom' => 'Send To Phone number',

    'help_template' => 'To customize SMS Templates, go to Design -> SMS Templates.',

    'nexmo' => [
        'text_title' => 'Nexmo SMS Channel',
        'text_desc' => 'Sending SMS notifications using Nexmo.',
    ],

    'twilio' => [
        'text_title' => 'Twilio SMS Channel',
        'text_desc' => 'Sending SMS notifications using Twilio.',
    ],

    'clickatell' => [
        'text_title' => 'Clickatell SMS Channel',
        'text_desc' => 'Sending SMS notifications using Clickatell.',
    ],

    'plivo' => [
        'text_title' => 'Plivo SMS Channel',
        'text_desc' => 'Sending SMS notifications using Plivo.',
    ],

    'channel' => [
        'text_title' => 'SMS Channels',
        'text_new_title' => 'SMS Channel: New',
        'text_edit_title' => 'SMS Channel: Update',
        'text_preview_title' => 'SMS Channel: Preview',
        'text_form_name' => 'SMS Channel',
        'text_empty' => 'No added sms channel',

        'column_label' => 'Label',
        'column_description' => 'Description',
        'column_updated_at' => 'Updated At',
        'column_created_at' => 'Created At',

        'label_channel' => 'Channel',
        'label_label' => 'Label',
        'label_code' => 'Code',
    ],

    'template' => [
        'text_title' => 'SMS Templates',
        'text_new_title' => 'SMS Template: New',
        'text_edit_title' => 'SMS Template: Update',
        'text_preview_title' => 'SMS Template: Preview',
        'text_form_name' => 'SMS Template',
        'text_empty' => 'No added sms template',
        'text_order_placed' => 'Order confirmation sms notification to staff.',
        'text_order_status_changed' => 'Order status changed sms notification to customer.',
        'text_order_assigned' => 'Order assigned sms notification to staff.',
        'text_new_reservation' => 'Reservation confirmation sms notification to staff.',
        'text_reservation_status_changed' => 'Reservation status changed sms notification to customer.',
        'text_reservation_assigned' => 'Reservation assigned sms notification to staff.',
        'text_order_confirmed' => 'Order confirmed sms notification to customer.',
        'text_reservation_confirmed' => 'Reservation confirmed sms notification to customer.',

        'column_name' => 'Name',
        'column_updated_at' => 'Updated At',
        'column_created_at' => 'Created At',

        'label_content' => 'Message',

        'button_test_message' => 'Send Test Message',

        'alert_test_message_sent' => 'Test SMS message successfully sent to %s',
    ],
];
