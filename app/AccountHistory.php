<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class AccountHistory extends Model   {


	/**
	 * The connection name for the model.
	 *
	 * @var string
	 */
	protected $connection = 'pi';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'account_histories';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id', 'account_id', 'currency_id', 'balance', 'locked', 'fee', 'amount', 'fun', 'reason', 'modifiable_id', 'modifiable_type' 
	];

	public function account()
	{
		return $this->belongsTo('App\Oaccount');
	}

	public function user()
	{
		return $this->belongsTo('App\Ouser');
	}

	public function currency()
	{
		return $this->belongsTo('App\Currency');
	}


	public function modifiable()
	{
		return $this->morphTo();
	}

}
