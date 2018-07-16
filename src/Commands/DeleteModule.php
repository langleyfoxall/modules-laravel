<?php

namespace LangleyFoxall\Modules\Commands;

use Illuminate\Console\Command;

use LangleyFoxall\Modules\Helper;
use LangleyFoxall\Modules\Repository;
use LangleyFoxall\Modules\Traits\CommandNamespace;
use LangleyFoxall\Modules\Exceptions\MissingModuleException;

class DeleteModule extends Command
{
	use CommandNamespace;

	/** @var string $signature */
	protected $signature = 'modules:delete {module}';

	/** @var string $description */
	protected $description = 'Delete a Langley Foxall Module';

	public function handle()
	{
		try {
			$reference = Helper::getModuleReference($this->argument('module'));

			/** @var Repository $modules */
			$modules = app('modules');

			if (!$modules->hasSubModule($reference)) {
				throw new MissingModuleException;
			}

			if ($modules->hasSubModules($reference)) {
				if (!$this->confirm('This module has sub modules, are you sure you want to delete it?')) {
					$this->info('Module deletion cancelled');

					return;
				}
			}

			$modules->deleteSubModule($reference);

			$this->info('Module successfully deleted');
		} catch (MissingModuleException $e) {
			$this->error('Missing module ' . $e->getMessage());
		} catch (\Exception $e) {
			$this->error('Unable to delete module');
			$this->warn($e->getMessage());
		}
	}
}