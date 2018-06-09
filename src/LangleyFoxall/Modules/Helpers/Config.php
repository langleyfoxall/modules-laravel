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

		if (array_key_exists($variable, $this->config)) {
			return $this->config[ $variable ];
		}

		return '';
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

		if (array_key_exists($method, $this->config)) {
			return $this->config[ $method ];
		}

		return null;
	}
}