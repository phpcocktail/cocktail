<?php

$params = array(
//	array('ModelMinorA', 1),
//	array('ModelMinorB', 1),
//	array('Minor', 1),
//	array('ModelMajorA', 1),
//	array('ModelMajorB', 1),
//	array('Major', 1),
//	array('ModelMinorA', 1000),
//	array('ModelMinorB', 1000),
//	array('Minor', 1000),
//	array('ModelMajorA', 1000),
//	array('ModelMajorB', 1000),
//	array('Major', 1000),
	array('ModelMinorA', 10000),
	array('ModelMinorB', 10000),
	array('ModelMinorC', 10000),
	array('Minor', 10000),
	array('ModelMajorA', 10000),
	array('ModelMajorB', 10000),
	array('ModelMajorC', 10000),
	array('Major', 10000),
);

foreach ($params as $eachParam) {
	$command = 'php ModelTest1Run.php ' . implode(' ', $eachParam);
	exec($command, $out);
	array_pop($out);
	array_pop($out);
	#echo implode(" ", $out); die;
	$d[] = array_merge(array($command), reset(eval('return ' . implode("\n", $out) . ';')));
	echop($command);
//	echop($eachParam);
	#echop(implode("\n", $out));
	unset($out);
}

echop($d);

die("\nFU\n\n");