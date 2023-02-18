<?php

namespace Basics\Illuminate\View;

use Basics\Illuminate\Collections\Arr;
use Illuminate\View\ComponentAttributeBag as BaseImplementation;

class ComponentAttributeBag extends BaseImplementation
{
    /**
     * Determine if a given attribute exists in the attribute array.
     * @version laravel/framework:8+
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
     * @version laravel/framework:9+
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
     * @version laravel/framework:8+
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
     * Merge additional attributes / values into the attribute bag.
     * @version laravel/framework:8+
     * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/View/ComponentAttributeBag.php#L197
     *
     * @param  array  $attributeDefaults
     * @param  bool  $escape
     * @return static
     */
    public function merge(array $attributeDefaults = [], $escape = true)
    {
        $attributeDefaults = array_map(function ($value) use ($escape) {
            return $this->shouldEscapeAttributeValue($escape, $value)
                        ? e($value)
                        : $value;
        }, $attributeDefaults);

        //* simplier implementation
        [$appendableAttributes, $nonAppendableAttributes] = collect($this->attributes)
            ->partition(fn($value, $key) => $key === 'class');

        $attributes = $appendableAttributes
            ->mapWithKeys(function ($value, $key) use ($attributeDefaults, $escape) {
                $defaultsValue = $attributeDefaults[$key] ?? '';
                return [$key => implode(' ', array_unique(array_filter([$defaultsValue, $value])))];
            })->merge($nonAppendableAttributes)->all();

        //? original implementation on laravel 8+
        // [$appendableAttributes, $nonAppendableAttributes] = collect($this->attributes)
        //             ->partition(function ($value, $key) use ($attributeDefaults) {
        //                 return $key === 'class' ||
        //                        (isset($attributeDefaults[$key]) &&
        //                         $attributeDefaults[$key] instanceof AppendableAttributeValue);
        //             });

        // $attributes = $appendableAttributes->mapWithKeys(function ($value, $key) use ($attributeDefaults, $escape) {
        //     $defaultsValue = isset($attributeDefaults[$key]) && $attributeDefaults[$key] instanceof AppendableAttributeValue
        //                 ? $this->resolveAppendableAttributeDefault($attributeDefaults, $key, $escape)
        //                 : ($attributeDefaults[$key] ?? '');

        //     return [$key => implode(' ', array_unique(array_filter([$defaultsValue, $value])))];
        // })->merge($nonAppendableAttributes)->all();

        return new static(array_merge($attributeDefaults, $attributes));
    }

    /**
     * Determine if the specific attribute value should be escaped.
     * @version laravel/framework:8+
     * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/View/ComponentAttributeBag.php#L230
     *
     * @param  bool  $escape
     * @param  mixed  $value
     * @return bool
     */
    protected function shouldEscapeAttributeValue($escape, $value)
    {
        if (! $escape) {
            return false;
        }

        return ! is_object($value) &&
               ! is_null($value) &&
               ! is_bool($value);
    }

    /**
     * Get all of the raw attributes.
     * @version laravel/framework:8+
     * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/View/ComponentAttributeBag.php#L274
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
