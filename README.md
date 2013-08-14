Laravel MongoDB Lite [![Build Status](https://travis-ci.org/jenssegers/Laravel-MongoDB-Lite.png?branch=master)](https://travis-ci.org/jenssegers/Laravel-MongoDB-Lite)
====================

The lite version of https://github.com/jenssegers/Laravel-MongoDB

The difference between the full version and this lite version is as following:

 - [x] Database manager with configurable connections
 - [x] Basic model class with accessors and mutators
 - [x] Use original MongoCollection operations
 - [ ] Advanced Eloquent operations like relations
 - [ ] Query builder support

This lite version returns the native PHP MongoDB collection object instead of hiding them behind the query builder. This can be useful for people who want to do advanced MongoDB stuff using the original methods.

It also ships with a basic Eloquent-like model for extra flexibility. More information about this model can be found below.

Installation
------------

Add the package to your `composer.json` or install manually.

    {
        "require": {
            "jenssegers/mongodb-lite": "*"
        }
    }

Run `composer update` to download and install the package.

Add the service provider in `app/config/app.php`:

    'Jenssegers\Mongodb\MongodbServiceProvider',

Add an alias for the database manager, you can change this alias to your own preference:

    'MDB'            => 'Jenssegers\MongodbLite\Facades\DB',

Configuration
-------------

This package will automatically check the database configuration in `app/config/database.php` for a 'mongodb' item.

    'mongodb' => array(
        'host'     => 'localhost',
        'port'     => 27017,
        'username' => 'username',
        'password' => 'password',
        'database' => 'database'
    ),

Getting a Collection
--------------------

Once your configuration is in place you can access your collections like this:

	MDB::collection('users');

This returns the MongoCollection object associated with the 'mongodb' connection item. If you want to use a different connection use:

	MDB::connection('mongodb2')->collection('users');
	// A MongoCollection object

Because this returns the native MongoCollection object you can use all of the standard methods:

	$users = MDB::collection('users')->find();
	$count = MDB::collection('users')->count();
	$user = MDB::collection('users')->findOne(array('name' => 'John Doe'));

Model
-----

The model is a more flexible way to access the collection object. You can use all standard MongoCollection operations here as well.

	use Jenssegers\MongodbLite\Model as Eloquent;

	class User extends Eloquent {

		protected $collection = 'users';

	}

Results returned when using a model class will be wrapped in a laravel-collection of model objects.

	$users = User::all();
	// Will return a collection of User objects

	// Do laravel-collection operations
	$user = $users->first();
	$count = $users->count();

Because model objects are returned, you can still define custom methods and use things like accessors and mutators.

	use Jenssegers\MongodbLite\Model as Eloquent;

	class User extends Eloquent {

		protected $collection = 'users';

		public function getAvatarAttribute()
		{
			$hash = md5($this->attributes['email']);
			return "http://www.gravatar.com/avatar/$hash";
		}
	}

Which can then be used like this:

	$user = User::findOne(array('_id' => new MongoId('47cc67093475061e3d9536d2')));
	echo $user->avatar;
