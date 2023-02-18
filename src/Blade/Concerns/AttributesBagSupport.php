<?php

namespace Basics\Blade\Concerns;

use Basics\Illuminate\Collections\Arr;
use Basics\Illuminate\Support\Str;
use Basics\Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentAttributeBag as BaseComponentAttributeBag;

class AttributesBagSupport
{
    /**
     * Wraps a ComponentAttributeBag or array into custom implementation.
     *
     * @param  \Illuminate\View\ComponentAttributeBag|array  $bag
     * @return \Basics\Illuminate\View\ComponentAttributeBag
     */
    public static function wrapBag($bag = [])
    {
        if ($bag instanceof ComponentAttributeBag) {
            return $bag;
        }

        if ($bag instanceof BaseComponentAttributeBag) {
            return new ComponentAttributeBag((array) $bag);
        }

        return new ComponentAttributeBag(Arr::wrap($bag));
    }

    /**
     * Merge multiple arrays or ComponentAttributeBag together.
     *
     * @param  \Basics\Illuminate\View\ComponentAttributeBag|array  $bags
     * @return \Basics\Illuminate\View\ComponentAttributeBag
     */
    public static function mergeBags(...$bags)
    {
        $attributes = new ComponentAttributeBag();

        foreach ($bags as $bag) {
            if ($bag instanceof ComponentAttributeBag) {
                $attributes = $attributes->merge($bag->getAttributes());
            } else {
                $attributes = $attributes->merge((array) $bag);
            }
        }

        return $attributes;
    }

    /**
     * Filter attributes that starts with a prefix
     * and returns the filtered attributes without the prefix.
     *
     * @param  string  $prefix
     * @param  \Basics\Illuminate\View\ComponentAttributeBag|array  $attributes
     * @return \Basics\Illuminate\View\ComponentAttributeBag
     */
    public static function isolateBag($prefix, $attributes = [])
    {
        $newAttributes = [];

        foreach ($attributes as $key => $value) {
            if (!Str::startsWith($key, $prefix)) {
                continue;
            }

            $newKey = Str::replaceFirst($prefix, '', $key);
            $newAttributes[$newKey] = $value;
        }

        return new ComponentAttributeBag($newAttributes);
    }
}
