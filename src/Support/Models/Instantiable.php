<?php

namespace Basics\Support\Models;

trait Instantiable
{
    /**
     * Generates a new instance of the underlying class.
     */
    public static function new()
    {
        return new static(...func_get_args());
    }

    /**
     * Generates a new instance of the underlying class
     * and attach to it some defined attributes.
     */
    public static function newFrom(array $attributes, array $keys)
    {
        $attributes = (array) $attributes;

        $instance = new static();

        // attach attributes
        foreach ($keys as $key) {
            $instance->{$key} = $attributes[$key];
        }

        return $instance;
    }
}
