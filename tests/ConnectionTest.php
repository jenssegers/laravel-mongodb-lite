<?php

class ConnectionTest extends TestCase {

	public function testConstructs()
	{
		$this->assertInstanceOf('Jenssegers\Mongodb\Lite\Connection', DB::connection());
	}

	public function testGetsCollection()
	{
		$this->assertInstanceOf('MongoCollection', DB::collection('test'));
		$this->assertInstanceOf('MongoCollection', DB::table('test'));
	}

	public function testReconnects()
	{
		$c1 = DB::connection('mongodb');
		$c2 = DB::connection('mongodb');
		$this->assertEquals(spl_object_hash($c1), spl_object_hash($c2));

		$c1 = DB::connection('mongodb');
		$c2 = DB::reconnect('mongodb');
		$this->assertNotEquals(spl_object_hash($c1), spl_object_hash($c2));
	}

	public function testDb()
	{
		$this->assertInstanceOf('MongoDB', DB::getMongoDB());
	}

	public function testClient()
	{
		$this->assertInstanceOf('MongoClient', DB::getMongoClient());
	}

	public function testPassGetter()
	{
		$this->assertInstanceOf('MongoCollection', Db::connection()->test);
	}

	public function testPassCalls()
	{
		$this->assertTrue(is_array(DB::getCollectionNames()));
	}

	public function testAuth()
	{
		Config::set('database.connections.mongodb.username', 'foo');
		Config::set('database.connections.mongodb.password', 'bar');
		$host = Config::get('database.connections.mongodb.host');
		$port = Config::get('database.connections.mongodb.port', 27017);
		$database = Config::get('database.connections.mongodb.database');

		$this->setExpectedException('MongoConnectionException', "Failed to connect to: $host:$port: Authentication failed on database '$database' with username 'foo': auth fails");
		$connection = DB::connection('mongodb');
	}

	public function testCustomPort()
	{
		$port = 27000;
		Config::set('database.connections.mongodb.port', $port);
		$host = Config::get('database.connections.mongodb.host');
		$database = Config::get('database.connections.mongodb.database');

		$this->setExpectedException('MongoConnectionException', "Failed to connect to: $host:$port: Connection refused");
		$connection = DB::connection('mongodb');
	}

}
