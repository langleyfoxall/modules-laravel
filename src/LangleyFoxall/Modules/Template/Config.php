<?php

namespace LangleyFoxall\Modules\Template;

use LangleyFoxall\Modules\Interfaces\ConfigInterface;

class Config implements ConfigInterface
{
	/**
	 * @return bool
	 */
	public static function showOnSidenav()
	{
		return false;
	}

	/**
	 * @return string|null
	 */
	public static function sidenavContent()
	{
		return null;
	}

	/**
	 * @return string|null
	 */
	public static function sidenavLink()
	{
		return null;
	}
}