<?php

namespace App\Rules;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use stdClass;

class FilesArray implements Rule
{
    protected $nameIsSet;

    protected $fieldsAreString;

    protected $isArray;

    protected $eachItemIsStdClass;

    protected $attribute;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->fieldsAreString = $this->eachItemIsStdClass = $this->isArray = $this->nameIsSet = true;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = json_decode($value);
        $this->attribute = $attribute;

        if (!is_array($value)) {
            $this->isArray = false;
        } else {
            foreach ($value as $item) {
                if (!$this->validate()) {
                    break;
                }

                if ($item instanceof stdClass) {
                    if (!isset($item->name[0])) {
                        $this->nameIsSet = false;
                    }

                    foreach ($item as $k => $v) {
                        if (!is_string($v)) {
                            $this->fieldsAreString = false;
                        }
                    }
                } else {
                    $this->eachItemIsStdClass = false;
                }
            }
        }

        return $this->validate();
    }

    /**
     * @return bool
     */
    private function validate(): bool
    {
        return $this->isArray && $this->nameIsSet && $this->eachItemIsStdClass && $this->fieldsAreString;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $message = '';
        if (!$this->nameIsSet) {
            $message = $this->messageLookupTable()['nameIsSet'];
        }
        if (!$this->fieldsAreString) {
            $message = $this->messageLookupTable()['fieldsAreString'];
        }
        if (!$this->eachItemIsStdClass) {
            $message = $this->messageLookupTable()['eachItemIsStdClass'];
        }
        if (!$this->isArray) {
            $message = $this->messageLookupTable()['isArray'];
        }

        return $message;
    }

    protected function messageLookupTable(): array
    {
        return [
            'nameIsSet' => trans('validation.FileArray.name should be set',
                ['attribute' => $this->getAttributeName()]),
            'fieldsAreString' => trans('validation.FileArray.each field should be string',
                ['attribute' => $this->getAttributeName()]),
            'eachItemIsStdClass' => trans('validation.FileArray.each item in array should be instance of std class',
                ['attribute' => $this->getAttributeName()]),
            'isArray' => trans('validation.FileArray.should be An array',
                ['attribute' => $this->getAttributeName()]),
        ];
    }

    /**
     * @return array|Translator|null|string
     */
    protected function getAttributeName()
    {
        return trans('validation.attributes.'.$this->attribute);
    }
}
