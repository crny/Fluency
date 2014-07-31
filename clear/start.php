<?php

define('CLEAR_TIME_START', microtime(true));
define('CLEAR_MEMUSAGE', memory_get_usage(true));


// 检测是否定义APP_PATH
if (!defined('APP_PATH') || !$appPath = stream_resolve_include_path(APP_PATH) ) {
  exit('constant "APP_PATH" not defined or not exists!');  
} 

// 包含助手函数
include __DIR__ . '/helpers.php';

include __DIR__ . '/Slim/Slim/Slim.php';
include __DIR__ . '/Application.php';

$app = new Application($appPath);

// 将app自身放入容器中
$app->app = $app;

include app_path('routes.php');

$app->hook('slim.after', function() use ($app) {
    $timeUsage = round(microtime(true) - CLEAR_TIME_START, 5);
    $memUsage = biteConvert(memory_get_usage(true) - CLEAR_MEMUSAGE);

    function consoleArray($config){
        $table = [];
        foreach ($config as $key => $value) {
            is_scalar($value) || $value = json_encode($value);
            $table[$key] = ['value' => $value];
        }
        $data = json_encode($table);
        echo "console.info('应用配置：');";
        echo "console.table($data);";
    }

    echo "<script>
    console.group('debug:');
    console.log('耗时: {$timeUsage}s');
    console.log('内存: {$memUsage}');
    console.groupEnd();
    ";

    consoleArray($app->container['settings']);

    echo "</script>";
});