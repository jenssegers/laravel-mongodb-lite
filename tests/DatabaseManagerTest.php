<?php
require_once('tests/app.php');

use Jenssegers\MongodbLite\DatabaseManager;

class DatabaseManagerTest extends PHPUnit_Framework_TestCase {

	private $manager;

	public function setUp()
	{
		global $app;
		$this->manager = new DatabaseManager($app);
	}

	public function tearDown() {}

	public function testConstruct()
	{
		$this->assertInstanceOf('Jenssegers\MongodbLite\DatabaseManager', $this->manager);
	}

	public function testConnection()
	{
		$connection = $this->manager->connection();
		$this->assertInstanceOf('Jenssegers\MongodbLite\Connection', $connection);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testUnknownConnection()
	{
		$connection = $this->manager->connection('fake');
	}

	public function testDefaultConnection()
	{
		$default = $this->manager->getDefaultConnection();

		$connection1 = $this->manager->connection();
		$connection2 = $this->manager->connection($default);

		$this->assertEquals($connection1, $connection2);
	}

}