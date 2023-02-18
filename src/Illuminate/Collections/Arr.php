<?php

namespace Basics\Illuminate\Collections;

use Illuminate\Support\Arr as BaseImplementation;

class Arr extends BaseImplementation
{
    /**
     * Conditionally compile classes from an array into a CSS class list.
     * @version 8+
     * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/Collections/Arr.php#L691
     *
     * @param  array  $array
     * @return string
     */
    public static function toCssClasses($array)
    {
        $classList = static::wrap($array);

        $classes = [];

        foreach ($classList as $class => $constraint) {
            if (is_numeric($class)) {
                $classes[] = $constraint;
            } elseif ($constraint) {
                $classes[] = $class;
            }
        }

        return implode(' ', $classes);
    }
}
