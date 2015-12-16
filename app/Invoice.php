<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invoices';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		'user_id' , 'token' , 'status' , 'exception_status' , 'api_key' , 'product_id' , 'product_token' ,
		 'amount' , 'amount_received' , 'pi_amount' , 'pi_amount_received' , 'rate' , 'currency' ,
		 'inbound_address' , 'refund_address' , 'livemode' , 'item_desc' , 'order_id' , 'reference' , 
		 'email' , 'expiration_at' , 'url' , 'payment_url' , 'customer_email' , 'customer_name' , 
		 'customer_phone' , 'customer_address' , 'customer_custom' ,
	];
   

}
