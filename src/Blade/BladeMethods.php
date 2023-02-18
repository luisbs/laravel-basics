<?php

namespace Basics\Blade;

use Basics\Illuminate\Collections\Arr;

class BladeMethods
{
    /**
     * Prepare a string to be use a `<input />` name attribute.
     * @example - `account.id` => `account[id]`
     *
     * @param  string  $source
     * @return string
     */
    public static function prepareInputName($source)
    {
        if (mb_stripos($source, '.') === false) {
            return $source;
        }

        $segments = explode('.', $source);
        $initial = Arr::pull($segments, 0, '');
        return array_reduce($segments, fn($carry, $item) => $carry . '[' . $item . ']', $initial);
    }
}
