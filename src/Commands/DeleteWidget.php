<?php

namespace LangleyFoxall\Modules\Commands;

use Illuminate\Console\Command;

use LangleyFoxall\Modules\Helper;
use LangleyFoxall\Modules\Repository;
use LangleyFoxall\Modules\Traits\CommandNamespace;
use LangleyFoxall\Modules\Exceptions\MissingModuleException;

class DeleteWidget extends Command
{
	use CommandNamespace;

	/** @var string $signature */
	protected $signature = 'modules:delete-widget {widget}';

	/** @var string $description */
	protected $description = 'Delete a Langley Foxall Module Widget';

	public function handle()
	{
		try {
			$reference = Helper::getModuleReference($this->argument('widget'));

			/** @var Repository $modules */
			$modules = app('modules');

			if (!$modules->hasWidget($reference)) {
				throw new MissingModuleException;
			}

			if ($modules->hasWidgets($reference)) {
				if (!$this->confirm('This module has widgets, are you sure you want to delete it?')) {
					$this->info('Widget deletion cancelled');

					return;
				}
			}

			$modules->deleteWidget($reference);

			$this->info('Module successfully deleted');
		} catch (MissingModuleException $e) {
			$this->error('Missing module ' . $e->getMessage());
		} catch (\Exception $e) {
			$this->error('Unable to delete module');
			$this->warn($e->getMessage());
		}
	}
}