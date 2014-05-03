<?php

class ConnectionTest extends TestCase {

	public function tearDown()
	{
		DB::collection('test')->drop();
	}

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

}
