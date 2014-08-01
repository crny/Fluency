<?php
/**
 * Facade.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:47]
 */
namespace Clear\Facades;

/**
 * facades抽象类
 */
abstract class Facade {

    /**
     * DI容器
     *
     * @var \Phalcon\DI
     */
    protected static $container;

    /**
     * 方法别名
     *
     * <pre>
     * 在子类中使用：
     * protected static $methodAlias = [
     *   //别名 => 原名
     *   'filter' => 'sanitize',
     * ];
     * </pre>
     *
     * @var array
     */
    protected static $methodAlias = [];

    /**
     * 获取已经实例化的服务对象
     *
     * @var array
     */
    protected static $resolvedInstance;

    /**
     * 获取注入到DI窗口中的服务实例
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * 获取已经实例化的对象
     *
     * @return array
     */
    public static function getResolvedInstance()
    {
        return self::$resolvedInstance;
    }

    /**
     * 获取注入到DI容器中的服务名称
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new \RuntimeException("Facade does not implement getFacadeAccessor method.");
    }

    /**
     * 获取注入到DI窗口中的服务实例对象
     *
     * @param  string  $name
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) return $name;

        if (isset(static::$resolvedInstance[$name]))
        {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::$container[$name];
    }

    /**
     * 清除指定的服务实例
     *
     * @param  string  $name
     * @return void
     */
    public static function clearResolvedInstance($name)
    {
        unset(static::$resolvedInstance[$name]);
    }

    /**
     * 清除所有服务实例
     *
     * @return void
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = array();
    }

    /**
     * 获取DI容器实例
     *
     * @return \Phalcon\DI
     */
    public static function getFacadeApplication()
    {
        return static::$container;
    }

    /**
     * 设置DI容器实例
     *
     * @param  \Phalcon\DI  $container
     * @return void
     */
    public static function setFacadeApplication($container)
    {
        static::$container = $container;
    }

    /**
     * 动态转到静态
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return forward_static_call_array('self::__callStatic', array($method, $args));
    }

    /**
     * 处理静态方法
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (isset(static::$methodAlias[$method])) {
            $method = static::$methodAlias[$method];
        }

        switch (count($args))
        {
            case 0:
                return $instance->$method();

            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }

}