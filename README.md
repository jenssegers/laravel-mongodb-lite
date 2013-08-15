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

Add the package to your `composer.json` and run `composer update`.

    {
        "require": {
            "jenssegers/mongodb-lite": "*"
        }
    }

Add the service provider in `app/config/app.php`:

    'Jenssegers\Mongodb\Lite\MongodbServiceProvider',

Configuration
-------------

Change your default database connection name in `app/config/database.php`:

    'default' => 'mongodb',

And add a new mongodb connection:

    'mongodb' => array(
        'driver'   => 'mongodb',
        'host'     => 'localhost',
        'port'     => 27017,
        'username' => 'username',
        'password' => 'password',
        'database' => 'database'
    ),

Getting a Collection
--------------------

Once your configuration is in place you can access your collections like this:

	DB::collection('users');

This returns the MongoCollection object associated with the 'mongodb' connection item. If you want to use a different connection use:

	DB::connection('mongodb2')->collection('users');
	// A MongoCollection object

Because this returns the native MongoCollection object you can use all of the standard methods:

	$users = DB::collection('users')->find();
	$count = DB::collection('users')->count();
	$user = DB::collection('users')->findOne(array('name' => 'John Doe'));

Model
-----

A model offers a more flexible way to access a collection.

	use Jenssegers\MongodbLite\Model as Eloquent;

	class User extends Eloquent {

		protected $collection = 'users';

	}

You can use all standard MongoCollection operations here as well:

	$users = User::find();

### Collections

All multi-result sets returned by the model return an Laravel Collection object. This object implements the IteratorAggregate PHP interface so it can be iterated over like an array. However, this object also has a variety of other helpful methods for working with result sets. More information about this at http://laravel.com/docs/eloquent#collections

	$users = User::all();
	// Will return a collection of User objects

	// Do laravel-collection operations
	$user = $users->first();
	$count = $users->count();

### Accessors & Mutators

Because model objects are returned, you can still define custom methods and use accessors and mutators. More information about this at http://laravel.com/docs/eloquent#accessors-and-mutators

	use Jenssegers\MongodbLite\Model as Eloquent;

	class User extends Eloquent {

		protected $collection = 'users';

		// Accessor
		public function getAvatarAttribute()
		{
			$hash = md5($this->attributes['email']);
			return "http://www.gravatar.com/avatar/$hash";
		}

		// Mutator
		public function setPasswordAttribute($value)
		{
			$this->attributes['password'] = crypt($value);
		}
	}

Which can then be used like this:

	$user = User::findOne(array('_id' => new MongoId('47cc67093475061e3d9536d2')));
	echo $user->avatar;

### Insert, Update, Delete

To create a new record in the database from a model, simply create a new model instance and call the save method.

	$user = new User;
	$user->name = 'John Doe';
	$user->save();

To update a model, you may retrieve it, change an attribute, and use the save method:

	$user->age = 35;
	$user->save();

You can create a new model using:

	$user = User::create(array('name' => 'John'));

To delete a model, simply call the delete method on the instance:

	$user->delete();

### Converting To Arrays / JSON

You can convert a model or a collection of models to array or json just like in Eloquent: 

	$user->toArray();
	$user->toJson();