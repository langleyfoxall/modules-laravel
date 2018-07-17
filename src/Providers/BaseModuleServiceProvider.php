<?php
namespace LangleyFoxall\Modules\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use LangleyFoxall\Modules\Events\ModulePing;

class BaseModuleServiceProvider extends ServiceProvider
{
    /** @var string[] $providers */
    protected $providers = [];

    public function register()
    {
        foreach($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

}