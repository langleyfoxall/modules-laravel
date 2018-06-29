<?php

namespace LangleyFoxall\Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

use LangleyFoxall\Modules\Traits\Common;
use LangleyFoxall\Modules\Template\Config;
use LangleyFoxall\Modules\Exceptions\MissingConfigException;
use LangleyFoxall\Modules\Exceptions\MissingSubModuleException;
use LangleyFoxall\Modules\Exceptions\MissingWidgetException;

use SplFileInfo;
use FilesystemIterator;

class Module extends ServiceProvider
{
	use Common;

	/** @var array $_paths */
	private $_paths;

	/** @var array $_constants */
	private $_constants;

	/** @var string $name */
	protected $name;

	/** @var string[] $files */
	private $files = [];

	/** @var Module[] $sub_modules */
	private $sub_modules = [];

	/** @var Widget[] $widgets */
	private $widgets = [];

	/** @var Config $config */
	private $config;

	/**
	 * Module constructor.
	 *
	 * @param        $app
	 * @param string $name
	 * @param string $path
	 */
	public function __construct($app, string $name, string $path = null)
	{
		parent::__construct($app);

		$config = $app[ 'config' ][ 'modules' ];

		$this->name       = $name;
		$this->_paths     = $config[ 'paths' ];
		$this->_constants = $config[ 'module' ][ 'consts' ];

		$this->setPath($path);
		$this->setSubModules();
		$this->setWidgets();
	}

	public function register()
	{
		if ($this->loadFilesOnRegister()) {
			$this->init();
		}

		$this->fireEvent('register');
	}

	public function boot()
	{
		if ($this->loadFilesOnBoot()) {
			$this->init();
		}

		$this->fireEvent('boot');
	}

	public function init()
	{
		$this->loadFiles();
		$this->registerProviders();

		$this->loadMigrationsFrom($this->getPath() . '/Database/Migrations');
		$this->loadViewsFrom($this->getPath() . '/Views', Helper::getModuleReference($this->name, ''));
	}

	/**
	 * @return bool
	 */
	public function hasConfig()
	{
		foreach ($this->files as $file) {
			$bits     = explode(DIRECTORY_SEPARATOR, $file);
			$filename = end($bits);

			if ($filename === 'Config.php') {
				return true;

				break;
			}
		}

		return false;
	}

	/**
	 * @throws MissingConfigException
	 * @return Config
	 */
	public function config()
	{
		if(!is_string($this->config)) {
			foreach ($this->files as $file) {
				$bits     = explode(DIRECTORY_SEPARATOR, $file);
				$filename = end($bits);

				if ($filename === 'Config.php') {
					preg_match('/\/(app\/.*).php$/i', $file, $matches);
					$class = str_replace([ 'app', '/' ], [ 'App', '\\' ], $matches[ 1 ]);

					if (!class_exists($class, true)) {
						throw new MissingConfigException;
					}

					return $this->config = new $class;

					break;
				}
			}

			return null;
		}

		return $this->config;
	}

	/**
	 * @return bool
	 */
	public function delete()
	{
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->getPath(), FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);

		/** @var SplFileInfo $item */
		foreach ($iterator as $item) {
			if ($item->isDir()) {
				rmdir($item->getPathname());

				continue;
			}

			unlink($item->getPathname());
		}

		rmdir($this->getPath());

