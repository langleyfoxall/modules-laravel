<?php
namespace LangleyFoxall\Modules\Template\Providers;

use LangleyFoxall\Modules\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /** @var string[] $providers */
    protected $providers = [
        RouteServiceProvider::class,
        EventServiceProvider::class,
    ];
}