<?php

namespace App\Rules;

use App\Models\Exam;
use Illuminate\Contracts\Validation\Rule;

class CheckUserExamWithProduct implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
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
        $exam = Exam::find($value);
        if (!$exam) {
            return true;
        }
        $product = $exam->product;
        foreach (auth()->user()->exams as $exam) {
            if ($exam->product->id == $product->id) {
                return false;
            }
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
        return 'برای این محصول قبلا آزمون انتخاب کرده اید.';
    }
}
