<?php
/**
 * Translator.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:51]
 */
namespace Engine\Services\Lang;

use MessageFormatter;

class Translator
{
    /**
     * DI容器
     *
     * @var \Phalcon\Di
     */
    protected $app;

    /**
     * 设置语言
     *
     * @var string
     */
    protected $locale;

    /**
     * 语言配置
     *
     * @var array
     */
    protected $patterns;

    /**
     * 语言包基础目录
     *
     * @var string
     */
    protected $basePath;

    /**
     * 语言别名
     *
     * @var array
     */
    protected $langs = [
        'zh-cn' => ['zh', 'cn', 'zh-cn', 'zh_cn'],
        'en-us' => ['en','us', 'en-us', 'en_us'],
    ];

    /**
     * 实例化container
     *
     * <pre>
     * new Translator($diContainer, APP_PATH . '/i18n/');
     * </pre>
     *
     * @param PhalconDi $app
     * @param string    $languageFileBasePath， ex: APP_PATH . '/i18n'
     */
    public function __construct(\Phalcon\Di $app, $languageFileBasePath)
    {
        if (!stream_resolve_include_path($languageFileBasePath)
            || !is_dir($languageFileBasePath)) {
            throw new Exception("语言包目录 '$languageFileBasePath' 不存在或不可读。");
        }

        $this->app = $app;
        $this->basePath = rtrim($languageFileBasePath, '/') . '/';
        $this->setDefaultLocale();
    }

    /**
     * 设置语言
     *
     * @param string $locale 语言，ex:zh-Hant-TW、 zh-CN、en-US
     *
     * @return  $this
     */
    public function setLocale($locale)
    {
        foreach ($this->langs as $lang => $alias) {
            if ($locale == $lang || array_search($locale, $alias)) {
                $locale = $lang;
            }
        }

        $this->locale = strtolower($locale);
        $this->patterns[$locale] = $this->loadPatterns($locale);

        return $this;
    }


    /**
     * 格式化
     *
     * @param string $pattern
     * @param array  $data
     *
     * @return string
     */
    public function format($pattern, $data = [])
    {
        is_array($data) || $data = array($data);

        $keys = array_map(function($key){
            return "{{$key}}";
        }, array_keys($data));

        return str_replace($keys, $data, $pattern);
    }

    /**
     * 获取语言
     *
     * @param string $name
     * @param array  $data
     *
     * @return string|array
     */
    public function trans($name, $data = [])
    {
        if (is_array($name)) {
            $strings = [];
            foreach ($name as $key => $data) {
                $strings[$key] = $this->getLine($key, $data);
            }

            return $string;
        }

        return $this->getLine($name, (array) $data);
    }

    /**
     * 获取一条语句的翻译结果
     *
     * @param string $key
     * @param array  $data
     * @param string $locale
     *
     * @return string
     */
    public function getLine($key, $data = [], $locale = '')
    {
        $pattern = $this->getPattern($key, $locale);
        if (empty($pattern)) {
            return $key;
        }

        return empty($data) ? $pattern : $this->format($pattern, (array) $data);
    }

    /**
     * 设置默认语言
     */
    protected function setDefaultLocale()
    {
        $locale = $this->app->get('request')->getBestLanguage();

        return $this->setLocale(empty($locale) ? 'zh-cn' : strtolower($locale));
    }

    /**
     * 获取语言配置项中原始key
     *
     * @param string $name
     * @param string $locale
     *
     * @return string
     */
    protected function getPattern($name, $locale = '')
    {
        $locale = $locale ? : $this->locale;
        $locale = strtolower($locale);

        if (!$this->localeIsLoaded($locale)) {
            $this->patterns[$locale] = $this->loadPatterns($locale);
        }

        return array_get($this->patterns[$locale], $name);
    }

    /**
     * 是否加载过对应语言的语言包
     *
     * @param string $locale
     *
     * @return boolean
     */
    protected function localeIsLoaded($locale)
    {
        return isset($this->patterns[$locale]);
    }

    /**
     * 读取指定语言的语言包
     *
     * @param string $locale
     *
     * @return array|boolean
     */
    protected function loadPatterns($locale)
    {
        return (array) include $this->basePath . $locale . '/all.php';
    }

}