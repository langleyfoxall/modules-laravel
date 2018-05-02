<?php

namespace LangleyFoxall\Modules;

use Illuminate\Container\Container;

use LangleyFoxall\Modules\Traits\Common;
use LangleyFoxall\Modules\Exceptions\MissingModuleException;
use LangleyFoxall\Modules\Exceptions\MissingSubModuleException;
use LangleyFoxall\Modules\Exceptions\ModuleHasSubModulesException;
use LangleyFoxall\Modules\Exceptions\MissingWidgetException;
use LangleyFoxall\Modules\Exceptions\ModuleHasWidgetsException;

class Repository
{
	use Common;

	/** @var $app */
	protected $app;

	/** @var Module[] $modules */
	private $modules = [];

	/**
	 * Module constructor.
	 *
	 * @param        $app
	 * @param string $path
	 */
	public function __construct(Container $app, string $path = null)
	{
		$this->app  = $app;
		$this->path = $path;
	}

	public function register()
	{
		$this->scan();

		/** @var Module $module */
		foreach ($this->modules as $module) {
			$module->register();

			/** @var Module $sub_module */
			foreach ($module->getSubModules() as $sub_module) {
				$sub_module->register();
			}

			/** @var Widget $widget */
			foreach ($module->getWidgets() as $widget) {
				$widget->register();
			}
		}
	}

	public function boot()
	{
		/** @var Module $module */
		foreach ($this->modules as $module) {
			$module->boot();

			/** @var Module $sub_module */
			foreach ($module->getSubModules() as $sub_module) {
				$sub_module->boot();
			}

			/** @var Widget $widget */
			foreach ($module->getWidgets() as $widget) {
				$widget->boot();
			}
		}
	}

	/**
	 * @param string $name
	 * @return Module
	 */
	public function createModule(string $name)
	{
		$module = new Module($this->app, $name);
		$module->scan();

		$this->modules[ $module->getReference() ] = &$module;

		return $module;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function create(string $name)
	{
		return $this->createModule($name);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasModule(string $name)
	{
		$bits = explode('.', $name);
		$name = array_shift($bits);

		$scope = $this->modules;

		return array_key_exists($name, $scope);
	}

	/**
	 * @param string $name
	 * @throws MissingModuleException
	 * @return bool
	 */
	public function hasSubModule(string $name)
	{
		$bits = explode('.', $name);
		$name = array_pop($bits);

		$scope = $this->modules;

		foreach ($bits as $bit) {
			if (!array_key_exists($bit, $scope)) {
				throw new MissingModuleException($bit);
			}

			$scope = $scope[ $bit ]->getSubModules();
		}

		return array_key_exists($name, $scope);
	}

	/**
	 * @param string $name
	 * @throws MissingModuleException
	 * @return bool
	 */
	public function hasWidget(string $name)
	{
		$bits   = explode('.', $name);
		$length = count($bits);

		$name = array_pop($bits);

		$scope = $this->modules;

		foreach ($bits as $key => $bit) {
			if (!array_key_exists($bit, $scope)) {
				throw new MissingModuleException($bit);
			}

			if ($key != ($length - 1)) {
				$scope = $scope[ $bit ]->getSubModules();
			} else {
				$scope = $scope[ $bit ]->getWidgets();
			}
		}

		return array_key_exists($name, $scope);
	}

	/**
	 * @param string $name
	 * @param bool   $throw
	 * @throws MissingModuleException
	 * @throws ModuleHasSubModulesException
	 * @throws MissingSubModuleException
	 * @return bool
	 */
	public function hasSubModules(string $name, bool $throw = false)
	{
		/** @var Module $module */
		list($module, $parent) = $this->getSubModule($name);

		if (!empty($module->getSubModules())) {
			if ($throw) {
				throw new ModuleHasSubModulesException;
			} else {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $name
	 * @param bool   $throw
	 * @throws MissingModuleException
	 * @throws ModuleHasWidgetsException
	 * @throws MissingWidgetException
	 * @return bool
	 */
	public function hasWidgets(string $name, bool $throw = false)
	{
		/** @var Module $module */
		list($module, $parent) = $this->getWidget($name);

		if (!empty($module->getWidgets())) {
			if ($throw) {
				throw new ModuleHasWidgetsException;
			} else {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $name
	 * @throws MissingModuleException
	 * @throws MissingSubModuleException
	 * @return array
	 */
	public function getSubModule(string $name)
	{
		$bits = explode('.', $name);
		$name = array_pop($bits);

		$parent = null;
		$scope  = $this->modules;

		foreach ($bits as $bit) {
			if (!array_key_exists($bit, $scope)) {
				throw new MissingModuleException($bit);
			}

			$scope = ($parent = $scope[ $bit ])->getSubModules();
		}

		if (!array_key_exists($name, $scope)) {
			throw new MissingSubModuleException($name);
		}

		return [ $scope[ $name ], $parent ];
	}

	/**
	 * @param string $name
	 * @throws MissingModuleException
	 * @throws MissingWidgetException
	 * @return array
	 */
	public function getWidget(string $name)
	{
		$bits   = explode('.', $name);
		$length = count($bits);
		$name   = array_pop($bits);

		$parent = null;
		$scope  = $this->modules;

		foreach ($bits as $key => $bit) {
			if (!array_key_exists($bit, $scope)) {
				throw new MissingModuleException($bit);
			}

			if ($key != ($length - 1)) {
				$scope = $scope[ $bit ]->getSubModules();
			} else {
				$scope = $scope[ $bit ]->getWidgets();
			}
		}

		if (!array_key_exists($name, $scope)) {
			throw new MissingWidgetException($name);
		}

		return [ $scope[ $name ], $parent ];
	}

	/**
	 * @param string $name
	 * @throws MissingModuleException
	 * @throws MissingSubModuleException
	 * @return bool
	 */
	public function deleteSubModule(string $name)
	{
		/**
		 * @var Module      $module
		 * @var Module|null $parent
		 */
		list($module, $parent) = $this->getSubModule($name);

		if (is_null($parent)) {
			$module->delete();

			return true;
		}

		$parent->deleteSubModule($module);

		return true;
	}

	/**
	 * @param string $name
	 * @throws MissingModuleException
	 * @throws MissingWidgetException
	 * @return bool
	 */
	public function deleteWidget(string $name)
	{
		/**
		 * @var Module      $module
		 * @var Module|null $parent
		 */
		list($widget, $parent) = $this->getWidget($name);

		if (is_null($parent)) {
			$widget->delete();

			return true;
		}

		$parent->deleteWidget($widget);

		return true;
	}

	/**
	 * @return $this
	 */
	protected function scan()
	{
		$base_path   = $this->app[ 'config' ][ 'modules' ][ 'paths' ][ 'modules' ];
		$directories = array_filter(glob("{$base_path}/*"), 'is_dir');

		foreach ($directories as $path) {
			$bits      = explode('/', $path);
			$reference = end($bits);
			$module    = new Module($this->app, $reference, $path);

			$this->modules[ $reference ] = $module;
		}

		return $this;
	}
}