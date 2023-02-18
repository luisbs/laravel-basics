<?php

namespace Basics\Blade;

use Basics\Illuminate\Collections\Arr;
use Illuminate\View\Component;

class AliasComponent extends Component
{
    /**
     * List of component paths.
     *
     * @var array
     */
    protected $pathAliases = [
        //
    ];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view($this->componentPath());
    }

    /**
     * Get the real path of the aliased component.
     *
     * @return string
     */
    protected function componentPath()
    {
        $segments = explode('.', $this->componentName);

        if (array_key_exists($key = Arr::pull($segments, 0), $this->pathAliases)) {
            return $this->pathAliases[$key] . '.' . implode('.', $segments);
        }

        return $this->componentName;
    }
}
