<?php

namespace Basics\Support\Http;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnonymousExtensibleResourceCollection extends AnonymousResourceCollection
{
    /**
     * Resolve the resource to an array.
     */
    public function resolveOnMode(string $mode = null): array
    {
        return $this->resolve(null, $mode);
    }

    /**
     * Resolve the resource to an array.
     *
     * @param  \Illuminate\Http\Request|null  $request
     */
    public function resolve($request = null, string $mode = null): array
    {
        $data = $this->toArray(
            $request = $request ?: Container::getInstance()->make('request'),
            $mode,
        );

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        } elseif ($data instanceof \JsonSerializable) {
            $data = $data->jsonSerialize();
        }

        return $this->filter((array) $data);
    }

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request, string $mode = null)
    {
        return $this->collection->map->toArray($request, $mode)->all();
    }
}
