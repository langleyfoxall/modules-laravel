<?php

namespace LangleyFoxall\Modules\Template;

use Illuminate\Support\Str;

class Config
{
	protected $config = [];

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