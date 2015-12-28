<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class Legder extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'legders';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id', 'account_id', 'currency', 'balance', 'locked', 'fee', 'amount', 'fun', 'reason', 'modifiable_id', 'modifiable_type' 
	];

	public function account()
	{
		return $this->belongsTo('App\Account');
	}

	public function userProfile()
	{
		return $this->belongsTo('App\UserProfile');
	}

	public function modifiable()
	{
		return $this->morphTo();
	}


}
