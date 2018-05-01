<?php

namespace LangleyFoxall\Modules\Commands;

use Illuminate\Console\Command;

use LangleyFoxall\Modules\Traits\CommandNamespace;
use LangleyFoxall\Modules\Helpers\ModuleGenerator;
use LangleyFoxall\Modules\Exceptions\MissingModuleException;

class MakeModule extends Command
{
	use CommandNamespace;

	/** @var string $signature */
	protected $signature = 'modules:make {name}';

	/** @var string $description */
	protected $description = 'Create a Langley Foxall Module';

	public function handle()
	{
		try {
			$generator = new ModuleGenerator($this->laravel, $this->output, $this->argument('name'));

			if ($generator->check()) {
				$this->error('Module already exists');

				return;
			}

			$generator->generate();

			$this->line('');
			$this->info('Module successfully generated');
		} catch (MissingModuleException $e) {
			$this->error('Missing parent module: ' . $e->getMessage());
		} catch (\Exception $e) {
			$this->error('Unable to create module');
			$this->warn($e->getMessage());
		}
	}

}