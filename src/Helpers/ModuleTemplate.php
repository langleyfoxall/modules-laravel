<?php

namespace LangleyFoxall\Modules\Helpers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Console\OutputStyle;

use LangleyFoxall\Modules\Helper;

class ModuleTemplate
{
	/** @var Application $app */
	private $app;

	/** @var OutputStyle $console */
	private $console;

	/** @var string $name */
	protected $name;

	/** @var array $parents */
	protected $parents = [];

	/**
	 * Template constructor.
	 *
	 * @param Application $app
	 * @param OutputStyle $console
	 * @param string      $name
	 */
	public function __construct(Application $app, OutputStyle $console, string $name)
	{
		$this->app     = $app;
		$this->console = $console;

		$bits = explode('.', $name);

		$this->name    = array_pop($bits);
		$this->parents = $bits;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName(string $name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getReference()
	{
		return Helper::getModuleReference($this->name);
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return Helper::getModulePath($this->name, $this->parents);
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return Helper::getModuleNamespace($this->name, $this->parents);
	}

	/**
	 * @return $this
	 */
	public function generate()
	{
		$template_path = config('modules.paths.template');
		$gitkeep       = config('modules.module.consts.gitkeep', false);
		$base_path     = $this->getPath();
		$namespace     = $this->getNamespace();

		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($template_path));

		Directory::create($this->getPath(), $gitkeep);

		foreach ($iterator as $item) {
			$bits = explode($template_path, $item->getPath());
			$path = $base_path . DIRECTORY_SEPARATOR . end($bits);

			/** @var \SplFileInfo $item */
			if ($item->isDir()) {
				if (!file_exists($path)) {
					Directory::create($path, $gitkeep);
				}

				continue;
			}

			if (($filename = $item->getFilename()) === '.gitkeep') {
				continue;
			}

			copy($item->getRealPath(), ($path = $path . DIRECTORY_SEPARATOR . $filename));

			if (str_contains($filename, 'Controller')) {
				$this->updateNamespace($path, $namespace, '\Http\Controllers');

				continue;
			}

			if (str_contains($filename, 'Provider')) {
				$this->updateNamespace($path, $namespace, '\Providers');
				$this->updateVariableNamespace($path, $namespace, '\Http\Controllers');

				continue;
			}
			
			if (str_contains($filename, 'Listener')) {
				$this->updateNamespace($path, $namespace, '\Listeners');
			}

			if (str_contains($filename, 'Config.php')) {
				$this->updateConfig($path);
			}

			$this->updateNamespace($path, $namespace);
		}

		return $this;
	}

	/**
	 * @param string $path
	 * @return $this
	 */
	protected function updateConfig(string $path)
	{
		if (file_exists($path)) {
			$content = file_get_contents($path);
			$content = preg_replace(
				'/\$config\s+=\s+(.*);/',
				'$config = ' . Helper::varExport(config('modules.module.config', []), true, 8, true) . ';',
				$content
			);

			file_put_contents($path, $content);
		}

		return $this;
	}

	/**
	 * @param string $path
	 * @param string $namespace
	 * @param string $suffix
	 * @return $this
	 */
	protected function updateNamespace(string $path, string $namespace = null, string $suffix = null)
	{
		if (file_exists($path)) {
			$content = file_get_contents($path);
			$content = preg_replace(
				'/^namespace\s+(.*);/m',
				'namespace ' . (is_string($namespace) ? $namespace : $this->getNamespace()) . $suffix . ';',
				$content
			);

			file_put_contents($path, $content);
		}

		return $this;
	}

	/**
	 * @param string $path
	 * @param string $namespace
	 * @param string $suffix
	 * @return $this
	 */
	protected function updateVariableNamespace(string $path, string $namespace = null, string $suffix = null)
	{
		if (file_exists($path)) {
			$content = file_get_contents($path);
			$content = preg_replace(
				'/\$namespace\s+\=\s+(.*);/',
				'$namespace = \'' . (is_string($namespace) ? $namespace : $this->getNamespace()) . $suffix . '\';',
				$content
			);

			file_put_contents($path, $content);
		}

		return $this;
	}
}
