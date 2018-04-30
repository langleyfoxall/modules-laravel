<?php

namespace LangleyFoxall\Modules\Facade;

use Illuminate\Support\Facades\Facade;

class Module extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'modules';
	}
}