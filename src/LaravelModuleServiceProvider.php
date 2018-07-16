<?php

namespace LangleyFoxall\Modules;

use Illuminate\Support\ServiceProvider;

use LangleyFoxall\Modules\Providers\BootServiceProvider;
use LangleyFoxall\Modules\Providers\CommandServiceProvider;

class LaravelModuleServiceProvider extends ServiceProvider
{
	/** @var bool $defer */
	protected $defer = false;

	public function register()
	{
		$this->registerServices();
		$this->registerProviders();
	}

	public function boot()
	{
		$this->registerNamespaces();
		$this->app->register(BootServiceProvider::class);
	}

	public function registerServices()
	{
		$this->app->singleton('modules', function ($app) {
			$path   = $app[ 'config' ][ 'modules.paths.modules' ];

			return new Repository($app, $path);
		});
	}

	public function registerNamespaces()
	{
		$configPath = __DIR__ . '/config.php';

		$this->mergeConfigFrom($configPath, 'modules');

		$this->publishes([
			$configPath => config_path('modules.php'),
		], 'config');
	}

	public function registerProviders()
	{
		$this->app->register(CommandServiceProvider::class);
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [ 'modules' ];
	}
}