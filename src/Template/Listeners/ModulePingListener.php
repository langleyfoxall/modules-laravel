<?php
namespace LangleyFoxall\Modules\Template\Listeners;

class ModulePingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $parts = explode('\\', get_class($this));
        $this->moduleName = $parts[count($parts)-3];
    }
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return bool
     */
    public function handle()
    {
        return $this->moduleName;
    }
}