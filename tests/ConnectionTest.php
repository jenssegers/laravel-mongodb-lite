<?php
require_once('tests/app.php');

use Jenssegers\MongodbLite\DatabaseManager;

class ConnectionTest extends PHPUnit_Framework_TestCase {

	private $manager;
	private $connection;

	public function setUp()
	{
		global $app;
		$this->manager = new DatabaseManager($app);
		$this->connection = $this->manager->connection();
	}

	public function tearDown() {}

	public function testConstruct()
	{
		$this->assertInstanceOf('Jenssegers\MongodbLite\Connection', $this->connection);
	}

	public function testCollection()
	{
		$collection = $this->connection->collection('test');
		$this->assertInstanceOf('MongoCollection', $collection);
	}

	public function testDb()
	{
		$this->assertInstanceOf('MongoDB', $this->connection->getMongoDB());
	}

	public function testClient()
	{
		$this->assertInstanceOf('MongoClient', $this->connection->getMongoClient());
	}

	public function testGet()
	{
		$collection = $this->connection->test;
		$this->assertInstanceOf('MongoCollection', $collection);
	}

	public function testDynamic()
	{
		$collections = $this->connection->getCollectionNames();
		$this->assertTrue(is_array($collections));
	}

}