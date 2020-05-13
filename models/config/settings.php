<?php

/**
 * Model configuration options for settings model.
 */

return [
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => [
                    'label' => 'lang:admin::lang.button_save',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                ],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-data' => 'close:1',
                ],
            ],
        ],
//        'tabs' => [
        'fields' => [
            'enable_otp_registration' => [
                'tab' => 'igniterlabs.smsnotify::default.settings.text_tab_general',
                'label' => 'igniterlabs.smsnotify::default.settings.label_enable_otp_registration',
                'type' => 'switch',
                'default' => TRUE,
            ],
            'enable_otp_login' => [
                'tab' => 'igniterlabs.smsnotify::default.settings.text_tab_general',
                'label' => 'igniterlabs.smsnotify::default.settings.label_enable_otp_login',
                'type' => 'switch',
                'default' => TRUE,
            ],
            'enable_otp_password_reset' => [
                'tab' => 'igniterlabs.smsnotify::default.settings.text_tab_general',
                'label' => 'igniterlabs.smsnotify::default.settings.label_enable_otp_password_reset',
                'type' => 'switch',
                'default' => TRUE,
            ],
            'enable_otp_checkout' => [
                'tab' => 'igniterlabs.smsnotify::default.settings.text_tab_general',
                'label' => 'igniterlabs.smsnotify::default.settings.label_enable_otp_checkout',
                'type' => 'switch',
                'default' => TRUE,
            ],
            'enable_otp_booking' => [
                'tab' => 'igniterlabs.smsnotify::default.settings.text_tab_general',
                'label' => 'igniterlabs.smsnotify::default.settings.label_enable_otp_booking',
                'type' => 'switch',
                'default' => TRUE,
            ],
            'resend_timer' => [
                'tab' => 'igniterlabs.smsnotify::default.settings.text_tab_general',
                'label' => 'igniterlabs.smsnotify::default.settings.label_resend_timer',
                'type' => 'number',
                'span' => 'left',
                'default' => 15,
            ],
            'max_allowed_resend' => [
                'tab' => 'igniterlabs.smsnotify::default.settings.text_tab_general',
                'label' => 'igniterlabs.smsnotify::default.settings.label_max_allowed_resend',
                'type' => 'number',
                'span' => 'left',
                'default' => 4,
            ],
//            ],
        ],
    ],
];