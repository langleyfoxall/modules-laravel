<?php

namespace LangleyFoxall\Modules;

use Illuminate\Support\Str;

class Helper
{
	/**
	 * @param string $name
	 * @param string $delimiter
	 * @return string
	 */
	public static function getModuleReference(string $name, string $delimiter = '.')
	{
		$reference = '';
		$bits      = explode('.', $name);

		foreach ($bits as $name) {
			$reference .= str_replace(config('modules.module.sanitize', ''), '', Str::title($name));
			$reference .= $delimiter;
		}

		return empty($delimiter) ? $reference : substr($reference, 0, -1);
	}

	/**
	 * @param string $name
	 * @param array  $parents
	 * @return string
	 */
	public static function getModuleNamespace(string $name, array $parents = [])
	{
		$namespace = 'App\\Modules\\';

		if (empty($parents)) {
			$parents = self::getModuleParents($name);
		}

		foreach ($parents as $parent) {
			$namespace .= self::getModuleReference($parent) . '\\Modules\\';
		}

		return $namespace . self::getModuleReference($name);
	}

	/**
	 * @param string $name
	 * @param array  $parents
	 * @return string
	 */
	public static function getModulePath(string $name, array $parents = [])
	{
		$path = config('modules')[ 'paths' ][ 'modules' ] . DIRECTORY_SEPARATOR;

		if (empty($parents)) {
			$parents = self::getModuleParents($name);
		}

		foreach ($parents as $parent) {
			$path .= self::getModuleReference($parent) . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR;
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