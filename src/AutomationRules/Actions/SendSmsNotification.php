<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\AutomationRules\Actions;

use Igniter\Automation\AutomationException;
use Igniter\Automation\Classes\BaseAction;
use Igniter\Cart\Models\Order;
use Igniter\Reservation\Models\Reservation;
use IgniterLabs\SmsNotify\Classes\Manager;
use IgniterLabs\SmsNotify\Models\Template;
use Illuminate\Database\Eloquent\Model;
use Override;

class SendSmsNotification extends BaseAction
{
    #[Override]
    public function actionDetails()
    {
        return [
            'name' => 'Send an SMS notification',
            'description' => 'Send an SMS to a recipient',
        ];
    }

    #[Override]
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

    #[Override]
    public function triggerAction($params): void
    {
        if (!$object = $this->shouldTrigger($params)) {
            return;
        }

        if (!$templateCode = $this->model->template) {
            throw new AutomationException('SendSmsNotification: Missing a valid mail template');
        }

        if (!$sendToNumber = $this->getRecipientAddress($object)) {
            throw new AutomationException('SendSmsNotification: Missing a valid send to number from the event payload');
        }

        resolve(Manager::class)->notify($templateCode, $sendToNumber, $params, $object->location);
    }

    public function getTemplateOptions()
    {
        return Template::get()->pluck('name', 'code');
    }

    public function getSendToOptions(): array
    {
        return [
            'location' => 'igniterlabs.smsnotify::default.text_send_to_location_tel',
            'customer' => 'igniterlabs.smsnotify::default.text_send_to_customer_tel',
            'order' => 'igniterlabs.smsnotify::default.text_send_to_order_tel',
            'reservation' => 'igniterlabs.smsnotify::default.text_send_to_reservation_tel',
            'custom' => 'igniterlabs.smsnotify::default.text_send_to_custom_tel',
        ];
    }

    protected function shouldTrigger($params): Order|Reservation|false
    {
        $object = array_get($params, 'order', array_get($params, 'reservation'));

        return $object instanceof Model ? $object : false;
    }

    protected function getRecipientAddress($object)
    {
        $mode = $this->model->send_to;

        return match ($mode) {
            'custom' => $this->model->custom,
            'location' => optional($object->location)->location_telephone,
            'customer' => optional($object->customer)->telephone ?? $object->telephone,
            'order', 'reservation' => $object->telephone,
            default => null,
        };
    }
}
