<?php

namespace LangleyFoxall\Modules\Providers;

use Illuminate\Support\ServiceProvider;

use LangleyFoxall\Modules\Commands\DeleteModule;
use LangleyFoxall\Modules\Commands\MakeModule;
use LangleyFoxall\Modules\Commands\MakeModuleConfig;

class CommandServiceProvider extends ServiceProvider
{
	/** @var bool $defer */
	protected $defer = false;

	/** @var string[] $commands */
	protected $commands = [
		MakeModuleConfig::class,
		MakeModule::class,
		DeleteModule::class
	];

	public function register()
	{
		$this->commands($this->commands);
	}

	/**
	 * @return string[]
	 */
	public function provides()
	{
		return $this->commands;
	}
}