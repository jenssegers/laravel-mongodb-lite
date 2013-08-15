<?php
$loader = require 'vendor/autoload.php';
$loader->add('', 'tests/models');

use Jenssegers\Mongodb\Lite\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connectors\ConnectionFactory;

# Fake app class
class App extends ArrayObject {
	function bound() {}
}

# Fake app
$app = new App;

# Database configuration
$app['config']['database.fetch'] = null;
$app['config']['database.default'] = 'mongodb';
$app['config']['database.connections']['mongodb'] = array(
	'name'	   => 'mongodb',
	'driver'   => 'mongodb',
	'host'     => 'localhost',
	'database' => 'unittest'
);

# Initialize database manager
$app['db.factory'] = new ConnectionFactory(new Container);
$app['db'] = new DatabaseManager($app, $app['db.factory']);

# Extend database manager with reflection hack
$reflection = new ReflectionClass('Jenssegers\Mongodb\Lite\Connection');
$app['db']->extend('mongodb', array($reflection, 'newInstance'));

# Static setup
Model::setConnectionResolver($app['db']);
DB::setFacadeApplication($app);