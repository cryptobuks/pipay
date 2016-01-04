<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PiTransaction extends Model
{
 	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pi_transactions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'currency', 'txid', 'amount', 'confirmations', 'address', 'txout', 'state' , 'received_at' , 
	];

}
