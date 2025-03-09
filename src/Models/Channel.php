<?php

namespace IgniterLabs\SmsNotify\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Local\Models\Concerns\Locationable;
use Igniter\Local\Models\Location;
use IgniterLabs\SmsNotify\Classes\Manager;

/**
 *
 *
 * @property int $id
 * @property int|null $location_id
 * @property string $code
 * @property string|null $class_name
 * @property string|null $name
 * @property string|null $description
 * @property array<array-key, mixed>|null $config_data
 * @property bool|null $is_enabled
 * @property bool|null $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Igniter\Flame\Database\Builder<static>|Channel applyFilters(array $options = [])
 * @method static \Igniter\Flame\Database\Builder<static>|Channel applySorts(array $sorts = [])
 * @method static \Igniter\Flame\Database\Builder<static>|Channel dropdown(string $column, string $key = null)
 * @method static \Igniter\Flame\Database\Builder<static>|Channel like(string $column, string $value, string $side = 'both', string $boolean = 'and')
 * @method static \Igniter\Flame\Database\Builder<static>|Channel listFrontEnd(array $options = [])
 * @method static \Igniter\Flame\Database\Builder<static>|Channel lists(string $column, string $key = null)
 * @method static \Igniter\Flame\Database\Builder<static>|Channel newModelQuery()
 * @method static \Igniter\Flame\Database\Builder<static>|Channel newQuery()
 * @method static \Igniter\Flame\Database\Builder<static>|Channel orLike(string $column, string $value, string $side = 'both')
 * @method static \Igniter\Flame\Database\Builder<static>|Channel orSearch(string $term, string $columns = [], string $mode = 'all')
 * @method static \Igniter\Flame\Database\Builder<static>|Channel pluckDates(string $column, string $keyFormat = '%Y-%m', string $valueFormat = '%M %Y')
 * @method static \Igniter\Flame\Database\Builder<static>|Channel query()
 * @method static \Igniter\Flame\Database\Builder<static>|Channel search(string $term, string $columns = [], string $mode = 'all')
 * @method static \Igniter\Flame\Database\Builder<static>|Channel whereCode($value)
 * @mixin Model
 */
class Channel extends Model
{
    use Locationable;
    use Purgeable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniterlabs_smsnotify_channels';

    public $timestamps = true;

    /**
     * @var array fillable fields
     */
    protected $fillable = ['id', 'name', 'description', 'code', 'class_name', 'config_data', 'is_enabled', 'is_default', 'location_id'];

    public $relation = [
        'belongsTo' => [
            'location' => Location::class,
        ],
    ];

    protected $casts = [
        'config_data' => 'array',
        'is_enabled' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected $purgeable = ['channel'];

    /**
     * @var self Default channel cache.
     */
    protected static $defaultChannel;

    protected static $configCache;

    protected static $configCacheKey = 'igniterlabs-smsnotify-channel-config';

    public static function getConfig($channelCode = null, $default = null)
    {
        if (!self::$configCache) {
            self::$configCache = self::whereIsEnabled()->get()->mapWithKeys(function(self $model) {
                return [$model->code => $model->config_data];
            })->all();
        }

        return array_get(self::$configCache, $channelCode, $default);
    }

    public static function clearStaticCache()
    {
        self::$configCache = null;
        self::$defaultChannel = null;
    }

    public function getNameAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return ($channelObject = $this->getChannelObject()) ? lang($channelObject->getName()) : null;
    }

    public function getDescriptionAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return ($channelObject = $this->getChannelObject()) ? lang($channelObject->getDescription()) : null;
    }

    //
    // Events
    //

    protected function afterFetch()
    {
        $this->applyChannelClass();

        if (is_array($this->config_data)) {
            $this->attributes = array_merge($this->config_data, $this->attributes);
        }
    }

    protected function beforeSave()
    {
        if (!$this->exists) {
            return;
        }

        if ($this->is_default) {
            $this->makeDefault();
        }

        $data = [];
        $fields = $this->getConfigFields();
        foreach ($fields as $name => $config) {
            if (array_key_exists($name, $this->attributes)) {
                $data[$name] = $this->attributes[$name];
            }
        }

        foreach ($this->attributes as $name => $value) {
            if (!array_key_exists($name, $this->original)) {
                unset($this->attributes[$name]);
            }
        }

        $this->config_data = $data;
    }

    public function isEnabled()
    {
        return $this->is_enabled;
    }

    public function scopeWhereIsEnabled($query)
    {
        return $query->where('is_enabled', 1);
    }

    //
    // Manager
    //

    /**
     * Extends this model with the notification class
     * @return bool
     */
    public function applyChannelClass()
    {
        $className = $this->class_name;
        if (!$className || !class_exists($className)) {
            $className = null;
        }

        if ($className && !$this->isClassExtendedWith($className)) {
            $this->extendClassWith($className);
        }

        $this->class_name = $className;

        return true;
    }

    /**
     * @return \IgniterLabs\SmsNotify\Classes\BaseChannel
     */
    public function getChannelObject()
    {
        return $this->class_name ? $this->asExtension($this->class_name) : null;
    }

    //
    // Helpers
    //

    public function makeDefault()
    {
        throw_unless($this->is_enabled, ApplicationException::class, 'Cannot set default channel when disabled.');

        $this->timestamps = false;
        $this->newQuery()->whereHasOrDoesntHaveLocation($this->location_id)->update(['is_default' => 0]);
        $this->newQuery()->where('id', $this->id)->whereHasOrDoesntHaveLocation($this->location_id)->update(['is_default' => 1]);
        $this->timestamps = true;
    }

    public static function getDefault($locationId = null): ?self
    {
        if (self::$defaultChannel !== null) {
            return self::$defaultChannel;
        }

        $query = self::whereIsEnabled()->where('is_default', true);
        $query->whereHasOrDoesntHaveLocation($locationId);

        if (!$defaultChannel = $query->first()) {
            $query = self::whereIsEnabled();
            $query->whereHasOrDoesntHaveLocation($locationId);

            if ($defaultChannel = $query->first()) {
                /** @var Channel $defaultChannel */
                $defaultChannel->makeDefault();
            }
        }

        /** @var Channel $defaultChannel */
        return self::$defaultChannel = $defaultChannel;
    }

    public static function listChannels()
    {
        $result = [];
        $manager = resolve(Manager::class);
        $channels = self::whereIsEnabled()->get()->keyBy('code');
        foreach ($manager->listChannels() as $code => $className) {
            if (!$channel = $channels->get($code)) {
                continue;
            }

            /** @var Channel $channel */
            $result[$code] = $channel->name;
        }

        return $result;
    }

    /**
     * Synchronise all channels to the database.
     * @return void
     */
    public static function syncAll()
    {
        $manager = resolve(Manager::class);
        $channels = self::pluck('code')->all();
        foreach ($manager->listChannelObjects() as $code => $channelObject) {
            if (in_array($code, $channels)) {
                continue;
            }

            $model = self::make([
                'code' => $code,
                'class_name' => get_class($channelObject),
                'name' => lang($channelObject->getName()),
                'description' => lang($channelObject->getDescription()),
            ]);

            $model->applyChannelClass();
            $model->save();
        }
    }
}
