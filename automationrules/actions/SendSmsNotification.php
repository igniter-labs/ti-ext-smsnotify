<?php

namespace IgniterLabs\SmsNotify\AutomationRules\Actions;

use Igniter\Automation\Classes\BaseAction;
use Igniter\Flame\Exception\ApplicationException;
use IgniterLabs\SmsNotify\Classes\Notifier;
use IgniterLabs\SmsNotify\Models\Template;
use IgniterLabs\SmsNotify\SmSNotifications\AnonymousNotification;
use Illuminate\Database\Eloquent\Model;

class SendSmsNotification extends BaseAction
{
    public function actionDetails()
    {
        return [
            'name' => 'Send an SMS notification',
            'description' => 'Send an SMS to a recipient',
        ];
    }

    public function defineFormFields()
    {
        return [
            'fields' => [
                'template' => [
                    'label' => 'igniterlabs.smsnotify::default.label_template',
                    'type' => 'select',
                    'comment' => 'igniterlabs.smsnotify::default.help_template',
                ],
                'send_to' => [
                    'label' => 'igniterlabs.smsnotify::default.label_send_to',
                    'type' => 'select',
                ],
                'custom' => [
                    'label' => 'igniterlabs.smsnotify::default.label_send_to_custom',
                    'type' => 'text',
                    'trigger' => [
                        'action' => 'show',
                        'field' => 'send_to',
                        'condition' => 'value[custom]',
                    ],
                ],
            ],
        ];
    }

    public function triggerAction($params)
    {
        if (!$object = $this->shouldTrigger($params))
            return;

        $templateCode = $this->model->template;
        $sendToNumber = $this->getRecipientAddress($object);

        if (!$sendToNumber OR !$templateCode)
            throw new ApplicationException('Send SMS event rule is missing a valid send to or template value');

        $notification = (new AnonymousNotification($object))->template($templateCode);

        (new Notifier)->notifyNow($sendToNumber, $notification, $params);
    }

    public function getTemplateOptions()
    {
        return Template::get()->pluck('name', 'code');
    }

    public function getSendToOptions()
    {
        return [
            'location' => 'igniterlabs.smsnotify::default.text_send_to_location_tel',
            'customer' => 'igniterlabs.smsnotify::default.text_send_to_customer_tel',
            'order' => 'igniterlabs.smsnotify::default.text_send_to_order_tel',
            'custom' => 'igniterlabs.smsnotify::default.text_send_to_custom_tel',
        ];
    }

    protected function shouldTrigger($params)
    {
        $object = array_get($params, 'order', array_get($params, 'reservation'));

        return $object instanceof Model ? $object : FALSE;
    }

    protected function getRecipientAddress($object)
    {
        $mode = $this->model->send_to;

        switch ($mode) {
            case 'custom':
                return $this->model->custom;
            case 'location':
                return optional($object->location)->location_telephone;
            case 'customer':
                return optional($object->customer)->telephone ?? $object->telephone;
            case 'order':
            case 'reservation':
                return $object->telephone;
        }
    }
}
