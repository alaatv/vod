<?php

namespace App\Models;

use App\Repositories\DraftRepository;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Draft extends Model
{
    use LogsActivity;


    protected static $recordEvents = ['updated', 'created', 'deleted'];
    protected $fillable = ['draftable_id', 'draftable_type', 'accepted_at', 'draft_content', 'author_id'];
    protected $casts = ['draft_content' => 'array'];

    public static function deactivateOthers($draft)
    {
        $draftableDrafts = DraftRepository::allDraftsOnDraftableExceptThis($draft)
            ->whereNotNull('accepted_at')
            ->get();
        foreach ($draftableDrafts as $draft) {
            $draft->update([
                'accepted_at' => null
            ]);
        }
    }

    public function draftable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function getTypeAttribute()
    {
        $type = $this->draftable;
        switch ($type) {
            case ($type instanceof Product):
                return 'محصول';
            case ($type instanceof Content):
                return 'کانتنت';
            default:
                return get_class($type);
        }
    }

    public function getTitleAttribute()
    {
        return $this->draftable->name;

    }

    public function getActivitylogOptions(): LogOptions
    {
        $model = explode('\\', self::class)[1];
        return LogOptions::defaults()
            ->logOnly(['draft_content', 'accepted_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $eventName)
            ->useLogName("{$model}");
    }
}
