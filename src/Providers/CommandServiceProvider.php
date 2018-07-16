<?php

namespace LangleyFoxall\Modules\Providers;

use Illuminate\Support\ServiceProvider;

use LangleyFoxall\Modules\Commands\DeleteModule;
use LangleyFoxall\Modules\Commands\DeleteWidget;
use LangleyFoxall\Modules\Commands\MakeModule;
use LangleyFoxall\Modules\Commands\MakeModuleConfig;
use LangleyFoxall\Modules\Commands\MakeWidget;

class CommandServiceProvider extends ServiceProvider
{
	/** @var bool $defer */
	protected $defer = false;

	/** @var string[] $commands */
	protected $commands = [
		MakeModuleConfig::class,

		MakeModule::class,
		DeleteModule::class,

		MakeWidget::class,
		DeleteWidget::class
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