<?php

use \Slim\Slim;
use \Slim\View;

class Application extends Slim 
{   

    /**
     * 初始化环境配置
     *
     * @param string $appPath 应用目录
     */
    public function __construct($appPath)
    {   
        $this->app_path = str_finish($appPath, '/');

        // 获取当前环境名称
        $this->env = $this->detectEnvironment();

        $settings = $this->getUserSettings();

        $this->registerAutoloader();

        parent::__construct($settings);

        $this->view(new View);
    }

    /**
     * 注册自动加载器
     *
     * @return void
     */
    public static function registerAutoloader()
    {
        parent::registerAutoloader();
        spl_autoload_register(__NAMESPACE__ .'\\Application::clearAutoload');
    }

    /**
     * 注册clear自动加载器
     *
     * @return void
     */
    public static function clearAutoload($className)
    {
        $className = substr(ltrim($className, '\\'), strlen('Clear\\'));

        $file = stream_resolve_include_path(
            __DIR__ . '/' . str_replace(['\\', '\\\\'], '/', $className) . '.php');

        if ($file) {
            require $file;
        }
    }

    /**
     * 获取用户配置  
     *
     * @return array
     */
    protected function getUserSettings()
    {
        $settings = include app_path('config') . '/global.php';

        if (stream_resolve_include_path(config_path($this->env . '.php'))) {
            $envSettings = include config_path($this->env . '.php');
            $settings = array_merge($settings, (array) $envSettings);
        }

        return $settings;
    }

    /**
     * 侦测当前应用的运行环境
     *
     * @return string 当前运行的环境
     */
    public function detectEnvironment()
    {
        //环境设置文件是否存在
        if (!file_exists(app_path() . '/environments.php')) {
            return 'production';
        }

        $environments = include app_path() . '/environments.php';
        
        if ($environments instanceof Closure){
            return call_user_func($environments);
        }

        //匹配环境设置
        foreach ($environments as $environment => $hosts) {
            if (false !== array_search('*', $hosts) 
                || false !== array_search(gethostname(), $hosts)) {
                return $environment;
            }
        }

        return 'production';
    }

    public static function errorTemplateMarkup()
    {
        return forward_static_call_array('parent::generateTemplateMarkup', func_get_args());
    }

    public function run()
    {
        parent::run();
    }
}