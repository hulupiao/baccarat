<?php
return array(
	'app_name' => array(
		'index', //the first app will be default run.
		'app'
	),
	'rule' => array(
		'dir/a/(\w+)/(\w+)/(\d+)' => array('namespace' => 'dir1', 'parameter' => array('id' => '3')),
	)
);
