<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'products';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id' , 'token' , 'item_desc' , 'order_id' , 'amount' , 'currency' , 'usage' , 'settlement_currency' , 'email' , 'redirect' , 'callback' , 'ipn' ,
		'customer_email' , 'customer_name' , 'customer_phone' , 'customer_address' , 'customer_custom' , 
	];
   
    
}
