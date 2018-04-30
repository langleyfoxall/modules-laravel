<?php

namespace LangleyFoxall\Modules\Helpers;

use Illuminate\Support\Str;

class Directory
{
	/**
	 * @param string $path
	 * @param bool $keep
	 */
	public static function create(string $path, bool $keep = false)
	{
		@mkdir($path, 0775, true);
		!$keep || @touch($path . DIRECTORY_SEPARATOR . '.gitkeep');
	}

	/**
	 * @param string[] $paths
	 * @param bool $keep
	 */
	public static function createMultiple(array $paths, bool $keep = false)
	{
		foreach ($paths as $path) {
			if (is_string($path)) {
				@mkdir($path, 0775, true);
				!$keep || @touch($path . DIRECTORY_SEPARATOR . '.gitkeep');
			}
		}
	}

	/**
	 * @param string $base
	 * @param string[] $paths
	 * @param bool $keep
	 */
	public static function createMultipleFromBase(string $base, array $paths, bool $keep = false)
	{
		@mkdir($base, 0775, true);
		!$keep || @touch($base . '/.gitkeep');

		foreach ($paths as $path) {
			if (is_string($path)) {
				$tmp = $base . (Str::startsWith($path, '/') ? $path : DIRECTORY_SEPARATOR . $path);

				@mkdir($tmp, 0775, true);
				!$keep || @touch($tmp . DIRECTORY_SEPARATOR . '.gitkeep');
			}
		}
	}
}