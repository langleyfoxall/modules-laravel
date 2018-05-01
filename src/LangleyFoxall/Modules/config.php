<?php
$BASE_PATH = base_path('vendor/langleyfoxall/modules-laravel/src/LangleyFoxall/Modules');

return [

	'paths' => [
		'modules'   => app_path('Modules'),
		'template'  => $BASE_PATH . DIRECTORY_SEPARATOR . '/Template',
		'base_path' => $BASE_PATH,
	],

	'module' => [
		'consts' => [
			'sanitize' => [ '_', '-', ' ' ],
			'ignore'   => [ 'Views', 'Modules', 'Widgets' ],
			'gitkeep'  => true
		],
	],

	'widget' => [
		'consts' => [
			'sanitize' => [ '_', '-', ' ' ],
			'ignore'   => [ 'Views', 'Modules', 'Widgets' ],
			'skip'     => [ 'Modules', 'Widgets' ],
			'gitkeep'  => true
		],
	],

	'register' => [
		'files' => 'register',
	],

];