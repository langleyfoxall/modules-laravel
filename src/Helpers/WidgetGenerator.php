<?php

namespace LangleyFoxall\Modules\Helpers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Console\OutputStyle;

use LangleyFoxall\Modules\Helper;
use LangleyFoxall\Modules\Exceptions\MissingParentModuleException;
use LangleyFoxall\Modules\Exceptions\MissingModuleException;

class WidgetGenerator
{
	/** @var Application $app */
	protected $app;

	/** @var OutputStyle $console */
	protected $console;

	/** @var bool $keep */
	protected $keep;

	/** @var string $name */
	protected $name;

	/** @var string $reference */
	protected $reference;

	/** @var array $config */
	protected $config;

	/** @var string[] $paths */
	protected $paths = [];

	/** @var string[] $directories */
	protected $directories = [];

	/** @var string $base_path */
	protected $base_path;

	/**
	 * ModuleGenerator constructor.
	 *
	 * @param Application $app
	 * @param OutputStyle $console
	 * @param string      $name
	 */
	public function __construct(Application $app, OutputStyle $console, string $name)
	{
		$config = $app[ 'config' ][ 'modules' ];

		$this->app     = $app;
		$this->console = $console;

		$this->name      = $name;
		$this->reference = Helper::getModuleReference($this->name, '.', false);

		$this->paths     = $config[ 'paths' ];
		$this->base_path = $this->paths[ 'modules' ] . DIRECTORY_SEPARATOR . $this->reference;

		$console->title("Generating {$this->reference} Widget");
	}

	/**
	 * @return WidgetTemplate
	 */
	public function generate()
	{
		$template = new WidgetTemplate($this->app, $this->console, $this->name);

		return $template->generate();
	}

	/**
	 * @throws MissingModuleException
	 * @throws MissingParentModuleException
	 * @return boolean
	 */
	public function check()
	{
		if(!str_contains($this->name, '.')) {
			throw new MissingParentModuleException;
		}

		return $this->app[ 'modules' ]->hasWidget($this->reference);
	}
}