		return true;
	}

	/**
	 * @param Module|string $module
	 * @throws MissingSubModuleException
	 * @return bool
	 */
	public function deleteSubModule($module)
	{
		if (is_string($module)) {
			$reference = Helper::getModuleReference($module);

			if (!array_key_exists($reference, $this->getSubModules())) {
				throw new MissingSubModuleException;
			}

			$module = $this->getSubModules()[ $reference ];
		} else {
			/** @var Module $module */
			$reference = $module->getReference();
		}

		$module->delete();

		unset($this->sub_modules[ $reference ]);

		return true;
	}

	/**
	 * @param Widget|string $widget
	 * @throws MissingWidgetException
	 * @return bool
	 */
	public function deleteWidget($widget)
	{
		if (is_string($widget)) {
			$reference = Helper::getModuleReference($widget);

			if (!array_key_exists($reference, $this->getWidgets())) {
				throw new MissingWidgetException;
			}

			$widget = $this->getWidgets()[ $reference ];
		} else {
			/** @var Widget $widget */
			$reference = $widget->getReference();
		}

		$widget->delete();

		unset($this->sub_modules[ $reference ]);

		return true;
	}

	/**
	 * @return string
	 */
	public function getReference()
	{
		return Helper::getModuleReference($this->name);
	}

	/**
	 * @return string[]
	 */
	public function getFiles()
	{
		if (empty($this->files)) {
			$this->scan();
		}

		return $this->files;
	}

	/**
	 * @return Module[]
	 */
	public function getSubModules()
	{
		return $this->sub_modules;
	}

	/**
	 * @return Widget[]
	 */
	public function getWidgets()
	{
		return $this->widgets;
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function setPath(string $path = null)
	{
		return $this->path = is_string($path) ? $path : Helper::getModulePath($this->name);
	}

	/**
	 * @return array
	 */
	protected function setSubModules()
	{
		$this->sub_modules = [];
		$directories       = array_filter(glob("{$this->getPath()}/Modules/*"), 'is_dir');

		/** @var SplFileInfo $item */
		foreach ($directories as $path) {
			$bits   = explode('/', $path);
			$module = end($bits);

			$this->sub_modules[ $module ] = new Module($this->app, $module, $path);
		}

		return $this->sub_modules;
	}

	/**
	 * @return array
	 */
	protected function setWidgets()
	{
		$this->widgets = [];
		$directories   = array_filter(glob("{$this->getPath()}/Widgets/*"), 'is_dir');

		/** @var SplFileInfo $item */
		foreach ($directories as $path) {
			$bits   = explode('/', $path);
			$widget = end($bits);

			$this->widgets[ $widget ] = new Widget($this->app, $widget, $path);
		}

		return $this->widgets;
	}

	/**
	 * @return $this
	 */
	public function scan()
	{
		try {
			$path    = $this->getPath();
			$pattern = array_map(function ($item) use ($path) {
				return $path . DIRECTORY_SEPARATOR . $item;
			}, $this->_constants[ 'ignore' ]);

			$pattern = implode('/|', $pattern);
			$pattern = '/' . addcslashes($pattern, '/') . '/i';

			$inner    = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
			$iterator = new \RecursiveIteratorIterator($inner, \RecursiveIteratorIterator::SELF_FIRST, \RecursiveIteratorIterator::CATCH_GET_CHILD);

			/** @var SplFileInfo $item */
			foreach ($iterator as $item) {
				if ($item->isDir()) {
					continue;
				}

				if (Str::startsWith($item->getFilename(), '.')) {
					continue;
				}

				$this->files[] = $item->getRealPath();
			}

			$this->files = array_filter($this->files, function ($item) use ($pattern) {
				return !preg_match($pattern, $item);
			});
		} catch (\UnexpectedValueException $e) {
			$this->files = [];
		}

		return $this;
	}

	protected function registerProviders()
	{
		$files = glob($this->path . '/Providers/*');

		foreach ($files as $file) {
			preg_match('/\/(app\/.*).php$/i', $file, $matches);

			if (empty($matches)) {
				continue;
			}

			$this->app->register(str_replace([ 'app', '/' ], [ 'App', '\\' ], $matches[ 1 ]));
		}
	}

	/**
	 * @param string $event
	 */
	protected function fireEvent(string $event)
	{
		$this->app[ 'events' ]->fire(sprintf('modules.%s.%s', $this->getReference(), $event), [ $this ]);
	}

	protected function loadFiles()
	{
		foreach ($this->getFiles() as $file) {
			if (strpos($file, '.php') === false) {
				continue;
			}
			
			include_once $this->getPath() . Str::startsWith($file, '/') ? $file : '/' . $file;
		}
	}

	/**
	 * @return bool
	 */
	protected function loadFilesOnBoot()
	{
		return config('modules.register.files', 'register') === 'boot';
	}

	/**
	 * @return bool
	 */
	protected function loadFilesOnRegister()
	{
		return config('modules.register.files', 'boot') === 'register';
	}
}
