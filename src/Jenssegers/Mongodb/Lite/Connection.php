<?php namespace Jenssegers\Mongodb\Lite;

use MongoClient;

class Connection extends \Illuminate\Database\Connection {

    /**
     * The MongoClient object.
     *
     * @var resource
     */
    protected $connection;

    /**
     * The MongoDB object.
     *
     * @var resource
     */
    protected $db;

    /**
     * Create a new database connection instance.
     *
     * @param  array   $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        // Build the connection string
        $dsn = $this->getDsn($config);

        // You can pass options directly to the MogoClient constructor
        $options = array_get($config, 'options', array());

        // Create the connection
        $this->connection = $this->createConnection($dsn, $config, $options);

        // Select database
        $this->db = $this->connection->{$config['database']};
    }

     /**
     * Begin a fluent query against a database collection.
     *
     * @param  string  $collection
     * @return MongoCollection
     */
    public function collection($collection)
    {
        return $this->db->{$collection};
    }

    /**
     * Begin a fluent query against a database collection.
     *
     * @param  string  $table
     * @return MongoCollection
     */
    public function table($table)
    {
        return $this->collection($table);
    }

    /**
     * Get the MongoDB database object.
     *
     * @return MongoDB
     */
    public function getMongoDB()
    {
        return $this->db;
    }

    /**
     * return MongoClient object
     *
     * @return MongoClient
     */
    public function getMongoClient()
    {
        return $this->connection;
    }

    /**
     * Create a new MongoClient connection.
     *
     * @param  string  $dsn
     * @param  array   $config
     * @param  array   $options
     * @return MongoClient
     */
    protected function createConnection($dsn, array $config, array $options)
    {
        // Add credentials as options, this makes sure the connection will not fail if
        // the username or password contains strange characters.
        if (isset($config['username']) && $config['username'])
        {
            $options['username'] = $config['username'];
        }

        if (isset($config['password']) && $config['password'])
        {
            $options['password'] = $config['password'];
        }

        return new MongoClient($dsn, $options);
    }

    /**
     * Create a DSN string from a configuration.
     *
     * @param  array   $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        // First we will create the basic DSN setup as well as the port if it is in
        // in the configuration options. This will give us the basic DSN we will
        // need to establish the MongoClient and return them back for use.
        extract($config);

        // Treat host option as array of hosts
        $hosts = is_array($config['host']) ? $config['host'] : array($config['host']);

        // Add ports to hosts
        foreach ($hosts as &$host)
        {
            if (isset($config['port']))
            {
                $host = "{$host}:{$port}";
            }
        }

        // The database name needs to be in the connection string, otherwise it will
        // authenticate to the admin database, which may result in permission errors.
        return "mongodb://" . implode(',', $hosts) . "/{$database}";
    }

    /**
     * Dynamically get collections.
     *
     * @param  string  $name
     * @return Collection
     */
    public function __get($name)
    {
        return $this->collection($name);
    }

    /**
     * Dynamically pass methods to the internal MongoDB object.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->db, $method), $parameters);
    }

}
