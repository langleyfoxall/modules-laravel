<?php
namespace LangleyFoxall\Modules\Template\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
	/** @var string $namespace */
	protected $namespace = 'LangleyFoxall\Modules\Http\Controllers';

	public function boot()
	{
		//

		parent::boot();
	}

	public function map()
	{
		$this->mapWebRoutes();

		$this->mapApiRoutes();

		//
	}

	protected function mapWebRoutes()
	{
		Route::middleware('web')
			 ->namespace($this->namespace)
			 ->group(__DIR__ . '/../Routes/web.php');
	}

	protected function mapApiRoutes()
	{
		Route::prefix('api')
			 ->middleware('api')
			 ->namespace($this->namespace)
			 ->group(__DIR__ . '/../Routes/api.php');
	}
}
