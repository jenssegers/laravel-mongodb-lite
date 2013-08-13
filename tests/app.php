<?php
$loader = require 'vendor/autoload.php';
$loader->add('', 'tests/models');

use Jenssegers\MongodbLite\DatabaseManager;
use Jenssegers\MongodbLite\Model;
use Jenssegers\MongodbLite\Facades\DB;

# Fake app
$app = array();

# Database configuration
$app['config']['database.connections']['mongodb'] = array(
	'name'	   => 'mongodb',
	'host'     => 'localhost',
	'database' => 'unittest'
);

# Register service
$app['mongodblite'] = new DatabaseManager($app);

# Static setup
Model::setConnectionResolver($app['mongodblite']);
DB::setFacadeApplication($app);