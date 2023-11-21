<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class TicketDepartment extends BaseModel
{
    public const EDUCATION_DEPARTMENT = 1;
    public const FINANCIAL_DEPARTMENT = 2;
    public const EMPLOYMENT_DEPARTMENT = 3;
    public const PARCHAM_DEPARTMENT = 4;
    public const RAHE_ABRISHAM_DEPARTMENT = 5;
    public const FANI_DEPARTMENT = 6;
    public const MOSHAVERE_KHARID_DEPARTMENT = 7;
    public const MOSHKELAT_MOHRAVA_RAYGAN = 8;
    public const MOSHKELAT_MOHTAVA_POOLI = 9;
    public const TAMAS_BA_MA = 10;
    public const HEMAYAT_MARDOMI = 11;
    public const ACCOUNT_TRANSFER = 12;
    public const TAFTAN_DEPARTMENT = 13;
    public const ARASH_DEPARTMENT = 14;
    public const TETA_DEPARTMENT = 15;
    public const _3A_DEPARTMENT = 16;
    public const HEKMAT_DEPARTMENT = 17;
    public const INTERNAL_FANI_DEPARTMENT = 18;
    public const INTERNAL_FINANCIAL_DEPARTMENT = 19;

    public const INDEX_PAGE_NAME = 'ticketDepartmentPage';

    protected $table = 'ticketDepartments';

    protected $fillable = [
        'ticket_form',
        'grand_id',
        'order',
        'enable',
        'display',
        'title',
        'responders_employees',
    ];

    protected $appends = [
        'children',
        'features',
        'edit_link',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::Class, 'department_id', 'id');
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    public function scopeEnable(Builder $query): Builder
    {
        return $query->where('enable', 1);
    }

    public function scopeDisplay(Builder $query): Builder
    {
        return $query->where('display', 1);
    }

    public function isEnable(): bool
    {
        return $this->enable ? true : false;
    }

    public function getFeaturesAttribute()
    {
        $feature = [];
        if ($this->title == 'مالی') {
            $feature[] = 'showOrdersInTicketCreation';
        }
        return $feature;
    }

    public function getChildrenAttribute()
    {
        $key = 'departmentChildren:'.$this->cacheKey();
        return Cache::tags(['ticket', 'ticketDepartment', 'ticketDepartmentChildren'])
            ->remember($key, config('constants.CACHE_600'), function () {
                return $this->children()->get();
            });
    }

    public function children()
    {
        return $this->hasMany(TicketDepartment::Class, 'parent_id', 'id');
    }

    public function getParentAttribute()
    {
        if (is_null($this->parent_id)) {
            return null;
        }

        $key = 'departmentParent:'.$this->cacheKey();
        return Cache::tags(['ticket', 'ticketDepartment', 'ticketDepartmentParent'])
            ->remember($key, config('constants.CACHE_600'), function () {
                return $this->parent()->first();
            });
    }

    public function parent()
    {
        return $this->belongsTo(TicketDepartment::Class, 'parent_id', 'id');
    }

    public function getTicketFormAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        return json_decode($value);
    }
}
