<?php


namespace App\Traits;


use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait logger
{
    use LogsActivity;

    protected static $recordEvents = ['updated', 'deleted'];
    protected static $console_description = ' from console';

    public function getActivitylogOptions(): LogOptions
    {
        $model = explode('\\', self::class)[1];
        return LogOptions::defaults()
            ->logOnly(self::LOG_ATTRIBUTES)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName
            ) => (auth()->check()) ? $eventName : $eventName.self::$console_description)
            ->useLogName("{$model}");
    }


}
