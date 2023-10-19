<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Enable implements Rule
{
    /**
     * @var string
     */

    private $modelClassName;
    /**
     * @var string
     */
    private $message;

    /**
     * Create a new rule instance.
     *
     * @param  string  $modelClassName
     */
    public function __construct(string $modelClassName)
    {
        $this->modelClassName = $modelClassName;
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
        $modelObj = $this->modelClassName::query()->Find($value);
        if (!isset($modelObj)) {
            $this->message = 'validation.exists';
            return false;
        }

        if (!$modelObj->isEnable()) {
            $this->message = 'validation.enable';
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
        return trans($this->message);
    }
}
