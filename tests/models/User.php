<?php

use Jenssegers\MongodbLite\Model as Eloquent;

class User extends Eloquent {

	protected $collection = 'users';

	public function getTestAttribute($value)
	{
		return md5($this->attributes['name']);
	}

}