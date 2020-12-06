<?php

namespace IgniterLabs\SmsNotify\Models;

use Igniter\Flame\Database\Traits\Validation;
use Igniter\Flame\Mail\MailParser;
use Igniter\Flame\Support\Facades\File;
use IgniterLabs\SmsNotify\Classes\Manager;
use Illuminate\Support\Facades\View;

class Template extends \Model
{
    use Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniterlabs_smsnotify_templates';

    public $timestamps = TRUE;

    /**
     * @var array fillable fields
     */
    protected $guarded = [];

    public $rules = [
        ['code', 'igniter.pages::default.menu.label_code', 'required|unique:igniterlabs_smsnotify_templates,code'],
        ['name', 'igniter.pages::default.menu.label_title', 'required|max:128'],
        ['content', 'admin::lang.label_description', 'string'],
    ];

    protected $injectUniqueIdentifier = TRUE;

    public function getNameAttribute($value)
    {
        $name = !empty($this->attributes['name']) ? $this->attributes['name'] : '';

        return is_lang_key($name) ? lang($name) : $name;
    }

    //
    // Events
    //

    protected function afterFetch()
    {
        if (!$this->is_custom) {
            $this->fillFromView();
        }
    }

    //
    // Helpers
    //

    public function fillFromContent($content)
    {
        $this->fillFromSections(MailParser::parse($content));
    }

    public function fillFromView()
    {
        $this->fillFromSections(self::getTemplateSections($this->code));
    }

    protected function fillFromSections(array $sections)
    {
        $this->content = array_get($sections, 'html');
    }

    protected static function getTemplateSections($code)
    {
        return MailParser::parse(File::get(View::make($code)->getPath()));
    }

    //
    //
    //

    public static function findOrMakeTemplate($code)
    {
        if (!$template = self::whereCode($code)->first()) {
            $template = new self;
            $template->code = $code;
            $template->fillFromView();
        }

        return $template;
    }

    /**
     * Synchronise all templates to the database.
     * @return void
     */
    public static function syncAll()
    {
        $templates = (array)Manager::instance()->getRegisteredTemplates();
        $dbTemplates = self::pluck('is_custom', 'code')->all();
        $newTemplates = array_diff_key($templates, $dbTemplates);

        foreach ($dbTemplates as $code => $is_custom) {
            if ($is_custom)
                continue;

            if (!array_key_exists($code, $templates))
                self::whereCode($code)->delete();
        }

        foreach ($newTemplates as $code => $name) {
            $model = self::make();
            $model->code = $code;
            $model->name = $name;
            $model->is_custom = 0;
            $model->save();
        }
    }
}
