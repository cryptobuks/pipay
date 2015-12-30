<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'payments';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id' , 'api_key' , 'buyer_id' , 'invoice_id' , 'amount' , 'amount_refunded' , 'fee' , 'fee_id' , 'currency' , 'livemode' , 'account_id' , 
	];
   
    
}
