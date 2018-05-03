<?php

namespace LangleyFoxall\Modules;

use Illuminate\Support\Str;

use LangleyFoxall\Modules\Facade\Module as Service;
use LangleyFoxall\Modules\Template\Config;
use LangleyFoxall\Modules\Exceptions\MissingConfigException;

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

	/**
	 * @return array
	 */
	public static function getAuthenticatableModels()
	{
		/** @var Module[] $modules */
		$modules = Service::getModules();
		$models  = [];

		foreach ($modules as $module) {
			if ($module->hasConfig()) {
				try {

					/** @var Config $config */
					$config  = $module->config();
					$classes = $config->authenticatable() ?? [];

					foreach ($classes as $class) {
						if (class_exists($class)) {
							$models[] = $class;
						}
					}
				} catch (MissingConfigException $e) {
					\Log::warning('Failed to load config', [ 'Failed to load configuration file for ' . $module->getReference() . ' Module' ]);
				}
			}
		}

		return $models;
	}

	/**
	 * @param string $field
	 * @param string $_table
	 * @param mixed  $ignore
	 * @return array
	 */
	public static function getAuthenticationUniques(string $field, string $_table = null, $ignore = null)
	{
		$models = self::getAuthenticatableModels();
		$rules  = [];

		foreach ($models as $model) {
			$table = with(new $model)->getTable();
			$rule  = 'unique:' . $table . ',' . $field;

			if ($table === $_table) {
				$rule .= ',' . $ignore;
			}

			$rules[] = $rule;
		}

		return $rules;
	}

	/**
	 * @param mixed $expression
	 * @param bool  $return
	 * @param int   $indent
	 * @param bool  $indent_last
	 * @return mixed
	 */
	public static function varExport($expression, $return = false, $indent = 4, $indent_last = false)
	{
		$object = json_decode(str_replace([ '(', ')' ], [ '(', ')' ], json_encode($expression)), true);
		$export = str_replace([ 'array (', ')', '(', ')' ], [ '[', ']', '(', ')' ], var_export($object, true));
		$export = preg_replace("/ => \n[^\S\n]*\[/m", ' => [', $export);
		$export = preg_replace("/ => \[\n[^\S\n]*\]/m", ' => []', $export);
		$spaces = str_repeat(' ', $indent);
		$export = preg_replace("/([ ]{2})(?![^ ])/m", $spaces, $export);
		$export = preg_replace("/^([ ]{2})/m", $spaces, $export);

		if ($indent_last) {
			$export = preg_replace('/\]$/', str_repeat(' ', $indent / 2) . ']', $export);
		}

		if ((bool)$return) {
			return $export;
		} else {
			echo $export;
			return null;
		}
	}
}