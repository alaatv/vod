<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/25/2018
 * Time: 5:25 PM
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class HasPermissionThroughRole extends FilterAbstract
{
    protected $attribute = 'name';

    protected $roleRelation = 'roles';

    protected $permissionRewlation = 'permissions';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        return $builder->whereHas($this->roleRelation, function ($q) use ($value) {
            $q->whereHas($this->permissionRewlation, function ($q2) use ($value) {
                $q2->where($this->attribute, $value);
            });
        });
    }
}
