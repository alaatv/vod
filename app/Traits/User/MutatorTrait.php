<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 16:03
 */

namespace App\Traits\User;

use App\Classes\Uploader\Uploader;

trait MutatorTrait
{
    /** Setter mutator for major_id
     *
     * @param $value
     */
    public function setFirstNameAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['firstName'] = null;
        } else {
            $this->attributes['firstName'] = $value;
        }
    }

    /** Setter mutator for major_id
     *
     * @param $value
     */
    public function setLastNameAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['lastName'] = null;
        } else {
            $this->attributes['lastName'] = $value;
        }
    }

    /** Setter mutator for major_id
     *
     * @param $value
     */
    public function setMajorIdAttribute($value): void
    {
        if ($value == 0) {
            $this->attributes['major_id'] = null;
        } else {
            $this->attributes['major_id'] = $value;
        }
    }

    /** Setter mutator for bloodtype_id
     *
     * @param $value
     */
    public function setBloodtypeIdAttribute($value): void
    {
        if ($value == 0) {
            $this->attributes['bloodtype_id'] = null;
        } else {
            $this->attributes['bloodtype_id'] = $value;
        }
    }

    /** Setter mutator for grade_id
     *
     * @param $value
     */
    public function setGenderIdAttribute($value): void
    {
        if ($value == 0) {
            $this->attributes['gender_id'] = null;
        } else {
            $this->attributes['gender_id'] = $value;
        }
    }

    /** Setter mutator for grade_id
     *
     * @param $value
     */
    public function setGradeIdAttribute($value): void
    {
        if ($value == 0) {
            $this->attributes['grade_id'] = null;
        } else {
            $this->attributes['grade_id'] = $value;
        }
    }

    /** Setter mutator for email
     *
     * @param $value
     */
    public function setEmailAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['email'] = null;
        } else {
            $this->attributes['email'] = $value;
        }
    }

    /** Setter mutator for phone
     *
     * @param $value
     */
    public function setPhoneAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['phone'] = null;
        } else {
            $this->attributes['phone'] = $value;
        }
    }

    /** Setter mutator for address
     *
     * @param $value
     */
    public function setAddressAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['address'] = null;
        } else {
            $this->attributes['address'] = $value;
        }
    }

    /** Setter mutator for postalCode
     *
     * @param $value
     */
    public function setPostalCodeAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['postalCode'] = null;
        } else {
            $this->attributes['postalCode'] = $value;
        }
    }

    /** Setter mutator for school
     *
     * @param $value
     */
    public function setSchoolAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['school'] = null;
        } else {
            $this->attributes['school'] = $value;
        }
    }

    /** Setter mutator for allergy
     *
     * @param $value
     */
    public function setAllergyAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['allergy'] = null;
        } else {
            $this->attributes['allergy'] = $value;
        }
    }

    /** Setter mutator for medicalCondition
     *
     * @param $value
     */
    public function setMedicalConditionAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['medicalCondition'] = null;
        } else {
            $this->attributes['medicalCondition'] = $value;
        }
    }

    /** Setter mutator for discount
     *
     * @param $value
     */
    public function setDietAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['diet'] = null;
        } else {
            $this->attributes['diet'] = $value;
        }
    }

    /**
     *
     */
    public function getReverseFullNameAttribute()
    {
        return ucfirst($this->lastName).' '.ucfirst($this->firstName);
    }

    public function getPhotoAttribute($value)
    {
        $defaultResult = null;

        if (empty($value)) {
            return $defaultResult;
        }

        $this->setDisk();
        return Uploader::url($this->disk, $value) ?? $defaultResult;
    }

    public function getCustomSizePhoto(int $width, int $height, string $disk)
    {
        $defaultResult = '/acm/image/255x255.png';

        if (!isset($this->photo) || empty($this->photo)) {
            return $defaultResult;
        }

        $value = $this->getRawOriginal('photo');

        if (empty($value)) {
            return $defaultResult;
        }

        $imageUrl = Uploader::url($disk, $value);

        if (empty($imageUrl)) {
            return $defaultResult;
        }

        return "{$imageUrl}?w={$width}&h={$height}";
    }


    public function getShortNameAttribute()
    {
        if (isset($this->firstName)) {
            return ucfirst($this->firstName);
        }
        if (isset($this->lastName)) {
            return ucfirst($this->lastName);
        }

        return 'کاربر آلایی';
    }

    public function getFullNameAttribute($value)
    {
        if (!isset($this->firstName) && !isset($this->lastName)) {
            return null;
        }

        return ucfirst($this->firstName).' '.ucfirst($this->lastName);
    }
}
