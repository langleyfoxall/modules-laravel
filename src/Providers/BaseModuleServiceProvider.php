<?php
namespace LangleyFoxall\Modules\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use LangleyFoxall\Modules\Events\ModulePing;

abstract class BaseModuleServiceProvider extends ServiceProvider
{
    /** @var string[] $providers */
    protected $providers = [];

    public function register()
    {
        foreach($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    public function boot()
    {
    	$class = get_class($this);
    	$bits = explode('\\', $class);

    	$path = array_slice($bits, 1, count($bits) - 3);

        $this->loadMigrationsFrom(app_path(implode('/', $path) . '/Database/Migrations'));
    }
}
