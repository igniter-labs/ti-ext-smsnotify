<?php

namespace IgniterLabs\SmsNotify\Models;

use Model;

/**
 * @method static instance()
 */
class Settings extends Model
{
    public $implement = ['System\Actions\SettingsModel'];

    public $settingsCode = 'igniterlabs_smsnotify_settings';

    public $settingsFieldsConfig = 'settings';
}