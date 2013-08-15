<?php namespace Jenssegers\Mongodb\Lite;

use MongoCursor;
use Jenssegers\Model\Model as Eloquent;
use Illuminate\Support\Collection;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

class Model extends Eloquent {

	/**
	 * The connection name for the model.
	 *
	 * @var string
	 */
	protected $connection;

	/**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection;

	/**
	 * The collection resolver instance.
	 *
	 * @var ConnectionResolverInterface
	 */
	protected static $resolver;

	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		$collection = $this->getMongoCollection();
		$saved = $collection->save($this->attributes);

		$this->exists = true;

		return $saved;
	}

	/**
	 * Save a new model and return the instance.
	 *
	 * @param  array  $attributes
	 * @return Model|static
	 */
	public static function create(array $attributes)
	{
		$model = new static($attributes);

		$model->save();

		return $model;
	}

	/**
	 * Delete the model from the database.
	 *
	 * @return bool|null
	 */
	public function delete()
	{
		if ($this->exists)
		{
			// Remove just one item
			$collection = $this->getMongoCollection();
			$collection->remove(array('_id' => $this->attributes['_id']), array('justOne' => true));

			$this->exists = false;

			return true;
		}
	}

	/**
	 * Get the collection name associated with the model.
	 *
	 * @return string
	 */
	public function getCollection()
	{
		if (isset($this->collection)) return $this->collection;

		return str_replace('\\', '', snake_case(str_plural(class_basename($this))));
	}

	/**
	 * Get the MongoCollection for the model.
	 *
	 * @return Collection
	 */
	public function getMongoCollection()
	{
		// Get the collection from the resolver
		$connection = static::$resolver->connection($this->connection);

		// Return the collection
		return $connection->collection($this->getCollection());
	}

	/**
	 * Set the collection resolver instance.
	 *
	 * @param  ConnectionResolverInterface  $resolver
	 * @return void
	 */
	public static function setConnectionResolver(Resolver $resolver)
	{
		static::$resolver = $resolver;
	}

	/**
	 * Create a new Eloquent Collection from a MongoCursor.
	 *
	 * @param  MongoCursor  $cursor
	 * @return Illuminate\Support\Collection
	 */
	protected function newCollection(MongoCursor $cursor)
	{
		$models = array();

		foreach (iterator_to_array($cursor, false) as $attributes)
		{
			$models[] = $this->newInstance($attributes, true);
		}

		return new Collection($models);
	}

	/**
	 * Handle dynamic static method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		$instance = new static;

		// Pass static methods to the MongoCollection object
		$result = call_user_func_array(array($instance->getMongoCollection(), $method), $parameters);

		// If a MongoCursor is returned we will convert it to a collection of models
		if ($result instanceof MongoCursor)
		{
			return $instance->newCollection($result);
		}
		// If methods return a single item we will convert it to a single model object
		else if (in_array($method, array('findOne', 'findAndModify')))
		{
			return $instance->newInstance($result, true);
		}

		return $result;
	}

}