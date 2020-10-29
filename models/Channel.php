<?php

namespace IgniterLabs\SmsNotify\Models;

use Igniter\Flame\Database\Traits\Purgeable;
use IgniterLabs\SmsNotify\Classes\Manager;

class Channel extends \Model
{
    use Purgeable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniterlabs_smsnotify_channels';

    public $timestamps = TRUE;

    /**
     * @var array fillable fields
     */
    protected $fillable = ['code', 'class_name', 'config_data', 'is_enabled', 'is_default'];

    public $casts = [
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
            self::$configCache = self::whereIsEnabled()->get()->mapWithKeys(function ($model) {
                return [$model->code => $model->config_data];
            })->all();
        }

        return array_get(self::$configCache, $channelCode, $default);
    }

    public function getNameAttribute()
    {
        return $this->class_name ? lang($this->getChannelObject()->getName()) : null;
    }

    public function getDescriptionAttribute()
    {
        return $this->class_name ? lang($this->getChannelObject()->getDescription()) : null;
    }

    //
    // Events
    //

    protected function afterFetch()
    {
        $this->applyChannelClass();

        if (is_array($this->config_data))
            $this->attributes = array_merge($this->config_data, $this->attributes);
    }

    protected function beforeSave()
    {
        if (!$this->exists)
            return;

        if ($this->is_default)
            $this->makeDefault();

        $data = [];
        $fields = $this->getConfigFields();
        foreach ($fields as $name => $config) {
            if (!array_key_exists($name, $this->attributes)) continue;
            $data[$name] = $this->attributes[$name];
        }

        foreach ($this->attributes as $name => $value) {
            if (in_array($name, $this->fillable)) continue;
            unset($this->attributes[$name]);
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
        if (!$className OR !class_exists($className))
            $className = null;

        if ($className AND !$this->isClassExtendedWith($className)) {
            $this->extendClassWith($className);
        }

        $this->class_name = $className;

        return TRUE;
    }

    /**
     * @return \IgniterLabs\SmsNotify\Classes\BaseChannel
     */
    public function getChannelObject()
    {
        return $this->asExtension($this->class_name);
    }

    //
    // Helpers
    //

    public function makeDefault()
    {
        if (!$this->is_enabled) {
            return FALSE;
        }

        $this->timestamps = FALSE;
        $this->newQuery()->where('is_default', '!=', 0)->update(['is_default' => 0]);
        $this->newQuery()->where('id', $this->id)->update(['is_default' => 1]);
        $this->timestamps = TRUE;
    }

    public static function getDefault($id = null)
    {
        if (self::$defaultChannel !== null) {
            return self::$defaultChannel;
        }

        $defaultChannel = self::whereIsEnabled()->where('is_default', TRUE)->first();

        if (!$defaultChannel) {
            if ($defaultChannel = self::whereIsEnabled()->first()) {
                $defaultChannel->makeDefault();
            }
        }

        return self::$defaultChannel = $defaultChannel;
    }

    public static function listChannels()
    {
        $result = [];
        $manager = Manager::instance();
        $channels = self::whereIsEnabled()->get()->keyBy('code');
        foreach ($manager->listChannels() as $code => $className) {
            if (!$channel = $channels->get($code))
                continue;

            $result[$code] = $channel->getName();
        }

        return $result;
    }

    /**
     * Synchronise all channels to the database.
     * @return void
     */
    public static function syncAll()
    {
        $manager = Manager::instance();
        $channels = self::pluck('code')->all();
        foreach ($manager->listChannels() as $code => $className) {
            if (in_array($code, $channels)) continue;

            $model = self::make([
                'code' => $code,
                'class_name' => $className,
            ]);

            $model->applyChannelClass();
            $model->save();
        }
    }
}
