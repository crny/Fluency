<?php
/**
 * Lang.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:48]
 */
namespace Clear\Facades;

/**
 * @see \Clear\Lang\Translator
 */
class Lang extends Facade 
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'lang'; }

}