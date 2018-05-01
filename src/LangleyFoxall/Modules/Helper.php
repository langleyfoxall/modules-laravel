<?php

namespace LangleyFoxall\Modules;

use Illuminate\Support\Str;

class Helper
{
	/**
	 * @param string $name
	 * @param string $delimiter
	 * @param bool   $module
	 * @return string
	 */
	public static function getModuleReference(string $name, string $delimiter = '.', bool $module = true)
	{
		$reference = '';
		$bits      = explode('.', $name);

		$replace = config(($module ? 'modules.module.sanitize' : 'modules.widget.sanitize'), '');

		foreach ($bits as $name) {
			$reference .= str_replace($replace, '', Str::ucfirst($name));
			$reference .= $delimiter;
		}

		return empty($delimiter) ? $reference : substr($reference, 0, -1);
	}

	/**
	 * @param string $name
	 * @param array  $parents
	 * @param bool   $module
	 * @return string
	 */
	public static function getModuleNamespace(string $name, array $parents = [], bool $module = true)
	{
		$namespace = 'App\\Modules\\';

		if (empty($parents)) {
			$parents = self::getModuleParents($name);
		}

		foreach ($parents as $parent) {
			$namespace .= self::getModuleReference($parent) . '\\Modules\\';
		}

		if (!$module) {
			$namespace = preg_replace('/\\\Modules\\\$/', '\\Widgets\\', $namespace);
		}

		return $namespace . self::getModuleReference($name);
	}

	/**
	 * @param string $name
	 * @param array  $parents
	 * @param bool   $module
	 * @return string
	 */
	public static function getModulePath(string $name, array $parents = [], bool $module = true)
	{
		$path = config('modules')[ 'paths' ][ 'modules' ] . DIRECTORY_SEPARATOR;

		if (empty($parents)) {
			$parents = self::getModuleParents($name);
		}

		foreach ($parents as $parent) {
			$path .= self::getModuleReference($parent) . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR;
		}

		if (!$module) {
			$path = preg_replace('/\/Modules\/$/', '/Widgets/', $path);
		}

		return $path . self::getModuleReference($name);
	}

	/**
	 * @param string $name
	 * @return array
	 */
	public static function getModuleParents(string $name)
	{
		$bits = explode('.', $name);

		array_pop($bits);

		return $bits ?? [];
	}
}