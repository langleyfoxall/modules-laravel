<?php

namespace LangleyFoxall\Modules;

use Illuminate\Container\Container;
use LangleyFoxall\Modules\Events\ModulePing;
use LangleyFoxall\Modules\Traits\Common;

class Repository
{
	use Common;

	/** @var $app */
	protected $app;

	/**
	 * Module constructor.
	 *
	 * @param        $app
	 * @param string $path
	 */
	public function __construct(Container $app)
	{
		$this->app  = $app;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasModule(string $name)
	{
		return in_array($name, event(new ModulePing()));
	}

	/**
	 * @param string $name
	 * @return Module
	 */
	public function getModule(string $name)
	{
        $module = new Module($this->app, $name);
        $module->scan();

        return $module;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function deleteModule(string $name)
	{
		$module = $this->getModule($name);

		$module->delete();

		return true;
	}

}