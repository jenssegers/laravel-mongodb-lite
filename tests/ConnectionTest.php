<?php
require_once('tests/app.php');

use Illuminate\Support\Facades\DB;

class ConnectionTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->connection = DB::connection();
	}

	public function tearDown() {}

	public function testConstruct()
	{
		$this->assertInstanceOf('Jenssegers\Mongodb\Lite\Connection', DB::connection());
	}

	public function testCollection()
	{
		$this->assertInstanceOf('MongoCollection', DB::collection('test'));
	}

	public function testDb()
	{
		$connection = DB::connection();
		$this->assertInstanceOf('MongoDB', $connection->getMongoDB());
	}

	public function testClient()
	{
		$connection = DB::connection();
		$this->assertInstanceOf('MongoClient', $connection->getMongoClient());
	}

	public function testGet()
	{
		$collection = DB::connection()->test;
		$this->assertInstanceOf('MongoCollection', $collection);
	}

	public function testDynamic()
	{
		$collections = DB::connection()->getCollectionNames();
		$this->assertTrue(is_array($collections));
	}

}