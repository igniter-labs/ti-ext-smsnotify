<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Models;

use Igniter\Flame\Database\Builder;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Local\Models\Concerns\Locationable;
use Igniter\Local\Models\Location;
use IgniterLabs\SmsNotify\Classes\BaseChannel;
use IgniterLabs\SmsNotify\Classes\Manager;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property int $id
 * @property int|null $location_id
 * @property string $code
 * @property string|null $class_name
 * @property string|null $name
 * @property string|null $description
 * @property array<array-key, mixed>|null $config_data
 * @property bool|null $is_enabled
 * @property bool|null $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|Channel applyFilters(array $options = [])
 * @method static Builder<static>|Channel applySorts(array $sorts = [])
 * @method static Builder<static>|Channel dropdown(string $column, string $key = null)
 * @method static Builder<static>|Channel like(string $column, string $value, string $side = 'both', string $boolean = 'and')
 * @method static Builder<static>|Channel listFrontEnd(array $options = [])
 * @method static Builder<static>|Channel lists(string $column, string $key = null)
 * @method static Builder<static>|Channel newModelQuery()
 * @method static Builder<static>|Channel newQuery()
 * @method static Builder<static>|Channel orLike(string $column, string $value, string $side = 'both')
 * @method static Builder<static>|Channel orSearch(string $term, string $columns = [], string $mode = 'all')
 * @method static Builder<static>|Channel pluckDates(string $column, string $keyFormat = '%Y-%m', string $valueFormat = '%M %Y')
 * @method static Builder<static>|Channel query()
 * @method static Builder<static>|Channel search(string $term, string $columns = [], string $mode = 'all')
 * @method static Builder<static>|Channel whereCode($value)
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
     * @var null|self Default channel cache.
     */
    protected static $defaultChannel;

    protected static $configCache;

    protected static $configCacheKey = 'igniterlabs-smsnotify-channel-config';

    public static function getConfig($channelCode = null, $default = null)
    {
        if (!self::$configCache) {
            self::$configCache = self::whereIsEnabled()->get()->mapWithKeys(fn(self $model): array => [$model->code => $model->config_data])->all();
        }

        return array_get(self::$configCache, $channelCode, $default);
    }

    public static function clearStaticCache(): void
    {
        self::$configCache = null;
        self::$defaultChannel = null;
    }

    public function getNameAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return (($channelObject = $this->getChannelObject()) instanceof BaseChannel) ? lang($channelObject->getName()) : null;
    }

    public function getDescriptionAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return (($channelObject = $this->getChannelObject()) instanceof BaseChannel) ? lang($channelObject->getDescription()) : null;
    }

    //
    // Events
    //

    #[Override]
    protected function afterFetch()
    {
        $this->applyChannelClass();

        if (is_array($this->config_data)) {
            $this->attributes = array_merge($this->config_data, $this->attributes);
        }
    }

    #[Override]
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
     */
    public function applyChannelClass(): bool
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

    public function getChannelObject(): ?BaseChannel
    {
        return $this->class_name ? $this->asExtension($this->class_name) : null;
    }

    //
    // Helpers
    //

    public function makeDefault(): void
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
     */
    public static function syncAll(): void
    {
        $manager = resolve(Manager::class);
        $channels = self::pluck('code')->all();
        foreach ($manager->listChannelObjects() as $code => $channelObject) {
            if (in_array($code, $channels)) {
                continue;
            }

            $model = self::make([
                'code' => $code,
                'class_name' => $channelObject::class,
                'name' => lang($channelObject->getName()),
                'description' => lang($channelObject->getDescription()),
            ]);

            $model->applyChannelClass();
            $model->save();
        }
    }
}
