<?php

use Jenssegers\Mongodb\Lite\Model as Eloquent;

class User extends Eloquent {

	protected $collection = 'users';

	public function getTestAttribute($value)
	{
		return md5($this->attributes['name']);
	}

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = crypt($value);
	}

}