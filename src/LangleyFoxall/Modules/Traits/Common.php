<?php

namespace LangleyFoxall\Modules\Traits;

trait Common
{
	/** @var string $path */
	private $path;

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}
}