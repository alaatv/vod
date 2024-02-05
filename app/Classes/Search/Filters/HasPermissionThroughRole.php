<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/25/2018
 * Time: 5:25 PM
 */

namespace App\Classes\Search\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HasPermissionThroughRole extends FilterAbstract
{
    protected $attribute = 'name';

    protected $roleRelation = 'roles';

    protected $permissionRewlation = 'permissions';

    /**
     * @param  array  $value
     */
    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if (! is_array($value)) {
            return $builder;
        }

        $resultArray = $this->retrieveUsers($value);

        $callback->success($builder, $resultArray);

        return $builder->whereIn('id', $resultArray);
    }

    private function retrieveUsers(array $value): Collection
    {
        $permissions = array_filter($value);

        return User::getUserWithPermissions($permissions) ?? collect();
    }
}
