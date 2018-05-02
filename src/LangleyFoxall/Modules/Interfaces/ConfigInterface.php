<?php

namespace LangleyFoxall\Modules\Interfaces;


interface ConfigInterface
{
	/**
	 * @return bool
	 */
	public static function showOnSidenav();

	/**
	 * @return string|null
	 */
	public static function sidenavContent();

	/**
	 * @return string|null
	 */
	public static function sidenavLink();
}