<?php namespace Jenssegers\MongodbLite;

use MongoCursor;
use Jenssegers\Model\Model as Eloquent;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Collection;

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
	 * @var \Illuminate\Database\ConnectionResolverInterface
	 */
	protected static $resolver;

	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		// If the model already exists in the database we can just update our record
		// that is already in this database using the current IDs in this "where"
		// clause to only update this model. Otherwise, we'll just insert them.
		if ($this->exists)
		{
			$collection = $this->getMongoCollection();
			$saved = $collection->update(array('_id', $this->attributes['_id']), $this->attributes);
		}

		// If the model is brand new, we'll insert it into our database and set the
		// ID attribute on the model to the value of the newly inserted row's ID
		// which is typically an auto-increment value managed by the database.
		else
		{
			$collection = $this->getMongoCollection();
			$saved = $collection->insert($this->attributes);

			// We will go ahead and set the exists property to true
			$this->exists = true;
		}

		return $saved;
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
	public static function setConnectionResolver(ConnectionResolverInterface $resolver)
	{
		static::$resolver = $resolver;
	}

	/**
	 * Create a new Eloquent Collection from a MongoCursor.
	 *
	 * @param  MongoCursor  $cursor
	 * @return Illuminate\Database\Eloquent\Collection
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
	 * Create a new instance of the given model.
	 *
	 * @param  array  $attributes
	 * @param  bool   $exists
	 * @return \Illuminate\Database\Eloquent\Model|static
	 */
	public function newInstance($attributes = array(), $exists = false)
	{
		// This method just provides a convenient way for us to generate fresh model
		// instances of this current model. It is particularly useful during the
		// hydration of new objects via the Eloquent query builder instances.
		$model = new static((array) $attributes);

		$model->exists = $exists;

		return $model;
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