<?php

namespace Basics\Support\Http;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class ExtensibleResource extends JsonResource
{
    /**
     * Defines a resource resolution behavior
     * that uses always the `arrayResolver` method,
     * leaving the end class to handle the request modes.
     */
    protected const CUSTOM = 0;

    /**
     * Defines a resource resolution behavior
     * that uses the `minArrayResolver` method on **minimal** mode,
     * and uses the `arrayResolver` on other modes.
     */
    protected const SPLIT = 1;

    /**
     * Defines a resource resolution behavior
     * that uses the `minArrayResolver` method on **minimal** mode,
     * and on other cases merges the `arrayResolver` method into the `minArrayResolver` method.
     */
    protected const MERGE = 2;

    /**
     * Defines the behavior of the resolver.
     */
    protected int $behavior = self::CUSTOM;

    /**
     * Defines the request param for the mode value.
     */
    protected string $modeParamName = 'mode';

    /**
     * Defines the request param for the minimal mode.
     */
    protected string $minParamName = 'min';

    /**
     * Create new anonymous resource collection.
     */
    public static function collection($resource): AnonymousExtensibleResourceCollection
    {
        return tap(new AnonymousExtensibleResourceCollection($resource, static::class), function (
            $collection
        ) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }

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
     * Resolve the resource to an array.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return array
     */
    public function toArray($request, string $mode = null)
    {
        if (is_null($this->resource)) {
            return [];
        }

        $mode =
            $mode ?? //
            ($request->input($this->modeParamName) ??
                ($request->boolean($this->minParamName) ? $this->minParamName : null));

        // resolve minimal mode
        if ($mode === $this->minParamName) {
            return $this->behavior === static::CUSTOM
                ? $this->arrayResolver($mode, $request, $this->resource)
                : $this->minArrayResolver($request, $this->resource);
        }

        // resolve other modes
        if ($this->behavior === static::MERGE) {
            return array_merge(
                $this->minArrayResolver($request, $this->resource),
                $this->arrayResolver($mode, $request, $this->resource),
            );
        }

        // fallback
        return $this->arrayResolver($mode, $request, $this->resource);
    }

    /**
     * Resolve the resource to an array on a minimal mode.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @param  \Illuminate\Database\Eloquent\Model  $resource
     * @return array
     */
    protected function minArrayResolver($request, $resource)
    {
        return [];
    }

    /**
     * Resolve the resource to an array on special modes.
     *
     * @param  string|null  $mode
     * @param  \Illuminate\Http\Request|null  $request
     * @param  \Illuminate\Database\Eloquent\Model  $resource
     * @return array
     */
    abstract protected function arrayResolver($mode, $request, $resource);
}
