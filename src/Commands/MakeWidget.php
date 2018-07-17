<?php

namespace LangleyFoxall\Modules\Commands;

use Illuminate\Console\Command;

use LangleyFoxall\Modules\Traits\CommandNamespace;
use LangleyFoxall\Modules\Helpers\WidgetGenerator;
use LangleyFoxall\Modules\Exceptions\MissingModuleException;
use LangleyFoxall\Modules\Exceptions\MissingParentModuleException;

class MakeWidget extends Command
{
	use CommandNamespace;

	/** @var string $signature */
	protected $signature = 'modules:make-widget {name}';

	/** @var string $description */
	protected $description = 'Create a Langley Foxall Module Widget';

	public function handle()
	{
		try {
			$generator = new WidgetGenerator($this->laravel, $this->output, $this->argument('name'));

			if ($generator->check()) {
				$this->error('Widget already exists');

				return;
			}

			$generator->generate();

			$this->line('');
			$this->info('Widget successfully generated');
		} catch (MissingModuleException $e) {
			$this->error('Missing parent module: ' . $e->getMessage());
		} catch (MissingParentModuleException $e) {
			$this->error('Widgets must have a parent module');
		} catch (\Exception $e) {
			$this->error('Unable to create widget');
			$this->warn($e->getMessage());
		}
	}

}