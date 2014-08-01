<?php
/**
 * ValidationServiceProvider.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:52]
 */
namespace Fluency\Validation;

use \Fluency\ServiceProvider;
use \Fluency\Lang\Translator;

class ValidationServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//$this->registerPresenceVerifier();

		$this->container->setShared('validator', function()
		{
			$container = $this->container;
			$validator = new Factory(new Lang($container), $container);

			// The validation presence verifier is responsible for determining the existence
			// of values in a given data collection, typically a relational database or
			// other persistent data stores. And it is used to check for uniqueness.
			// if ($container->has('validation.presence'))
			// {
			// 	$validator->setPresenceVerifier($container->get('validation.presence', $container));
			// }

			return $validator;
		});
	}

	// /**
	//  * Register the database presence verifier.
	//  *
	//  * @return void
	//  */
	// protected function registerPresenceVerifier()
	// {
	// 	$this->container->setShared('validation.presence', function()
	// 	{
	// 		$container = $this->container;
	// 		return new DatabasePresenceVerifier($container['dbRead']);
	// 	});
	// }

}