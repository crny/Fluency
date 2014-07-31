<?php
/**
 * View.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:49]
 */
namespace Engine\Facades;

/**
 * @see \Phalcon\Mvc\View
 */
class View extends Facade {

    protected static $methodAlias = [
        'make'    => 'pick',
    ];

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'view'; }

}