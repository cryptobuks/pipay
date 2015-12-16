<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'refunds';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id' , 'payment_id' , 'amount' , 'pi_amount' , 'currency' , 'reason' ,
	];
   
    
}
