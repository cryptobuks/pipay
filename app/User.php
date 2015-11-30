<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use McCool\LaravelAutoPresenter\HasPresenter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class User extends Model implements HasPresenter
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'email', 'password'];

    
}
