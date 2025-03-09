<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Http\Requests;

use Override;
use Igniter\System\Classes\FormRequest;

class TemplateRequest extends FormRequest
{
    #[Override]
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
