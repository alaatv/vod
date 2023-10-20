<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepo;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BonyadService
{
    public static function getRoles()
    {
        $roles = [
            config('constants.ROLE_BONYAD_EHSAN_MANAGER') => [
                config('constants.ROLE_BONYAD_EHSAN_NETWORK'),
                config('constants.ROLE_BONYAD_EHSAN_SUB_NETWORK'),
                config('constants.ROLE_BONYAD_EHSAN_MOSHAVER'),
                config('constants.ROLE_BONYAD_EHSAN_USER'),
            ],
            config('constants.ROLE_BONYAD_EHSAN_NETWORK') => [
                config('constants.ROLE_BONYAD_EHSAN_SUB_NETWORK'),
                config('constants.ROLE_BONYAD_EHSAN_MOSHAVER'),
                config('constants.ROLE_BONYAD_EHSAN_USER'),
            ],
            config('constants.ROLE_BONYAD_EHSAN_SUB_NETWORK') => [
                config('constants.ROLE_BONYAD_EHSAN_MOSHAVER'),
                config('constants.ROLE_BONYAD_EHSAN_USER'),
            ],
            config('constants.ROLE_BONYAD_EHSAN_MOSHAVER') => [
                config('constants.ROLE_BONYAD_EHSAN_USER'),
            ],
            config('constants.ROLE_BONYAD_EHSAN_USER') => [],
        ];
        return $roles;
    }

    public static function userLevel(User $user, bool $pagination = true, $search = [])
    {
        $role = [
            0 => config('constants.ROLE_BONYAD_EHSAN_MANAGER'),
            1 => config('constants.ROLE_BONYAD_EHSAN_NETWORK'),
            2 => config('constants.ROLE_BONYAD_EHSAN_SUB_NETWORK'),
            3 => config('constants.ROLE_BONYAD_EHSAN_MOSHAVER'),
            4 => config('constants.ROLE_BONYAD_EHSAN_USER'),
            5 => 'null'
        ];
        $userRole = $user->roles()->pluck('name')->toArray();
        $newRoleId = array_keys(array_intersect($role, $userRole))[0] + 1;
        $users = UserRepo::usersByRole($user->id, $role[$newRoleId], true);
        if (!empty($search)) {
            foreach ($search as $key => $value) {
                $users = UserRepo::$key($users, $value);
            }
        }
        $pagination ? $users = UserRepo::userPaginate($users) : $users = UserRepo::userGet($users);
        return $users;
    } //one level under this user


    public static function users($authUserId, string $value, bool $pagination = true, $search = [])
    {
        $users = match ($value) {
            'show-networks' => UserRepo::usersByRole($authUserId, config('constants.ROLE_BONYAD_EHSAN_NETWORK'), false),
            'show-subnetworks' => UserRepo::usersByRole($authUserId, config('constants.ROLE_BONYAD_EHSAN_SUB_NETWORK'),
                false),
            'show-moshavers' => UserRepo::usersByRole($authUserId, config('constants.ROLE_BONYAD_EHSAN_MOSHAVER'),
                false),
            'show-students' => UserRepo::usersByRole($authUserId, config('constants.ROLE_BONYAD_EHSAN_USER'), false),
            'default' => collect(),
        };
        if (!empty($search)) {
            foreach ($search as $key => $value) {
                $users = UserRepo::$key($users, $value);
            }
        }
        $pagination ? $users = UserRepo::userPaginate($users) : $users = UserRepo::userGet($users);
        return $users;
    }

    public static function updateUserConsultant($parentIds, $registerLimit, $type = 'sum')
    {
        try {
            DB::beginTransaction();

            foreach ($parentIds['betweenId'] as $key => $value) {
                if ($type == 'sum') {
                    $limit = $value['student_register_limit'] + $registerLimit;
                    $number = $value['student_register_number'] + $registerLimit;
                } else {
                    $limit = $value['student_register_limit'] - $registerLimit;
                    $number = $value['student_register_number'] - $registerLimit;
                }
                UserRepo::updateConsultant($key, [
                    'student_register_limit' => $limit,
                    'student_register_number' => $number,
                ]);
            }

            if ($type == 'sum') {
                $requestUserNumber = $parentIds['requestUser']['student_register_number'] + $registerLimit;
                $targetUserLimit = $parentIds['targetUser']['student_register_limit'] + $registerLimit;
            } else {
                $requestUserNumber = $parentIds['requestUser']['student_register_number'] - $registerLimit;
                $targetUserLimit = $parentIds['targetUser']['student_register_limit'] - $registerLimit;
            }
            UserRepo::updateConsultant($parentIds['requestUser']['id'],
                ['student_register_number' => $requestUserNumber]);
            UserRepo::updateConsultant($parentIds['targetUser']['id'], ['student_register_limit' => $targetUserLimit]);
            DB::commit();
            return response()->json([
                'data' => [
                    'message' => 'ظرفیت ثبت نام ویرایش شد.'
                ],
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'data' => [
                    'error' => $exception->getMessage()
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
