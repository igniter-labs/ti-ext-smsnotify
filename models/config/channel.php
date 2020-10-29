<?php

return [
    'list' => [
        'toolbar' => [
            'buttons' => [
                'back' => [
                    'label' => 'lang:admin::lang.button_icon_back',
                    'class' => 'btn btn-default',
                    'href' => 'settings',
                ],
                'create' => ['label' => 'lang:admin::lang.button_new', 'class' => 'btn btn-primary', 'href' => 'igniterlabs/smsnotify/channels/create'],
                'delete' => ['label' => 'lang:admin::lang.button_delete', 'class' => 'btn btn-danger', 'data-request-form' => '#list-form', 'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'", 'data-request-data' => "_method:'DELETE'", 'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm'],
            ],
        ],
        'columns' => [
            'edit' => [
                'type' => 'button',
                'iconCssClass' => 'fa fa-pencil',
                'attributes' => [
                    'class' => 'btn btn-edit',
                    'href' => 'igniterlabs/smsnotify/channels/edit/{code}',
                ],
            ],
            'name' => [
                'label' => 'igniterlabs.smsnotify::default.channel.column_label',
                'type' => 'text',
            ],
            'description' => [
                'label' => 'igniterlabs.smsnotify::default.channel.column_description',
                'type' => 'text',
            ],
            'is_enabled' => [
                'label' => 'lang:admin::lang.label_status',
                'type' => 'switch',
            ],
            'is_default' => [
                'label' => 'lang:admin::lang.payments.label_default',
                'type' => 'switch',
                'onText' => 'admin::lang.text_yes',
                'offText' => 'admin::lang.text_no',
            ],
            'updated_at' => [
                'label' => 'igniterlabs.smsnotify::default.channel.column_updated_at',
                'type' => 'timetense',
                'searchable' => TRUE,
            ],
            'created_at' => [
                'label' => 'igniterlabs.smsnotify::default.channel.column_created_at',
                'type' => 'timetense',
                'searchable' => TRUE,
            ],
            'id' => [
                'label' => 'lang:admin::lang.column_id',
                'invisible' => TRUE,
            ],
        ],
    ],
    'form' => [
        'toolbar' => [
            'buttons' => [
                'back' => [
                    'label' => 'lang:admin::lang.button_icon_back',
                    'class' => 'btn btn-default',
                    'href' => 'igniterlabs/smsnotify/channels',
                ],
                'save' => ['label' => 'lang:admin::lang.button_save', 'class' => 'btn btn-primary', 'data-request' => 'onSave'],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-data' => 'close:1',
                ],
            ],
        ],
        'fields' => [
            'channel' => [
                'label' => 'igniterlabs.smsnotify::default.channel.label_channel',
                'type' => 'select',
                'options' => 'listChannels',
                'context' => ['create'],
                'placeholder' => 'lang:admin::lang.text_please_select',
            ],
            'name' => [
                'label' => 'igniterlabs.smsnotify::default.channel.label_label',
                'type' => 'text',
                'span' => 'left',
                'context' => ['edit'],
            ],
            'code' => [
                'label' => 'igniterlabs.smsnotify::default.channel.label_code',
                'type' => 'text',
                'span' => 'right',
            ],
            'description' => [
                'label' => 'lang:admin::lang.label_description',
                'type' => 'textarea',
                'span' => 'left',
                'context' => ['edit'],
            ],
            'is_default' => [
                'label' => 'lang:admin::lang.payments.label_default',
                'type' => 'switch',
                'span' => 'right',
                'cssClass' => 'flex-width',
            ],
            'is_enabled' => [
                'label' => 'lang:admin::lang.label_status',
                'type' => 'switch',
                'span' => 'right',
                'cssClass' => 'flex-width',
            ],
        ],
    ],
];
