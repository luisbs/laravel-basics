<?php

namespace Basics\Blade\Concerns;

use Illuminate\Support\Arr;

trait CompilesAttributesBag
{
    /**
     * Compile the setOnBag statement into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileSetOnBag($expression)
    {
        return "<?php \$attributes->offsetSet($expression); ?>";
    }

    /**
     * Compile the wrapBag statement into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileWrapBag($expression)
    {
        return "<?php \$attributes = \Basics\Blade\Concerns::wrapBag(\$attributes) ?>";
    }

    /**
     * Compile the mergeIntoBag statement into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileMergeIntoBag($expression)
    {
        if (mb_strpos($expression, '$attributes') !== false) {
            return "<?php \$attributes = \Basics\Blade\Concerns::mergeBags($expression) ?>";
        }

        return "<?php \$attributes = \Basics\Blade\Concerns::mergeBags(\$attributes, $expression) ?>";
    }

    /**
     * Compile the propsFromBag statement into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compilePropsFromBag($expression)
    {
        $expression = '[' . trim($expression, '[]') . ']';

        // optimize the cached file by pre-spliting the array
        $numericKeys = array_filter(
            eval("return {$expression};"),
            'is_numeric',
            ARRAY_FILTER_USE_KEY,
        );
        $stringKeys = array_filter(
            eval("return {$expression};"),
            'is_string',
            ARRAY_FILTER_USE_KEY,
        );

        $defaultValues = static::stringify($stringKeys);
        $keys = static::stringify(array_merge($numericKeys, array_keys($stringKeys)));

        // the think behind this fragment is for a view used as a `<x-component />`
        // where all the attributes goes to `$attributes`
        // and the idea is to take the props out of `$attributes`
        //
        // this situation happens if you have a `Class-Component`
        // if you dont want to define the props on the `__constructor`
        // this fragment can help to instantiate any prop out from `$attributes`
        return implode("\n", [
            // extract numeric values on the array
            "<?php \$__keys = {$keys}; ?>",
            // extract values from the $attributes
            "<?php foreach (\$attributes->only(\$__keys) as \$__key => \$__value) {",
            "    \$\$__key = \$\$__key ?? \$__value;",
            '} ?>',
            // exclude props from the $attributes
            "<?php \$attributes = \$attributes->exceptProps(\$__keys); ?>",
            // instantiate props with default values
            "<?php foreach ({$defaultValues} as \$__key => \$__value) {",
            "    \$\$__key = \$\$__key ?? \$__value;",
            '} ?>',
            // cleans context
            "<?php unset(\$__keys, \$__key, \$__value); ?>",
        ]);
    }

    /**
     * Stringify into PHP valid code.
     *
     * @author https://stackoverflow.com/questions/28798159/php-array-stringify
     * @param  mixed  $data
     * @return string
     */
    protected static function stringify($data)
    {
        switch (gettype($data)) {
            case 'string':
                return '\'' . addcslashes($data, "'\\") . '\'';
            case 'boolean':
                return $data ? 'true' : 'false';
            case 'NULL':
                return 'null';
            case 'array':
                // for numeric arrays
                // generate shorter syntax
                if (!Arr::isAssoc($data)) {
                    $expressions = [];

                    foreach ($data as $value) {
                        $expressions[] = static::stringify($value);
                    }

                    return '[' . implode(', ', $expressions) . ']';
                }

            // on assocciative arrays generates object-like syntax
            case 'object':
                $expressions = [];

                foreach ($data as $key => $value) {
                    $expressions[] = static::stringify($key) . ' => ' . static::stringify($value);
                }

                return gettype($data) === 'object'
                    ? '(object)[' . implode(', ', $expressions) . ']'
                    : '[' . implode(', ', $expressions) . ']';
            default:
                return (string) $data;
        }
    }
}
