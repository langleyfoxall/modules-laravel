<?php

namespace LangleyFoxall\Modules\Helpers;

use LangleyFoxall\Modules\Helper;

class WidgetTemplate extends ModuleTemplate
{
	/**
	 * @return string
	 */
	public function getReference()
	{
		return Helper::getModuleReference($this->name, '.', false);
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return Helper::getModulePath($this->name, $this->parents, false);
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return Helper::getModuleNamespace($this->name, $this->parents, false);
	}

	/**
	 * @return $this
	 */
	public function generate()
	{
		$template_path = config('modules.paths.template');
		$gitkeep       = config('modules.widget.consts.gitkeep', false);

		$skip    = config('modules.widget.consts.skip');
		$pattern = implode('/|', $skip);
		$pattern = '/' . addcslashes($pattern, '/') . '/i';

		$base_path = $this->getPath();
		$namespace = $this->getNamespace();

		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($template_path));

		Directory::create($this->getPath(), $gitkeep);

		foreach ($iterator as $item) {
			$bits = explode($template_path, $item->getPath());
			$path = $base_path . DIRECTORY_SEPARATOR . end($bits);

			if (preg_match($pattern, end($bits))) {
				continue;
			}

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

			$this->updateNamespace($path, $namespace);
		}

		return $this;
	}
}