<?php

namespace LangleyFoxall\Modules\Commands;

use Illuminate\Console\Command;
use LangleyFoxall\Modules\Traits\CommandNamespace;

class MakeModuleConfig extends Command
{
	use CommandNamespace;

	/** @var string $signature */
	protected $signature = 'modules:config';

	/** @var string $description */
	protected $description = 'Generate the default configuration file for Langley Foxall Modules';

	public function handle()
	{
		try {
			$config_path = config_path('modules.php');

			if (file_exists($config_path)) {
				if (!$this->confirm('A configuration file already exists, do you want to overwrite it?')) {
					return;
				};

				unlink($config_path);
			}

			$default = file_get_contents(base_path('LangleyFoxall/Modules/Config.php'));

			file_put_contents($config_path, $default, 0775);

			$this->info('Configuration file created');
		} catch (\Exception $e) {
			$this->error('Unable to create configuration file');
			$this->error($e->getMessage());
			$this->error($e->getTraceAsString());
		}
	}
}