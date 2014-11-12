<?php

$config['jquery'] = array(
	'default' => array(
		'js' => '/public/js/jquery-1.8.3.min.js',
	),
	'1.10.2' => array(
		'js' => 'http://code.jquery.com/jquery-1.10.2.min.js',
	)
);

$config['ui'] = array(
	'default' => array(
		'js' => 'http://code.jquery.com/ui/1.10.3/jquery-ui.min.js',
		'css' => 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.min.css',
	),
);

$config['jqGrid'] = array(
	'default' => array(
		'js' => array(
			'/public/jquery/jqGrid/grid.locale-ru.js',
			'/public/jquery/jqGrid/jquery.jqGrid.min.js',
		),
		'css' => '/public/jquery/jqGrid/ui.jqgrid.css',
	),
);

$config['bootstrap'] = array(
	'default' => array(
		'js' => '/public/jquery/bootstrap/js/bootstrap.min.js',
		'css' => '/public/jquery/bootstrap/css/bootstrap.min.css',
	),
);
