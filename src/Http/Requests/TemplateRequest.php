<?php

namespace IgniterLabs\SmsNotify\Http\Requests;

use Igniter\System\Classes\FormRequest;

class TemplateRequest extends FormRequest
{
    public function attributes(): array
    {
        return [
            'name' => lang('igniter::admin.label_name'),
            'content' => lang('igniterlabs.smsnotify::default.template.label_content'),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'content' => ['required', 'string'],
        ];
    }

}
