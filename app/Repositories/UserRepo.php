<?php

namespace App\Repositories;

use App\Models\BonyadEhsanConsultant;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserRepo
{
    protected const HIDE_APPENDS = [
        'info', 'full_name', 'userstatus', 'roles', 'jalaliCreatedAt', 'jalaliUpdatedAt',
    ];

    public static function getSupportEmployees(): Builder
    {
        return User::whereHas('roles', function ($q) {
            $q->whereHas('permissions', function ($q2) {
                $q2->where('name', 'answerTicket');
            });
        });
    }

    public static function admins()
    {
        return User::whereHas('roles');
    }

    public static function withRoles(array $rolls)
    {
        return User::role($rolls);
    }

    /**
     * @param $userParams
     * @return User|Model
     */
    public static function create($userParams)
    {
        $user = User::create($userParams);
        return $user;
    }

    public static function whereHasMobileAndDoNotHaveProducts(array $mobiles, array $products)
    {
        return User::query()->whereIn('mobile', $mobiles)
            ->whereDoesntHave('orders', function (Builder $orderBuilder) use ($products) {
                $orderBuilder->paidAndClosed()
                    ->whereHas('orderproducts', function (Builder $orderProductBuilder) use ($products) {
                        $orderProductBuilder->whereIn('product_id', $products);
                    });
            });
    }

    public static function subsetsUserIds($id, $userIds)
    {
        $newIds = User::insertedByIds($id)->bonyadUser();
        if ($newIds->isEmpty()) {
            $userIds = $userIds->merge($id);
            return $userIds->unique();
        }
        $userIds = $userIds->merge($newIds);

        return self::subsetsUserIds($newIds, $userIds);
    }

    public static function userAccess($authUserId, $id, $roles)
    {
        $user = User::where('id', $id)->whereHas('parents', function ($query) use ($authUserId) {
            return $query->where('id', $authUserId);
        })->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->get();
        return $user->isNotEmpty();
    }

    public static function usersByRole($authUserId, $role, bool $subLevel = false, bool $pagination = true)
    {
        return self::userInitQuery()->whereHas('parents', function ($query) use ($authUserId) {
            return $query->where('id', $authUserId);
        })->whereRelation('roles', 'name', $role)->with(['roles', 'insertedBy']);
    } //all user that this user made

    public static function userInitQuery()
    {
        return User::query();
    }

    public static function userWithMajor($user, $value)
    {
        return $user->where('major_id', $value);
    }

    public static function userPaginate($user, int $value = 25)
    {
        return $user->paginate($value)->appends($_GET);
    }

    public static function userGet($user)
    {
        return $user->get();
    }

    /**
     * called by methods:users,userlevel in BonyadService.php
     * @param $user
     * @param $value
     * @return mixed
     */
    public static function filterFirstName($user, $value)
    {
        return $user->where('firstName', 'LIKE', "%$value%");
    }

    /**
     * called by methods:users,userlevel in BonyadService.php
     * @param $user
     * @param $value
     * @return mixed
     */
    public static function filterLastName($user, $value)
    {
        return $user->where('lastName', 'LIKE', "%$value%");
    }

    /**
     * called by methods:users,userlevel in BonyadService.php
     * @param $user
     * @param $value
     * @return mixed
     */
    public static function filterMobile($user, $value)
    {
        return $user->where('mobile', $value);
    }

    /**
     * called by methods:users,userlevel in BonyadService.php
     * @param $user
     * @param $value
     * @return mixed
     */
    public static function filterNationalCode($user, $value)
    {
        return $user->where('nationalCode', $value);
    }

    public static function parentUsers($id, array $parents = ['betweenId' => []])
    {
        $user = User::find($id);
        if ($user->id == auth('api')->user()->id) {
            return $parents;
        }
        $id = $user->inserted_by;
        $user->load('consultant');
        $parents['betweenId'][$user->id] = [
            'student_register_limit' => $user->consultant->student_register_limit,
            'student_register_number' => $user->consultant->student_register_number
        ];
        return self::parentUsers($id, $parents);
    }

    public static function find(string $mobile, string $nationalCode): Builder
    {
        return User::where('mobile', $mobile)->where('nationalCode', $nationalCode);
    }

    public static function parentUsersForSync($id, $parents = [])
    {
        $user = User::find($id);
        $parents[] = $user->id;
        if (is_null($user->inserted_by)) {
            return $parents;
        }
        return self::parentUsersForSync($user->inserted_by, $parents);

    }

    public static function updateConsultant($id, array $inputs = [])
    {
        return BonyadEhsanConsultant::where('user_id', $id)->update($inputs);
    }

    /**
     * @param $user
     * @param $userParams
     * @return mixed
     */
    public static function update($user, $userParams)
    {
        $user->update($userParams);
        return $user;
    }

    public function checkUserPurchasedProduct($productId)
    {
        return Product::find($productId)->is_purchased;
    }
}
