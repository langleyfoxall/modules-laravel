<?php

namespace LangleyFoxall\Modules\Template;

use LangleyFoxall\Modules\Helpers\Config as BaseConfig;

class Config extends BaseConfig
{
	/**
	 * @var array $config
	 */
	protected $config = [];

	/**
	 * @return string[]
	 */
	public function dependencies()
	{
		return [];
	}

	/**
	 * @return string[]
	 */
	public function authenticatable()
	{
		return [];
	}
	
	/**
	 * @return string[]
	 */
	public function editable()
	{
		return [];
	}
}
