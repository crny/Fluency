<?php
/**
 * LangServiceProvider.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:51]
 */
namespace Clear\Services\Lang;

use \Clear\Facades\View;
use \Clear\Services\ServiceProvider;

class LangServiceProvider extends ServiceProvider
{
    /**
     * 注册语言服务
     *
     * @return void
     */
    public function register()
    {
        $this->container->setShared('lang', function(){
            return new Translator($this->container, APP_PATH . '/i18n/');
        });
    }

    /**
     * 注册模板变量
     *
     * @return void
     */
    public function boot()
    {
        $translator = $this->container->get('lang');

        View::setVar('lang', $translator);
    }
}
