<?php

namespace IgniterLabs\SmsNotify\AutomationRules\Actions;

use Admin\Models\Locations_model;
use Igniter\Automation\Classes\BaseAction;
use Igniter\Flame\Exception\ApplicationException;
use IgniterLabs\SmsNotify\Classes\Notifier;
use IgniterLabs\SmsNotify\Models\Template;
use IgniterLabs\SmsNotify\SmSNotifications\AnonymousNotification;

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
        $templateCode = $this->model->template;
        $sendToNumber = $this->getRecipientAddress($params);

        if (!$sendToNumber OR !$templateCode)
            throw new ApplicationException('Send SMS event rule is missing a valid send to or template value');

        $fromNo = Locations_model::getDefault()->location_telephone;
        $notification = (new AnonymousNotification())->template($templateCode);

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

    protected function getRecipientAddress($params)
    {
        $mode = $this->model->send_to;

        switch ($mode) {
            case 'custom':
                return $this->model->custom;
            case 'location':
                $location = array_get($params, 'location');

                return !empty($location->location_telephone)
                    ? $location->location_telephone : null;
            case 'customer':
                $customer = array_get($params, 'customer');

                return !empty($customer->telephone)
                    ? $customer->telephone : null;
            case 'order':
                $order = array_get($params, 'order');

                return !empty($order->telephone)
                    ? $order->telephone : null;
        }
    }
}