<?php
/**
 * Config.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:46]
 */
namespace Engine\Facades;

/**
 * @see \Engine\Config
 */
class Config extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'config'; }

}