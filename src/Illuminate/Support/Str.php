<?php

namespace Basics\Illuminate\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str as BaseImplementation;

class Str extends BaseImplementation
{
    /**
     * Generate a random, secure password.
     * @version 10+
     * @see https://github.com/laravel/framework/blob/10.x/src/Illuminate/Support/Str.php#L734
     *
     * @param  int  $length
     * @param  bool  $letters
     * @param  bool  $numbers
     * @param  bool  $symbols
     * @param  bool  $spaces
     * @return string
     */
    public static function password($length = 32, $letters = true, $numbers = true, $symbols = true, $spaces = false): string
    {
        return (new Collection)
                ->when($letters, fn ($c) => $c->merge([
                    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
                    'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
                    'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
                    'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
                    'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                ]))
                ->when($numbers, fn ($c) => $c->merge([
                    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                ]))
                ->when($symbols, fn ($c) => $c->merge([
                    '~', '!', '#', '$', '%', '^', '&', '*', '(', ')', '-',
                    '_', '.', ',', '<', '>', '?', '/', '\\', '{', '}', '[',
                    ']', '|', ':', ';',
                ]))
                ->when($spaces, fn ($c) => $c->merge([' ']))
                ->pipe(fn ($c) => Collection::times($length, fn () => $c[random_int(0, $c->count() - 1)]))
                ->implode('');
    }
}
