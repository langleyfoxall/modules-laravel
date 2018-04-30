<?php

namespace LangleyFoxall\Modules;

use Illuminate\Container\Container;

use LangleyFoxall\Modules\Traits\Common;
use LangleyFoxall\Modules\Modules\MissingModuleException;
use LangleyFoxall\Modules\Modules\MissingSubModuleException;
use LangleyFoxall\Modules\Modules\ModuleHasSubModulesException;

class Repository
{
	use Common;

	/** @var $app */
	protected $app;

	/** @var array $modules */
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
		}
	}

	/**
	 * @param string $name
	 * @return Module
	 */
	public function createModule(string $name)
	{
		$module = new Module($this->app, strtolower($name));
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
	 * @throws MissingModuleException
	 * @return bool
	 */
	public function has(string $name)
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
	 * @param bool $throw
	 * @throws MissingModuleException
	 * @throws ModuleHasSubModulesException
	 * @return bool
	 */
	public function hasSubModules(string $name, bool $throw = false)
	{
		/** @var Module $module */
		list($module, $parent) = $this->get($name);

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
	 * @throws MissingModuleException
	 * @return array
	 */
	public function get(string $name)
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
			throw new MissingModuleException($name);
		}

		return [ $scope[ $name ], $parent ];
	}

	/**
	 * @param string $name
	 * @throws MissingModuleException
	 * @throws MissingSubModuleException
	 * @return bool
	 */
	public function delete(string $name)
	{
		/**
		 * @var Module $module
		 * @var Module|null $parent
		 */
		list($module, $parent) = $this->get($name);

		if (is_null($parent)) {
			$module->delete();

			return true;
		}

		$parent->deleteSubModule($module);

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