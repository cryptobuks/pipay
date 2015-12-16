<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'transactions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id', 'account_id' , 'currency', 'fee', 'fee_id' , 'amount' , 'net' , 'source_id' , 'source_type', 'status', 'type' , 'url' , 'description' ,
	];

}
