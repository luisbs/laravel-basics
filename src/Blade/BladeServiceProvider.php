<?php

namespace Basics\Blade;

use Basics\Blade\Concerns\CompilesAttributesBag;
use Basics\Blade\Concerns\CompilesDump;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    use CompilesAttributesBag, CompilesDump;

    /**
     * List of blade directives to be loaded.
     *
     * @var array
     */
    protected $directives = [
        // 'dumpVars',

        'setOnBag',
        // 'wrapBag',
        // 'mergeIntoBag',
        // 'mergeBagAttribute',
        // 'propsFromBag',
    ];

    /**
     * List of blade components to be loaded.
     *
     * @var array
     */
    protected $components = [
        //
    ];

    /**
     * List of blade aliased-components to be loaded.
     *
     * @var array
     */
    protected $aliasedComponents = [
        //
    ];

    /**
     * Component to use to resolve aliased components.
     *
     * @var string
     */
    protected $aliasComponent = AliasComponent::class;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadDirectives();
        $this->loadComponents();
        $this->loadAliasedComponents();
    }

    /**
     * Load blade directives.
     *
     * @return void
     */
    protected function loadDirectives()
    {
        foreach ($this->directives as $key => $callback) {
            $statement = is_string($key) ? $key : $callback;
            $callback = is_string($key) ? $callback : 'compile' . ucfirst($statement);

            Blade::directive($statement, function ($expression) use ($statement, $callback) {
                if (method_exists($this, $callback)) {
                    return $this->{$callback}($expression);
                }
                throw new \Exception("Statement: '$statement' could'nt be compiled.");
            });
        }
    }

    /**
     * Load Aliased Components.
     *
     * @return void
     */
    protected function loadComponents()
    {
        Blade::components($this->components);
    }

    /**
     * Load Aliased Components.
     *
     * @return void
     */
    protected function loadAliasedComponents()
    {
        foreach ($this->aliasedComponents as $component) {
            Blade::component($component, $this->aliasComponent);
        }
    }
}
