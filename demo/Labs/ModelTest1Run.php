<?php
ini_set('display_errors', 1);error_reporting(E_ALL);
require('bootstrap.php');

class ModelMinorA extends \ModelA {
	protected static $_idFieldName;
	protected static $_storeTable = '';
	protected static $_fields = array (
		'login',
	);
}
class ModelMajorA extends \ModelA {
	protected static $_idFieldName;
	protected static $_storeTable = '';
	protected static $_fields = array (
		'login', 'login00', 'login01','login02','login03','login04',
		'login05','login06','login07','login08','login09',
		'login10','login11','login12','login13','login14',
		'login15','login16','login17','login18','login19',
		'login20','login21','login22','login23','login24',
		'login25','login26','login27','login28','login29',
	);
}

class ModelMinorB extends \ModelB {
	protected static $_idFieldName;
	protected static $_storeTable = '';
	protected static $_fields = array (
		'login',
	);
}
class ModelMajorB extends \ModelB {
	protected static $_idFieldName;
	protected static $_storeTable = '';
	protected static $_fields = array (
		'login', 'login00', 'login01','login02','login03','login04',
		'login05','login06','login07','login08','login09',
		'login10','login11','login12','login13','login14',
		'login15','login16','login17','login18','login19',
		'login20','login21','login22','login23','login24',
		'login25','login26','login27','login28','login29',
	);
}

class ModelMinorC extends \ModelC {
	protected static $_idFieldName;
	protected static $_storeTable = '';
	protected static $_fields = array (
		'login',
	);
}
class ModelMajorC extends \ModelC {
	protected static $_idFieldName;
	protected static $_storeTable = '';
	protected static $_fields = array (
		'login', 'login00', 'login01','login02','login03','login04',
		'login05','login06','login07','login08','login09',
		'login10','login11','login12','login13','login14',
		'login15','login16','login17','login18','login19',
		'login20','login21','login22','login23','login24',
		'login25','login26','login27','login28','login29',
	);
}

class Minor {
	protected static $_idFieldName;
	protected static $_storeTable = '';
	protected static $_fields = array (
		'login',
	);
	public static function get() {
		return new static;
	}
}
class Major {
	protected static $_idFieldName;
	protected static $_storeTable = '';
	protected static $_fields = array (
		'login', 'login00', 'login01','login02','login03','login04',
		'login05','login06','login07','login08','login09',
		'login10','login11','login12','login13','login14',
		'login15','login16','login17','login18','login19',
		'login20','login21','login22','login23','login24',
		'login25','login26','login27','login28','login29',
	);
	public static function get() {
		return new static;
	}
}

$m;
function usages() {
	global $m;
	$m[] = array(
		'usage' => memory_get_usage(),
		'usageTrue' => memory_get_usage(true),
		'peak' => memory_get_peak_usage(),
		'peakTrue' => memory_get_peak_usage(true),
	);
};
$models;
function models($className, $cnt) {
	global $models;
	for ($i=0; $i<$cnt; $i++) {
		$Model = $className::get();
#		$Model->login = 'asd';
		$models[] = $Model;
	}
}

$models = array();
#echop($argv); die;
usages();
models($argv[1], $argv[2]);
usages();

$d = array();
for ($i=1; $i<count($m); $i++) {
	$d[] = array(
		'usage' => $m[$i]['usage']- $m[$i-1]['usage'],
#		'usageTrue' => $m[$i]['usageTrue']- $m[$i-1]['usageTrue'],
		'peak' => $m[$i]['peak']- $m[$i-1]['peak'],
#		'peakTrue' => $m[$i]['peakTrue']- $m[$i-1]['peakTrue'],
	);
}

#echop($m);
#echop($d);
var_export($d);

die("\n" . 'OK' . "\n\n");
