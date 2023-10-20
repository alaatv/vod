<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GetAtlesatOneQuailyVastRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(public string|null $enable)
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!isset($this->enable)) {
            return true;
        }
        $count = 0;
        foreach ($value as $quality => $name) {
            if (strlen($name) > 0) {
                $count++;
            }
        }
        if ($count == 0) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'حداقل یک کیفیت ویدیو باید انتخاب شود!';
    }
}
