<?php

namespace App\Traits;

trait CharacterCommon
{
    /** Checks whether a string is empty or not
     *
     * @param $str
     *
     * @return boolean
     */
    public function strIsEmpty($str): bool
    {
        return preg_replace('/\s+/', '', $str) === '';
    }

    /** Converts Persian numbers in a string to English numbers
     *
     * @param string $string
     *
     * @return string
     */
    public function convertToEnglish($string): string
    {
        $newNumbers = range(0, 9);
        // 1. Persian HTML decimal
        $persianDecimal = [
            '&#1776;',
            '&#1777;',
            '&#1778;',
            '&#1779;',
            '&#1780;',
            '&#1781;',
            '&#1782;',
            '&#1783;',
            '&#1784;',
            '&#1785;',
        ];
        // 2. Arabic HTML decimal
        $arabicDecimal = [
            '&#1632;',
            '&#1633;',
            '&#1634;',
            '&#1635;',
            '&#1636;',
            '&#1637;',
            '&#1638;',
            '&#1639;',
            '&#1640;',
            '&#1641;',
        ];
        // 3. Arabic Numeric
        $arabic = [
            '٠',
            '١',
            '٢',
            '٣',
            '٤',
            '٥',
            '٦',
            '٧',
            '٨',
            '٩',
        ];
        // 4. Persian Numeric
        $persian = [
            '۰',
            '۱',
            '۲',
            '۳',
            '۴',
            '۵',
            '۶',
            '۷',
            '۸',
            '۹',
        ];

        $string = str_replace($persianDecimal, $newNumbers, $string);
        $string = str_replace($arabicDecimal, $newNumbers, $string);
        $string = str_replace($arabic, $newNumbers, $string);

        return str_replace($persian, $newNumbers, $string);
    }

    /** Converts a string to slug form
     *
     * @param string $string
     * @param string $separator
     *
     * @return string
     */
    public function make_slug($string, $separator = '-'): string
    {
        $string = trim($string);
        $string = mb_strtolower($string, 'UTF-8');

        // Make alphanumeric (removes all other characters)
        // this makes the string safe especially when used as a part of a URL
        // this keeps latin characters and Persian characters as well
        $string = preg_replace('/[^a\-z 0\-9_\sءاآؤئبپتثجچحخدذرزژسشصضطظعغفقكکگلمنوهی]/u', '', $string);

        // Remove multiple dashes or whitespaces or underscores
        $string = preg_replace('/[\s\-_]+/', ' ', $string);

        // Convert whitespaces and underscore to the given separator
        $string = preg_replace('/[\s_]/', $separator, $string);

        return $string;
    }
}
