<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'transfers';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id' , 'token' , 'status' , 'type' , 'amount' , 'amount_reversed' , 'currency' , 'description' , 'destination_type' , 'destination_id' , 'livemode' , 
	];
   
}
