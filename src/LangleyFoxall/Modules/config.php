<?php
return [

	'paths' => [
		'modules'    => app_path('Modules'),
		'template'   => base_path('LangleyFoxall/Modules/Template'),
		'assets'     => public_path('modules'),
		'migrations' => base_path('database/migrations'),
		'base_path'  => base_path('LangleyFoxall/Modules'),
	],

	'module' => [
		'consts' => [
			'sanitize' => [ '_', '-', ' ' ],
			'ignore'   => [ 'Views', 'Modules' ],
			'gitkeep' => true
		],
	],

	'register' => [
		'files' => 'register',
	],

	'modules' => [],

];