<?php namespace Jenssegers\MongodbLite\Facades;

use Illuminate\Support\Facades\Facade;

class DB extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'mongodblite';
	}

}