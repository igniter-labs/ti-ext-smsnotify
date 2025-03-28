<?php

return [
    'list' => [
        'toolbar' => [
            'buttons' => [],
        ],
        'columns' => [
            'edit' => [
                'type' => 'button',
                'iconCssClass' => 'fa fa-pencil',
                'attributes' => [
                    'class' => 'btn btn-edit',
                    'href' => 'igniterlabs/smsnotify/templates/edit/{id}',
                ],
            ],
            'name' => [
                'label' => 'admin::lang.label_name',
                'type' => 'text',
            ],
            'updated_at' => [
                'label' => 'igniterlabs.smsnotify::default.template.column_updated_at',
                'type' => 'timetense',
                'searchable' => true,
            ],
            'created_at' => [
                'label' => 'igniterlabs.smsnotify::default.template.column_created_at',
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
                'save' => ['label' => 'lang:admin::lang.button_save', 'class' => 'btn btn-primary', 'data-request' => 'onSave'],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-data' => 'close:1',
                ],
                'test_message' => [
                    'label' => 'lang:igniterlabs.smsnotify::default.template.button_test_message',
                    'class' => 'btn btn-default',
                    'data-request' => 'onTestTemplate',
                    'context' => 'edit',
                ],
            ],
        ],
        'fields' => [
            'name' => [
                'label' => 'igniter::admin.label_name',
                'type' => 'text',
                'span' => 'left',
            ],
            'code' => [
                'label' => 'igniter::admin.label_code',
                'type' => 'text',
                'span' => 'right',
            ],
        ],
        'tabs' => [
            'fields' => [
                'content' => [
                    'tab' => 'igniterlabs.smsnotify::default.template.label_content',
                    'type' => 'textarea',
                    'span' => 'left',
                    'cssClass' => 'col-md-8 pl-0',
                ],
                '_variables' => [
                    'tab' => 'igniterlabs.smsnotify::default.template.label_content',
                    'type' => 'partial',
                    'path' => 'igniter.system::/mailtemplates/variables',
                    'span' => 'right',
                    'cssClass' => 'col-md-4 pr-0',
                    'options' => [\Igniter\System\Models\MailTemplate::class, 'getVariableOptions'],
                ],
            ],
        ],
    ],
];
