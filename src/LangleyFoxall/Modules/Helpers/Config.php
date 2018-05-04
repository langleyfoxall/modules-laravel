<?php

namespace LangleyFoxall\Modules\Helpers;

use Illuminate\Support\Str;

abstract class Config
{
	protected $config = [];

	/**
	 * @return string[]
	 */
	abstract public function dependencies();

	/**
	 * @return string[]
	 */
	abstract public function authenticatable();

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
		if (method_exists($this, $method)) {
			return $this->{$method}(...$args);
		}

		$key = Str::snake($method);

		if (array_key_exists($key, $this->config)) {
			return $this->config[ $key ];
		}

		return null;
	}
}