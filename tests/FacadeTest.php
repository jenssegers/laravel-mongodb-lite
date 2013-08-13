<?php
require_once('tests/app.php');

use Jenssegers\MongodbLite\Facades\DB;

class FacadeTest extends PHPUnit_Framework_TestCase {

	private $manager;

	public function setUp() {}

	public function tearDown() {}

	public function testCollection()
	{
		$collection = DB::collection('test');
		$this->assertInstanceOf('MongoCollection', $collection);
	}

}