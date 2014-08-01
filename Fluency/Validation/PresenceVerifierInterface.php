<?php 
/**
 * PresenceVerifierInterface.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:52]
 */
namespace Engine\Services\Validation;

interface PresenceVerifierInterface {

	/**
	 * Count the number of objects in a collection having the given value.
	 *
	 * @param  string  $collection
	 * @param  string  $column
	 * @param  string  $value
	 * @param  int     $excludeId
	 * @param  string  $idColumn
	 * @param  array   $extra
	 * @return int
	 */
	public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = array());

	/**
	 * Count the number of objects in a collection with the given values.
	 *
	 * @param  string  $collection
	 * @param  string  $column
	 * @param  array   $values
	 * @param  array   $extra
	 * @return int
	 */
	public function getMultiCount($collection, $column, array $values, array $extra = array());

}
