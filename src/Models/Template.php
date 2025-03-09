<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Models;

use Igniter\Flame\Database\Builder;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\Validation;
use Igniter\Flame\Mail\MailParser;
use IgniterLabs\SmsNotify\Classes\Manager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Override;

/**
 * @property int $id
 * @property string $code
 * @property string|null $name
 * @property string|null $content
 * @property int|null $is_custom
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|Template applyFilters(array $options = [])
 * @method static Builder<static>|Template applySorts(array $sorts = [])
 * @method static Builder<static>|Template dropdown(string $column, string $key = null)
 * @method static Builder<static>|Template like(string $column, string $value, string $side = 'both', string $boolean = 'and')
 * @method static Builder<static>|Template listFrontEnd(array $options = [])
 * @method static Builder<static>|Template lists(string $column, string $key = null)
 * @method static Builder<static>|Template newModelQuery()
 * @method static Builder<static>|Template newQuery()
 * @method static Builder<static>|Template orLike(string $column, string $value, string $side = 'both')
 * @method static Builder<static>|Template orSearch(string $term, string $columns = [], string $mode = 'all')
 * @method static Builder<static>|Template pluckDates(string $column, string $keyFormat = '%Y-%m', string $valueFormat = '%M %Y')
 * @method static Builder<static>|Template query()
 * @method static Builder<static>|Template search(string $term, string $columns = [], string $mode = 'all')
 * @mixin Model
 */
class Template extends Model
{
    use Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniterlabs_smsnotify_templates';

    public $timestamps = true;

    /**
     * @var array fillable fields
     */
    protected $guarded = [];

    public $rules = [
        ['code', 'igniter.pages::default.menu.label_code', 'required|unique:igniterlabs_smsnotify_templates,code'],
        ['name', 'igniter.pages::default.menu.label_title', 'required|max:128'],
        ['content', 'admin::lang.label_description', 'string'],
    ];

    protected $injectUniqueIdentifier = true;

    public function getNameAttribute($value)
    {
        $name = empty($this->attributes['name']) ? '' : $this->attributes['name'];

        return is_lang_key($name) ? lang($name) : $name;
    }

    //
    // Events
    //

    #[Override]
    protected function afterFetch()
    {
        if (!$this->is_custom) {
            $this->fillFromView();
        }
    }

    //
    // Helpers
    //

    public function fillFromContent($content): void
    {
        $this->fillFromSections(MailParser::parse($content));
    }

    public function fillFromView(): void
    {
        $this->fillFromSections(self::getTemplateSections($this->code));
    }

    protected function fillFromSections(array $sections)
    {
        $this->content = array_get($sections, 'html');
    }

    protected static function getTemplateSections($code): array
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
     */
    public static function syncAll(): void
    {
        $templates = (array)resolve(Manager::class)->getRegisteredTemplates();
        $dbTemplates = self::pluck('is_custom', 'code')->all();
        $newTemplates = array_diff_key($templates, $dbTemplates);

        foreach ($dbTemplates as $code => $is_custom) {
            if ($is_custom) {
                continue;
            }

            if (!array_key_exists($code, $templates)) {
                self::whereCode($code)->delete();
            }
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
