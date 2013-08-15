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
     * The database connection configuration options.
     *
     * @var array
     */
    protected $config = array();

    /**
     * Create a new database connection instance.
     *
     * @param  array   $config
     * @return void
     */
    public function __construct(array $config)
    {
        // Store configuration
        $this->config = $config;

        // Check for connection options
        $options = array_get($config, 'options', array());

        // Create connection
        $this->connection = new MongoClient($this->getDsn($config), $options);

        // Select database
        $this->db = $this->connection->{$config['database']};
    }

    /**
     * Return a new Collection
     *
     * @param  string  $collection
     * @return Collection
     */
    public function collection($collection)
    {
        return $this->db->{$collection};
    }

    /**
     * Get the MongoDB database object.
     *
     * @return  MongoDB
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

        // Credentials
        if (isset($config['username']) and isset($config['password']))
        {
            $credentials = "{$username}:{$password}@";
        }
        else
        {
            $credentials = '';
        }

        return "mongodb://{$credentials}" . implode(',', $hosts) . "/{$database}";
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