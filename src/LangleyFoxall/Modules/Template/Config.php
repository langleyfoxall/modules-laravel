<?php

namespace LangleyFoxall\Modules\Template;

use Illuminate\Support\Str;

class Config
{
	protected $config = [];

	/**
	 * @param $variable
	 */
	public function __get($variable)
	{
		$key = Str::snake($variable);

		if (array_key_exists($key, $this->config)) {
			echo $this->config[ $key ];
		}

		echo '';
	}

	/**
	 * @param string $method
	 * @param array  $args
	 * @return mixed|null
	 */
	public function __call(string $method, array $args)
	{
		$key = Str::snake($method);

		if (array_key_exists($key, $this->config)) {
			return $this->config[ $key ];
		}

		return null;
	}
}