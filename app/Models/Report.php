<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Report extends BaseModel
{
    use HasFactory;
    use DateTrait;
    use LogsActivity;

    public const DEFAULT_TYPE = 1;
    public $timestamps = false;
    public bool $logFillable = true;
    protected $fillable = [
        'status_id',
        'creator_id',
        'type_id',
        'title',
        'file',
        'data',
        'created_at',
        'from',
        'to',
    ];


    // relations

    public static function generateFileName(string $action, ?string $month = null, ?string $order = null): string
    {
        $date = now('Asia/Tehran')->format('Y-m-d');
        $static = new static();
        $jalaliDate = $static->convertDate($date, 'toJalali');
        $jalaliDate = explode('/', $jalaliDate);

        if (isset($order)) {
//            $order = isset($order) ? self::AUDIT_ORDERS[$order] : '';
            $year = $jalaliDate[0];
            $datePrefix = $month.'_'.$year;
            return "گزارش_{$action}_{$datePrefix}.xlsx";
        }

        $jalaliDate = implode('_', array_reverse($jalaliDate));

        return "گزارش_{$action}_{$jalaliDate}.xlsx";
    }

    public static function getAccessibleTypes(): array
    {
        $accessibleTypes = [];

        /** @var  User $user */
        if (!$user = auth()?->user()) {
            return $accessibleTypes;
        }

        $reportTypes = ReportType::all();
        $userPermissions = $user->getPermissionsThroughRoles()->pluck('name')->toArray();

        foreach ($reportTypes as $type) {
            $permission = self::permissionMaker($type->title);
            if (in_array($permission, $userPermissions)) {
                $accessibleTypes[] = $type->id;
            }
        }
        return $accessibleTypes;
    }

    public static function permissionMaker(string $reportTitle): string
    {
        return 'see'.Str::ucfirst($reportTitle).'Report';
    }

    public function status()
    {
        return $this->hasOne(ReportStatus::class, 'id', 'status_id');
    }

    // accessors

    public function type()
    {
        return $this->hasOne(ReportType::class, 'id', 'type_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject');
    }

    public function gateway()
    {
        return Transactiongateway::find($this->data?->gateway_id ?? null);
    }

    public function getLinkAttribute()
    {
        if ($this->file) {
            return Uploader::privateUrl(config('disks.GROUP_REGISTRATION_REPORT_MINIO'), 60 * 10,
                fileName: $this->file);
        }
    }

    // statics

    public function getCreatedAtAttribute($value)
    {
        return $this->convertDate($value, 'toJalali');
    }

    public function getTypeTitleAttribute()
    {
        return $this->type->title_display_name;
    }

    public function getDataAttribute()
    {
        return json_decode($this->getRawOriginal('data'));
    }

    public function getActivitylogOptions(): LogOptions
    {
        $model = explode('\\', self::class)[1];
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName
            ) => (auth()->check()) ? $eventName : $eventName.self::$console_description)
            ->useLogName("{$model}");
    }
}
