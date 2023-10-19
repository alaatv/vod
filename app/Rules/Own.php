<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Own implements Rule
{
    /**
     * @var int
     */
    private $ownerId;
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $owneeIndex;
    /**
     * @var string
     */
    private $model;

    /**
     * Own constructor.
     *
     * @param  int  $ownerId
     * @param  string  $modelClassName
     * @param  string  $owneeIndex
     */
    public function __construct(int $ownerId, string $modelClassName, string $owneeIndex)
    {
        $this->ownerId = $ownerId;
        $this->model = $modelClassName;
        $this->owneeIndex = $owneeIndex;
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
        $modelObj = $this->model::query()->Find($value);
        if (!isset($modelObj)) {
            $this->message = 'validation.exists';
            return false;
        }

        $owneeIndex = $this->owneeIndex;
        if ($modelObj->$owneeIndex != $this->ownerId) {
            $this->message = 'validation.own';
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
