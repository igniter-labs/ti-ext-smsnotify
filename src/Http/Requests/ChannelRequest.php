<?php

namespace IgniterLabs\SmsNotify\Http\Requests;

use Igniter\System\Classes\FormRequest;

class ChannelRequest extends FormRequest
{
    public function attributes(): array
    {
        return [
            'channel' => lang('igniterlabs.smsnotify::default.channel.label_channel'),
            'name' => lang('igniterlabs.smsnotify::default.channel.label_label'),
            'location_id' => lang('igniter::admin.label_location'),
            'code' => lang('igniterlabs.smsnotify::default.channel.label_code'),
            'description' => lang('igniter::admin.label_description'),
            'is_default' => lang('igniter.payregister::default.label_default'),
            'is_enabled' => lang('igniter::admin.label_status'),
        ];
    }

    public function rules(): array
    {
        $rules = $this->isEditFormContext()
            ? $this->route()->getController()->getFormModel()->getConfigRules() ?? []
            : [];

        return array_merge([
            'channel' => ['sometimes', 'required', 'alpha_dash'],
            'name' => ['sometimes', 'required', 'min:2', 'max:128'],
            'code' => ['sometimes', 'required', 'alpha_dash', 'unique:igniterlabs_smsnotify_channels,code'],
            'description' => ['sometimes', 'required', 'max:255'],
            'is_default' => ['required', 'integer'],
            'is_enabled' => ['required', 'integer'],
        ], $rules);
    }

    protected function isEditFormContext(): bool
    {
        return $this->route()->getController()->getFormContext() === 'edit';
    }
}
