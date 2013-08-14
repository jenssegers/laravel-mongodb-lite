<?php namespace Jenssegers\MongodbLite;

interface ConnectionResolverInterface {

	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return Connection
	 */
	public function connection($name = null);

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection();

	/**
	 * Set the default connection name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultConnection($name);

}