<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model {

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
	protected $table = 'currencies';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'code', 'name'
	];

	protected $visible = [ 'id', 'code', 'name'];

	/**
	 * The user validation rules.
	 *
	 * @var array
	 */
	public static $rules = [
	    'code'            => 'required',
	    'name'            => 'required',	    
	];

	public function accounts()
	{
		return $this->hasOne('App\Account' , 'currency_id' );
	}

	public function account_histories()
	{
		return $this->hasOne('App\AccountHistory' , 'currency_id' );
	}

	public function transaction()
	{
		return $this->hasOne('App\Transaction' , 'currency_id' );
	}


}
