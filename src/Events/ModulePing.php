<?php
namespace LangleyFoxall\Modules\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ModulePing
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}