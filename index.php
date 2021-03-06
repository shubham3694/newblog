<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

if(isset($_GET['show_logs'])) {
	defined('YII_DEBUG_SHOW_PROFILER') or define('YII_DEBUG_SHOW_PROFILER',true);
	defined('YII_DEBUG_PROFILING') or define('YII_DEBUG_PROFILING',true);
} else {
	defined('YII_DEBUG_SHOW_PROFILER') or define('YII_DEBUG_SHOW_PROFILER',false);
	defined('YII_DEBUG_PROFILING') or define('YII_DEBUG_PROFILING',false);
}

require_once($yii);
Yii::createWebApplication($config)->run();

function pick_server($connected, $query, $masters, $slaves, $last_used_connection, $in_transaction) {
	$ret = $masters[0];
	$enabled = true;
	$query_condition = (strpos($query, "USE_SLAVE") !== false || (Yii::app()->params["use_slave"] && (0 === stripos($query, 'SELECT '))));
	if($enabled && $query_condition) {
		$ret = $slaves[0];
	}
	return $ret;
}