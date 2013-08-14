<?php
require_once('tests/app.php');

use Jenssegers\MongodbLite\Facades\DB;

class ModelTest extends PHPUnit_Framework_TestCase {

	public function setUp() {}

	public function tearDown()
	{
		User::drop();
	}

	public function testInsert()
	{
		$status = User::insert(array('name' => 'John Doe'));
		$this->assertEquals(1, $status['ok']);
	}

	public function testCount()
	{
		$status = User::insert(array('name' => 'John Doe'));
		$this->assertEquals(1, User::count());
	}

	public function testTruncate()
	{
		User::insert(array('name' => 'John Doe'));
		$this->assertEquals(1, User::count());
		User::drop();
		$this->assertEquals(0, User::count());
	}

	public function testFind()
	{
		User::insert(array('name' => 'John Doe'));
		User::insert(array('name' => 'Jane Doe'));

		$users = User::find();
		$this->assertInstanceOf('Illuminate\Support\Collection', $users);

		$user = $users->first();
		$this->assertInstanceOf('User', $user);
		$this->assertEquals('John Doe', $user->name);
		$this->assertInstanceOf('MongoID', $user->_id);
	}

	public function testFindOne()
	{
		User::insert(array('name' => 'John Doe'));
		$user = User::findOne(array('name' => 'John Doe'));
		$this->assertInstanceOf('User', $user);
		$this->assertEquals('John Doe', $user->name);
		$this->assertInstanceOf('MongoID', $user->_id);
	}

	public function testAccessor()
	{
		User::insert(array('name' => 'John Doe'));
		$user = User::findOne(array('name' => 'John Doe'));

		$this->assertEquals(md5('John Doe'), $user->test);
	}

	public function testSave()
	{
		$user = new User;
		$user->name = 'John Doe';
		$this->assertFalse($user->exists);
		$this->assertEquals(0, User::count());
		$this->assertEquals(null, $user->_id);

		$user->save();
		$this->assertTrue($user->exists);
		$this->assertEquals(1, User::count());
		$this->assertEquals('John Doe', $user->name);
		$this->assertInstanceOf('MongoID', $user->_id);

		$test = User::findOne(array('name' => 'John Doe'));
		$this->assertEquals($test, $user);

		// backup id
		$id = $user->_id;

		$user->name = 'Jane Doe';
		$user->save();
		$this->assertEquals('Jane Doe', $user->name);
		$this->assertEquals($id, $user->_id);
		$this->assertNotEquals($test, $user);
	}

}