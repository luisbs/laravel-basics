<?php

namespace Basics\Illuminate\View;

use Basics\Illuminate\Collections\Arr;
use Illuminate\View\ComponentAttributeBag as BaseImplementation;

class ComponentAttributeBag extends BaseImplementation
{
    /**
     * Determine if a given attribute exists in the attribute array.
     * @version 8+
     * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/View/ComponentAttributeBag.php#L66
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Determine if a given attribute is missing from the attribute array.
     * @version 9+
     * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/View/ComponentAttributeBag.php#L78
     *
     * @param  string  $key
     * @return bool
     */
    public function missing($key)
    {
        return ! $this->has($key, $this->attributes);
    }

    /**
     * Conditionally merge classes into the attribute bag.
     * @version 8+
     * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/View/ComponentAttributeBag.php#L183
     *
     * @param  mixed|array  $classList
     * @return static
     */
    public function class($classList)
    {
        $classList = Arr::wrap($classList);

        return $this->merge(['class' => Arr::toCssClasses($classList)]);
    }

    /**
     * Get all of the raw attributes.
     * @version 8+
     * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/View/ComponentAttributeBag.php#L274
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
