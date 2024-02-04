<?php

return [
    'list' => [
        'toolbar' => [
            'buttons' => [
                'create' => [
                    'label' => 'lang:admin::lang.button_new',
                    'class' => 'btn btn-primary',
                    'href' => 'igniterlabs/smsnotify/channels/create',
                ],
            ],
        ],
        'bulkActions' => [
            'status' => [
                'label' => 'lang:admin::lang.list.actions.label_status',
                'type' => 'dropdown',
                'class' => 'btn btn-light',
                'statusColumn' => 'is_enabled',
                'menuItems' => [
                    'enable' => [
                        'label' => 'lang:admin::lang.list.actions.label_enable',
                        'type' => 'button',
                        'class' => 'dropdown-item',
                    ],
                    'disable' => [
                        'label' => 'lang:admin::lang.list.actions.label_disable',
                        'type' => 'button',
                        'class' => 'dropdown-item text-danger',
                    ],
                ],
            ],
            'delete' => [
                'label' => 'lang:admin::lang.button_delete',
                'class' => 'btn btn-light text-danger',
                'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm',
            ],
        ],
        'columns' => [
            'edit' => [
                'type' => 'button',
                'iconCssClass' => 'fa fa-pencil',
                'attributes' => [
                    'class' => 'btn btn-edit',
                    'href' => 'igniterlabs/smsnotify/channels/edit/{id}',
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
            'location_name' => [
                'label' => 'lang:admin::lang.column_location',
                'relation' => 'location',
                'select' => 'location_name',
                'searchable' => true,
                'locationAware' => true,
            ],
            'is_enabled' => [
                'label' => 'lang:admin::lang.label_status',
                'type' => 'switch',
            ],
            'is_default' => [
                'label' => 'lang:igniter.payregister::default.label_default',
                'type' => 'switch',
                'onText' => 'admin::lang.text_yes',
                'offText' => 'admin::lang.text_no',
            ],
            'updated_at' => [
                'label' => 'igniterlabs.smsnotify::default.channel.column_updated_at',
                'type' => 'timetense',
                'searchable' => true,
            ],
            'created_at' => [
                'label' => 'igniterlabs.smsnotify::default.channel.column_created_at',
                'type' => 'timetense',
                'searchable' => true,
            ],
            'id' => [
                'label' => 'lang:admin::lang.column_id',
                'invisible' => true,
            ],
        ],
    ],
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => [
                    'label' => 'lang:admin::lang.button_save',
                    'context' => ['create', 'edit'],
                    'partial' => 'form/toolbar_save_button',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                    'data-progress-indicator' => 'admin::lang.text_saving',
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
                'cssClass' => 'flex-width',
            ],
            'location_id' => [
                'label' => 'lang:admin::lang.label_location',
                'type' => 'relation',
                'relationFrom' => 'location',
                'nameFrom' => 'location_name',
                'span' => 'left',
                'cssClass' => 'flex-width',
                'placeholder' => 'lang:admin::lang.text_please_select',
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
            ],
            'is_default' => [
                'label' => 'lang:igniter.payregister::default.label_default',
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
