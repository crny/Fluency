<?php
/**
 * App.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:46]
 */
namespace Clear\Facades;

/**
 * @see \Engine\Application
 */
class App extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'app'; }

}