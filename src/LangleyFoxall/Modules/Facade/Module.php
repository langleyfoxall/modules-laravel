<?php

namespace LangleyFoxall\Modules\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class Module
 * 
 * @method static Module[] getModules()
 * @method static Module   getModule(string $name)
 *
 * @method static Module[] getSubModules()
 * @method static array    getSubModule(string $name)
 *
 * @method static Widget[] getWidgets()
 * @method static array    getWidget(string $name)
 *
 * @method static Module createModule(string $name)
 * @method static Module create(string $name)
 *
 * @method static bool deleteModule(string $name)
 * @method static bool deleteSubModule(string $name)
 * @method static bool deleteWidget(string $name)
 *
 * @method static bool hasModule(string $name)
 * @method static bool hasSubmodule(string $name)
 * @method static bool hasSubModules(string $name, bool $throw)
 * @method static bool hasWidget(string $name)
 * @method static bool hasWidgets(string $name, bool $throw)
 * 
 * @package LangleyFoxall\Modules\Facade
 */
class Module extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'modules';
	}
}
