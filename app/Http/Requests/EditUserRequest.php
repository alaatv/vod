<?php

namespace App\Http\Requests;

use App\Models\Afterloginformcontrol;
use App\Models\User;
use App\Traits\CharacterCommon;
use App\Traits\RequestCommon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
{
    use CharacterCommon;
    use RequestCommon;

    public const USER_UPDATE_TYPE_TOTAL = 'total';

    public const USER_UPDATE_TYPE_PROFILE = 'profile';

    public const USER_UPDATE_TYPE_ATLOGIN = 'atLogin';

    public const USER_UPDATE_TYPE_PASSWORD = 'password';

    public const USER_UPDATE_TYPE_PHOTO = 'photo';

    public const PHOTO_RULE = '|image|mimes:jpeg,jpg,png|max:512';

    private $updateType;

    private $userId;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param  Request  $request
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        $authenticatedUser = $request->user();
        $this->userId = $this->getUserIdFromRequestBody($request)->getValue(false);

        $this->updateType = $request->get('updateType', self::USER_UPDATE_TYPE_PROFILE);
        if (!in_array($this->updateType, [
            self::USER_UPDATE_TYPE_TOTAL, self::USER_UPDATE_TYPE_PROFILE, self::USER_UPDATE_TYPE_ATLOGIN,
            self::USER_UPDATE_TYPE_PASSWORD, self::USER_UPDATE_TYPE_PHOTO
        ])) {
            return false;
        }

        if ($this->isHeUpdatingHisOwnProfile($this->userId, $authenticatedUser))//He is updating his own profile
        {
            return true;
        }

        if ($this->hasUserAuthorityForEditAction($authenticatedUser)) {
            return true;
        }

        return false;
    }


    /**
     * @param      $userId
     * @param  User  $authenticatedUser
     *
     * @return bool
     */
    private function isHeUpdatingHisOwnProfile($userId, User $authenticatedUser): bool
    {
        return !$userId || $userId !== $authenticatedUser->id;
    }

    /**
     * @param  User  $authenticatedUser
     *
     * @return bool
     */
    private function hasUserAuthorityForEditAction(User $authenticatedUser): bool
    {
        return $authenticatedUser->isAbleTo(config('constants.EDIT_USER_ACCESS'));
    }

    public function rules()
    {

        switch ($this->updateType) {
            case self::USER_UPDATE_TYPE_TOTAL :
                $rules = [
                    'firstName' => 'required|max:255',
                    'lastName' => 'required|max:255',
                    'mobile' => [
                        'required',
                        'digits:11',
                        'phone:AUTO,IR',
                        Rule::unique('users')
                            ->where(function ($query) {
                                $query->where('nationalCode', $this->request->get('nationalCode'))
                                    ->where('id', '<>', $this->userId)
                                    ->where('deleted_at', null);
                            }),
                    ],
                    'nationalCode' => [
                        'required',
                        'digits:10',
                        'validate:nationalCode',
                        Rule::unique('users')
                            ->where(function ($query) {
                                $query->where('mobile', $this->request->get('mobile'))
                                    ->where('id', '<>', $this->userId)
                                    ->where('deleted_at', null);
                            }),
                    ],
                    'password' => 'sometimes|nullable|min:10',
                    'userstatus_id' => 'exists:userstatuses,id',
                    'photo' => 'sometimes|nullable'.self::PHOTO_RULE,
                    'postalCode' => 'sometimes|nullable|numeric',
                    'email' => 'sometimes|nullable|email',
                    'major_id' => 'sometimes|nullable|exists:majors,id',
                    'gender_id' => 'sometimes|nullable|exists:genders,id',
                    'techCode' => 'sometimes|nullable|alpha_num|max:5|min:5|unique:users,techCode,'.$this->userId.',id',
                    'shahr_id' => 'nullable|integer|min:1|exists:shahr,id',
                ];
                break;
            case self::USER_UPDATE_TYPE_PROFILE :
                $rules = [
                    'postalCode' => 'sometimes|nullable|numeric',
                    'email' => 'sometimes|nullable|email',
                    'photo' => 'sometimes|nullable'.self::PHOTO_RULE,
                    'birthdate' => 'sometimes|nullable|date',
                    'address' => 'sometimes|nullable',
                    'school' => 'sometimes|nullable',
                    'school_id' => 'sometimes|nullable|integer|min:0',
                    'shahr_id' => 'sometimes|nullable|integer|min:0',
                    'major_id' => 'sometimes|nullable|integer|min:0',
                    'educationalBase_id' => 'sometimes|nullable|integer|min:0',
                    'grade_id' => 'sometimes|nullable|integer|min:0',
                    'gender_id' => 'sometimes|nullable|integer|min:0',
                    'bloodtype_id' => 'sometimes|nullable|integer|min:0',

                ];
                break;
            case self::USER_UPDATE_TYPE_PHOTO :
                $rules = [
                    'photo' => 'required'.self::PHOTO_RULE,
                ];
                break;
            case self::USER_UPDATE_TYPE_ATLOGIN :
                $afterLoginFields = $this->getAfterLoginFields();

                $this->refineAfterLoginRequest($afterLoginFields);

                $rules = [];
                foreach ($afterLoginFields as $afterLoginField) {
                    $rule = 'required';
                    if (strcmp($afterLoginField, 'email') == 0) {
                        $rule .= '|email';
                    } else {
                        if (strcmp($afterLoginField, 'photo') == 0) {
                            $rule .= self::PHOTO_RULE;
                        } else {
                            $rule .= '|max:255';
                        }
                    }

                    $rules[$afterLoginField] = $rule;
                }
                break;
            case self::USER_UPDATE_TYPE_PASSWORD :
                $rules = [
                    'password' => 'required|confirmed|min:6',
                    'oldPassword' => 'required',
                ];
                break;
            default:
                $rules = [];
                break;
        }

        return $rules;
    }

    /**
     * @return array
     */
    private function getAfterLoginFields(): array
    {
        $afterLoginFields = Afterloginformcontrol::getFormFields()
            ->pluck('name', 'id')
            ->toArray();

        return $afterLoginFields;
    }

    private function refineAfterLoginRequest(array $baseFields)
    {

        $input = $this->request->all();

        foreach ($input as $key => $value) {
            if (!in_array($key, $baseFields) && $value != self::USER_UPDATE_TYPE_ATLOGIN) {
                Arr::pull($input, $key);
            }
        }

        $this->replace($input);
    }

    public function prepareForValidation()
    {
        $this->replaceNumbers();
        $this->convertRequestToCamelCase();
        parent::prepareForValidation();
    }

    private function replaceNumbers()
    {
        $input = $this->request->all();

        if (isset($input['mobile'])) {
            $input['mobile'] = preg_replace('/\s+/', '', $input['mobile']);
            $input['mobile'] = $this->convertToEnglish($input['mobile']);
        }
        if (isset($input['postalCode'])) {
            $input['postalCode'] = preg_replace('/\s+/', '', $input['postalCode']);
            $input['postalCode'] = $this->convertToEnglish($input['postalCode']);
        }
        if (isset($input['nationalCode'])) {
            $input['nationalCode'] = preg_replace('/\s+/', '', $input['nationalCode']);
            $input['nationalCode'] = $this->convertToEnglish($input['nationalCode']);
        }
        if (isset($input['password'])) {
            $input['password'] = $this->convertToEnglish($input['password']);
        }
        if (isset($input['email'])) {
            $input['email'] = preg_replace('/\s+/', '', $input['email']);
            $input['email'] = $this->convertToEnglish($input['email']);
        }
        $this->replace($input);
    }

    private function convertRequestToCamelCase()
    {
        $input = $this->request->all();

        foreach ($input as $key => $data) {
            if (strpos($key, '_id') !== false || $key == '_method') {
                continue;
            }

            $newKey = camel_case($key);
            Arr::pull($input, $key);
            $input[$newKey] = $data;
        }

        $this->replace($input);
    }
}
