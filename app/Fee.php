<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'fees';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id' , 'payment_id' , 'amount' , 'currency' , 'description' , 'type' , 
	];
   
    
}
