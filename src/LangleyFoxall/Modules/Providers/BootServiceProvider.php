<?php

namespace LangleyFoxall\Modules\Providers;

use Illuminate\Support\ServiceProvider;

class BootServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->app[ 'modules' ]->boot();
	}

	public function register()
	{
		$this->app[ 'modules' ]->register();
	}
}