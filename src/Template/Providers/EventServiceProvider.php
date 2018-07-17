<?php
namespace LangleyFoxall\Modules\Template\Providers;

use App\Events\AdminSidebarLinksRequest;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use LangleyFoxall\Modules\Events\ModulePing;
use LangleyFoxall\Modules\Template\Listeners\ModulePingListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ModulePing::class => [
            ModulePingListener::class,
        ],
    ];
}